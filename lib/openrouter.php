<?php
/**
 * Syncsity — OpenRouter LLM client
 *
 * Single function: llm_call($prompt, $system, $maxTokens, $expectJson)
 * Retries on rate limit / 5xx, falls back to OPENROUTER_FALLBACK on persistent
 * primary failure. Always uses temperature 0.2 for determinism in reports.
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

/**
 * @return array  Decoded JSON object, or ['_text' => string] when expectJson=false
 */
function llm_call(string $prompt, string $system = '', int $maxTokens = 4000, bool $expectJson = true, ?string $modelOverride = null): array
{
    $apiKey   = (string)env('OPENROUTER_API_KEY', '');
    $model    = $modelOverride ?: (string)env('OPENROUTER_MODEL', 'google/gemini-2.0-flash-001');
    $fallback = (string)env('OPENROUTER_FALLBACK', 'anthropic/claude-3.5-haiku');
    $referer  = (string)env('OPENROUTER_REFERER', APP_URL);
    $title    = (string)env('OPENROUTER_TITLE', APP_NAME);

    if ($apiKey === '') {
        throw new RuntimeException('OPENROUTER_API_KEY is not set');
    }

    $messages = [];
    if ($system !== '') $messages[] = ['role' => 'system', 'content' => $system];
    $messages[] = ['role' => 'user', 'content' => $prompt];

    $payload = [
        'model'       => $model,
        'messages'    => $messages,
        'temperature' => 0.2,
        'max_tokens'  => $maxTokens,
    ];
    if ($expectJson) {
        $payload['response_format'] = ['type' => 'json_object'];
    }

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'HTTP-Referer: ' . $referer,
        'X-Title: '     . $title,
    ];

    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

    // Primary attempt with retry/backoff
    $resp = _llm_curl_with_retry('https://openrouter.ai/api/v1/chat/completions', $body, $headers);

    // Fallback on persistent error
    if ($resp['code'] !== 200 && $fallback !== '' && $fallback !== $model) {
        error_log("[llm] primary {$model} returned {$resp['code']}, trying fallback {$fallback}");
        $payload['model'] = $fallback;
        $body2 = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $resp = _llm_curl_with_retry('https://openrouter.ai/api/v1/chat/completions', $body2, $headers);
    }

    if ($resp['code'] !== 200) {
        throw new RuntimeException("OpenRouter HTTP {$resp['code']}: " . mb_substr($resp['body'], 0, 400));
    }

    $decoded = json_decode($resp['body'], true);
    if (!isset($decoded['choices'][0]['message']['content'])) {
        throw new RuntimeException('Unexpected LLM response shape: ' . mb_substr($resp['body'], 0, 400));
    }
    $content = (string)$decoded['choices'][0]['message']['content'];

    if (!$expectJson) {
        return ['_text' => $content];
    }

    $parsed = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Try to extract JSON if the model wrapped it in markdown / chat
        if (preg_match('/\{[\s\S]*\}/', $content, $m)) {
            $parsed = json_decode($m[0], true);
        }
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('LLM returned invalid JSON: ' . mb_substr($content, 0, 400));
        }
    }
    return is_array($parsed) ? $parsed : ['value' => $parsed];
}

function _llm_curl_with_retry(string $url, string $body, array $headers, int $maxRetries = 1): array
{
    $lastCode = 0;
    $lastBody = '';
    for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
        if ($attempt > 0) {
            sleep($attempt * 4); // 4s, 8s, ...
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $resBody = curl_exec($ch);
        $code    = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err     = curl_error($ch);
        curl_close($ch);

        if (!$err && $code === 200) {
            return ['code' => 200, 'body' => (string)$resBody];
        }
        $lastCode = $code;
        $lastBody = (string)$resBody;
        $retryable = $err !== '' || in_array($code, [0, 408, 429, 500, 502, 503, 504], true);
        if (!$retryable) break;
        error_log("[llm] attempt {$attempt} failed (HTTP {$code}, curl: " . ($err ?: 'none') . ')');
    }
    return ['code' => $lastCode, 'body' => $lastBody];
}

/**
 * Load a prompt template from /prompts and substitute {{vars}}.
 */
function load_prompt(string $name, array $vars = []): string
{
    $name = preg_replace('/[^a-z0-9_\-]/', '', strtolower($name));
    $path = SYNC_ROOT . '/prompts/' . $name . '.txt';
    if (!file_exists($path)) {
        throw new RuntimeException("Prompt not found: {$name}");
    }
    $text = (string)file_get_contents($path);
    foreach ($vars as $k => $v) {
        $text = str_replace('{{' . $k . '}}', is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE), $text);
    }
    return $text;
}
