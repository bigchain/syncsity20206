<?php
/**
 * Syncsity — Tight markdown renderer for AI-generated report sections.
 *
 * Security model: HTML-escape ALL input first. Then promote a small, fixed
 * subset of markdown into HTML by regex. The LLM cannot inject raw HTML
 * because every < > & is already encoded before any promotion.
 *
 * Supported:
 *   ## / ### headings
 *   **bold**, *italic*
 *   `inline code`
 *   - / * unordered lists
 *   1. / 2. ordered lists
 *   > blockquote
 *   [text](url)   — only http(s):// or relative URLs
 *   | tables |
 *   paragraphs (blank-line separated)
 *   --- horizontal rule
 */

declare(strict_types=1);

function md_to_html(string $markdown): string
{
    // 1. Normalise line endings + trim trailing whitespace
    $md = str_replace(["\r\n", "\r"], "\n", $markdown);
    $md = preg_replace('/[ \t]+\n/', "\n", $md);

    // 2. Escape HTML up front
    $md = htmlspecialchars($md, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // 3. Split into blocks separated by blank lines
    $blocks = preg_split('/\n{2,}/', trim($md));
    $out = [];

    foreach ($blocks as $block) {
        $block = rtrim($block);
        if ($block === '') continue;

        // ── Horizontal rule
        if (preg_match('/^-{3,}$/', $block)) {
            $out[] = '<hr>';
            continue;
        }

        // ── Heading
        if (preg_match('/^(#{2,3})\s+(.+)$/m', $block, $m) && substr_count($block, "\n") === 0) {
            $level = strlen($m[1]);
            $out[] = "<h{$level}>" . md_inline($m[2]) . "</h{$level}>";
            continue;
        }

        // ── Blockquote (one or more lines starting with >)
        if (preg_match('/^>\s?/', $block)) {
            $lines = array_map(function ($l) { return preg_replace('/^>\s?/', '', $l); }, explode("\n", $block));
            $out[] = '<blockquote><p>' . md_inline(implode('<br>', $lines)) . '</p></blockquote>';
            continue;
        }

        // ── Table — | a | b |\n| - | - |\n| 1 | 2 |
        if (preg_match('/^\|.+\|\n\|\s*[-:|\s]+\|\n(?:\|.+\|\n?)+/', $block . "\n")) {
            $out[] = md_table($block);
            continue;
        }

        // ── Unordered list
        if (preg_match('/^(\s*)[-*]\s+/', $block)) {
            $out[] = md_list($block, false);
            continue;
        }

        // ── Ordered list
        if (preg_match('/^(\s*)\d+\.\s+/', $block)) {
            $out[] = md_list($block, true);
            continue;
        }

        // ── Paragraph (single newlines become <br>)
        $para = md_inline(str_replace("\n", '<br>', $block));
        $out[] = '<p>' . $para . '</p>';
    }

    return implode("\n", $out);
}

function md_inline(string $s): string
{
    // Inline code first (so other replacements don't touch its content)
    $s = preg_replace_callback('/`([^`]+)`/', function ($m) {
        return '<code>' . $m[1] . '</code>';
    }, $s);

    // Bold then italic
    $s = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $s);
    $s = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $s);

    // Links — only http(s) and relative paths. Anything else stays plain.
    $s = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', function ($m) {
        $label = $m[1];
        $url   = $m[2];
        // Already HTML-escaped, so check for href safety on the escaped form
        $isSafe = preg_match('#^(https?://|/)[^"\'<>]*$#i', $url);
        if (!$isSafe) return $label;
        $external = str_starts_with($url, 'http');
        $rel = $external ? ' rel="noopener noreferrer" target="_blank"' : '';
        return '<a href="' . $url . '"' . $rel . '>' . $label . '</a>';
    }, $s);

    return $s;
}

function md_list(string $block, bool $ordered): string
{
    $tag = $ordered ? 'ol' : 'ul';
    $lines = explode("\n", $block);
    $items = [];
    $current = null;

    foreach ($lines as $line) {
        if (preg_match('/^(\s*)(?:[-*]|\d+\.)\s+(.+)$/', $line, $m)) {
            if ($current !== null) $items[] = $current;
            $current = trim($m[2]);
        } elseif ($current !== null && trim($line) !== '') {
            $current .= ' ' . trim($line);
        }
    }
    if ($current !== null) $items[] = $current;

    $itemsHtml = array_map(function ($i) { return '<li>' . md_inline($i) . '</li>'; }, $items);
    return "<{$tag}>\n  " . implode("\n  ", $itemsHtml) . "\n</{$tag}>";
}

function md_table(string $block): string
{
    $rows = array_values(array_filter(array_map('trim', explode("\n", $block)), function ($r) { return $r !== ''; }));
    if (count($rows) < 2) return '<p>' . md_inline($block) . '</p>';

    $head     = explode('|', trim($rows[0], '|'));
    $bodyRows = array_slice($rows, 2); // skip the |---|---| separator

    $thead = '<thead><tr>';
    foreach ($head as $h) $thead .= '<th>' . md_inline(trim($h)) . '</th>';
    $thead .= '</tr></thead>';

    $tbody = '<tbody>';
    foreach ($bodyRows as $r) {
        $cells = explode('|', trim($r, '|'));
        $tbody .= '<tr>';
        foreach ($cells as $c) $tbody .= '<td>' . md_inline(trim($c)) . '</td>';
        $tbody .= '</tr>';
    }
    $tbody .= '</tbody>';

    return '<table class="report-table">' . $thead . $tbody . '</table>';
}
