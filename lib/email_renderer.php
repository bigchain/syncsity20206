<?php
/**
 * Syncsity — Email Template Renderer
 *
 * Loads emails/{template}.php with $vars in scope, captures HTML, derives plain text.
 * Each template assigns $subject before output.
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));

/**
 * @return array{subject: string, html: string, text: string}
 */
function render_email(string $template, array $vars = []): array
{
    $template = preg_replace('/[^a-z0-9_]/', '', strtolower($template));
    $path     = SYNC_ROOT . '/emails/' . $template . '.php';
    if (!file_exists($path)) {
        throw new RuntimeException("Email template not found: {$template}");
    }

    $subject = APP_NAME;
    ob_start();
    extract($vars, EXTR_SKIP);
    include $path;
    $html = (string)ob_get_clean();

    $text = preg_replace(['/<br\s*\/?>/i', '/<\/p>/i', '/<\/tr>/i', '/<\/li>/i'], "\n", $html);
    $text = trim(html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8'));
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);

    return ['subject' => $subject, 'html' => $html, 'text' => $text];
}
