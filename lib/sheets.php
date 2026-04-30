<?php
/**
 * Syncsity — Google Sheets append (no Composer, no library)
 *
 * Usage:
 *   GoogleSheets::appendLeadRow(['email' => ..., 'company' => ..., ...]);
 *   GoogleSheets::appendReportRow([...]);
 *
 * Service-account JSON path comes from .env (GOOGLE_SERVICE_ACCOUNT_JSON).
 * Token cache is kept under /storage/cache.
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/jwt.php';

final class GoogleSheets
{
    private const SCOPE = 'https://www.googleapis.com/auth/spreadsheets';

    /** Append a row of arbitrary values to a sheet. */
    public static function appendRow(string $sheetId, array $row, string $range = 'Sheet1!A1'): void
    {
        $jsonPath = (string)env('GOOGLE_SERVICE_ACCOUNT_JSON', '');
        if ($jsonPath === '' || !is_file($jsonPath)) {
            throw new RuntimeException("Google service-account JSON not found");
        }
        $sa = json_decode((string)file_get_contents($jsonPath), true);
        if (!is_array($sa) || empty($sa['client_email']) || empty($sa['private_key'])) {
            throw new RuntimeException("Google service-account JSON malformed");
        }

        $cacheDir  = SYNC_ROOT . '/storage/cache';
        if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0700, true); }
        $cachePath = $cacheDir . '/google_token.json';

        $token = GoogleJwt::getAccessToken($sa, self::SCOPE, $cachePath);

        $url = sprintf(
            'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS',
            rawurlencode($sheetId),
            rawurlencode($range)
        );

        $payload = json_encode(['values' => [array_values($row)]], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen((string)$payload),
            ],
        ]);
        $response = curl_exec($ch);
        $http     = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($response === false) throw new RuntimeException("Sheets append curl error: $err");
        if ($http < 200 || $http >= 300) {
            throw new RuntimeException("Sheets append HTTP $http: " . mb_substr((string)$response, 0, 400));
        }
    }

    /**
     * Append a lead row (assessment submitted). Best-effort: errors are logged
     * not surfaced — the lead is already in MySQL, the sheet is a convenience.
     */
    public static function appendLeadRow(array $data): void
    {
        try {
            $sheetId = (string)env('GOOGLE_SHEETS_LEADS_ID', '');
            if ($sheetId === '') return;
            $range = (string)env('GOOGLE_SHEETS_LEADS_RANGE', 'Leads!A1');

            self::appendRow($sheetId, [
                date('Y-m-d H:i:s'),
                $data['email']            ?? '',
                $data['name']             ?? '',
                $data['company']          ?? '',
                $data['role']             ?? '',
                $data['website']          ?? '',
                $data['industry']         ?? '',
                $data['team_size']        ?? '',
                $data['revenue_band']     ?? '',
                $data['biggest_frustration'] ?? '',
                $data['stated_problem']   ?? '',
                $data['real_problem']     ?? '',
                $data['monthly_inquiries']?? '',
                $data['conversion_rate']  ?? '',
                $data['avg_deal_size']    ?? '',
                $data['cant_handle_more'] ?? '',
                $data['already_tried']    ?? '',
                $data['hidden_block']     ?? '',
                $data['vision_12_months'] ?? '',
                $data['country']          ?? '',
                $data['region']           ?? '',
                $data['source']           ?? '',
                $data['assessment_id']    ?? '',
                $data['report_url']       ?? '',
            ], $range);
        } catch (Throwable $e) {
            error_log('[Sheets:appendLeadRow] ' . $e->getMessage());
        }
    }
}
