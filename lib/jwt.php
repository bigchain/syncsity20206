<?php
/**
 * Syncsity — Google service-account JWT signer
 *
 * Pure-PHP RS256 JWT for the Google OAuth2 token exchange.
 * No Composer — uses openssl_sign() from PHP core.
 * Lifted from the Darren freshpaydayrelief.com pattern (proven in production).
 */

declare(strict_types=1);

final class GoogleJwt
{
    /**
     * Build & sign a JWT for a Google service account.
     */
    public static function sign(array $serviceAccount, string $scope): string
    {
        if (empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
            throw new RuntimeException('Service account JSON missing client_email or private_key');
        }

        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claim  = [
            'iss'   => $serviceAccount['client_email'],
            'scope' => $scope,
            'aud'   => 'https://oauth2.googleapis.com/token',
            'exp'   => $now + 3600,
            'iat'   => $now,
        ];

        $segments = [
            self::base64url(json_encode($header, JSON_UNESCAPED_SLASHES)),
            self::base64url(json_encode($claim,  JSON_UNESCAPED_SLASHES)),
        ];
        $signingInput = implode('.', $segments);

        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
        if ($privateKey === false) {
            throw new RuntimeException('Unable to load service account private key');
        }

        $signature = '';
        $ok = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw new RuntimeException('openssl_sign failed for service account JWT');
        }

        $segments[] = self::base64url($signature);
        return implode('.', $segments);
    }

    /**
     * Exchange JWT for an OAuth2 access token. Caches on disk for ~50 minutes.
     */
    public static function getAccessToken(array $serviceAccount, string $scope, string $cachePath): string
    {
        if (is_file($cachePath)) {
            $cached = json_decode((string)@file_get_contents($cachePath), true);
            if (is_array($cached) && !empty($cached['access_token']) && !empty($cached['expires_at'])) {
                if ((int)$cached['expires_at'] > time() + 60) {
                    return (string)$cached['access_token'];
                }
            }
        }

        $jwt = self::sign($serviceAccount, $scope);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $response = curl_exec($ch);
        $http     = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($response === false) throw new RuntimeException("Google token request failed: $err");
        if ($http !== 200) throw new RuntimeException("Google token request HTTP $http: $response");

        $data = json_decode((string)$response, true);
        if (!is_array($data) || empty($data['access_token'])) {
            throw new RuntimeException('Google token response missing access_token');
        }

        @file_put_contents($cachePath, json_encode([
            'access_token' => $data['access_token'],
            'expires_at'   => time() + (int)($data['expires_in'] ?? 3600) - 600,
        ]), LOCK_EX);
        @chmod($cachePath, 0600);

        return (string)$data['access_token'];
    }

    private static function base64url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
