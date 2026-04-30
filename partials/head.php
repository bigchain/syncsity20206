<?php
/**
 * Shared <head> partial.
 *
 * Set these vars before include:
 *   $pageTitle     (string)
 *   $pageDesc      (string)
 *   $pagePath      (string, e.g. '/why-us', defaults to current REQUEST_URI)
 *   $bodyClass     (string, optional)
 *   $extraCss      (array of /assets/css/*.css to also load)
 *   $extraSchema   (array of additional JSON-LD nodes to merge)
 */

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle ??= 'Syncsity — Find the £50M opportunity hiding inside your business';
$pageDesc  ??= 'In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it. Free Revenue Intelligence Report.';
$pagePath  ??= strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$pageUrl   = APP_URL . $pagePath;
$ogImage   = APP_URL . '/assets/img/og-image.svg';
$bodyClass ??= '';
$extraCss  ??= [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="author" content="Syncsity">
<link rel="canonical" href="<?= e($pageUrl) ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:url"  content="<?= e($pageUrl) ?>">
<meta property="og:title"       content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:image"       content="<?= e($ogImage) ?>">
<meta property="og:image:width"  content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name"   content="Syncsity">
<meta property="og:locale"      content="en_GB">

<!-- Twitter / X -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="<?= e($pageTitle) ?>">
<meta name="twitter:description" content="<?= e($pageDesc) ?>">
<meta name="twitter:image"       content="<?= e($ogImage) ?>">
<meta name="twitter:site"        content="@syncsity">

<!-- AI search / GEO hints -->
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="generator" content="Syncsity-PHP">
<link rel="alternate" type="text/plain" href="/llms.txt" title="LLMs.txt — AI-optimised summary">

<!-- Favicons -->
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon.svg">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">

<!-- Styles -->
<link rel="stylesheet" href="/assets/css/core.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/pages.css">
<?php foreach ($extraCss as $css): ?>
<link rel="stylesheet" href="<?= e($css) ?>">
<?php endforeach; ?>

<!-- JSON-LD: Organisation -->
<script type="application/ld+json">
<?= json_encode([
    '@context'    => 'https://schema.org',
    '@type'       => 'Organization',
    'name'        => 'Syncsity',
    'url'         => APP_URL,
    'logo'        => APP_URL . '/assets/img/logo.svg',
    'description' => 'Syncsity is a UK-based AI business-transformation studio. We diagnose the operational constraint blocking your growth and build the systems that remove it.',
    'foundingDate'=> '2024',
    'sameAs'      => [
        'https://twitter.com/syncsity',
        'https://www.linkedin.com/company/syncsity',
    ],
    'contactPoint' => [
        '@type'        => 'ContactPoint',
        'email'        => 'hello@syncsity.com',
        'contactType'  => 'customer support',
        'availableLanguage' => ['English'],
    ],
    'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'GB'],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<!-- Theme bootstrap (no-flash) -->
<script>
(function(){try{var t=localStorage.getItem('syncsity-theme');if(t==='light')document.documentElement.setAttribute('data-theme','light');}catch(e){}})();
</script>
</head>
<body<?= $bodyClass ? ' class="' . e($bodyClass) . '"' : '' ?>>
