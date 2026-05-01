<?php
/**
 * Universal site <head> for marketing pages (the syncdemo design system).
 *
 * Set BEFORE including this file:
 *   $page_title        (string)  — required, e.g. "Pricing | Syncsity"
 *   $page_description  (string)  — required, ~140-160 chars
 *   $page_canonical    (string)  — required, absolute URL
 *   $page_og_image     (string)  — optional, default OG image
 *   $page_modified     (string)  — optional, ISO 8601, defaults to today
 *   $page_breadcrumb   (array)   — optional, [['Home','https://...'], ...]
 *   $page_extra_jsonld (string)  — optional, raw <script> blocks for FAQ/Service/etc.
 *   $page_path_prefix  (string)  — optional, '../' if not at site root, else ''
 *
 * Usage:
 *   <?php
 *   $page_title       = 'Pricing | Syncsity';
 *   $page_description = '...';
 *   $page_canonical   = 'https://syncsity.com/pricing';
 *   $page_breadcrumb  = [['Home','https://syncsity.com/'], ['Pricing','https://syncsity.com/pricing']];
 *   include __DIR__ . '/partials/site-head.php';
 *   ?>
 */

// Required — explicit defaults so static analysers don't complain and the page
// still renders something readable if a caller forgets to set them.
$page_title       = $page_title       ?? 'Syncsity';
$page_description = $page_description ?? 'Syncsity finds the operational constraints blocking your growth, removes the waste, and applies AI where it multiplies results.';
$page_canonical   = $page_canonical   ?? 'https://syncsity.com/';

// Optional with sensible defaults
$page_og_image     = $page_og_image     ?? 'https://syncsity.com/assets/img/og-image.svg';
$page_modified     = $page_modified     ?? gmdate('Y-m-d\TH:i:s\Z');
$page_breadcrumb   = $page_breadcrumb   ?? [['Home', 'https://syncsity.com/']];
$page_extra_jsonld = $page_extra_jsonld ?? '';
$page_path_prefix  = $page_path_prefix  ?? '/';

// Strip page title down to a clean OG title (drop the " | Syncsity" suffix if present)
$og_title = preg_replace('/\s*\|\s*Syncsity\s*$/', '', $page_title);

// Build BreadcrumbList JSON-LD
$breadcrumb_items = [];
foreach ($page_breadcrumb as $i => $crumb) {
    $breadcrumb_items[] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'name'     => $crumb[0],
        'item'     => $crumb[1],
    ];
}
$breadcrumb_json = json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => $breadcrumb_items,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$webpage_json = json_encode([
    '@context'           => 'https://schema.org',
    '@type'              => 'WebPage',
    '@id'                => $page_canonical . '#webpage',
    'url'                => $page_canonical,
    'name'               => $og_title,
    'description'        => $page_description,
    'isPartOf'           => ['@id' => 'https://syncsity.com/#website'],
    'about'              => ['@id' => 'https://syncsity.com/#organization'],
    'primaryImageOfPage' => $page_og_image,
    'dateModified'       => $page_modified,
    'inLanguage'         => 'en-GB',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
?><!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
<meta name="theme-color" content="#0a1022" />
<meta name="color-scheme" content="dark light" />

<title><?= $h($page_title) ?></title>
<meta name="description" content="<?= $h($page_description) ?>" />
<meta name="author" content="Syncsity" />
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
<link rel="canonical" href="<?= $h($page_canonical) ?>" />

<link rel="icon" type="image/svg+xml" href="<?= $h($page_path_prefix) ?>assets/img/favicon.svg" />
<link rel="icon" type="image/x-icon" href="<?= $h($page_path_prefix) ?>favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="<?= $h($page_path_prefix) ?>assets/img/favicon.svg" />

<meta property="og:type" content="website" />
<meta property="og:site_name" content="Syncsity" />
<meta property="og:url" content="<?= $h($page_canonical) ?>" />
<meta property="og:title" content="<?= $h($og_title) ?>" />
<meta property="og:description" content="<?= $h($page_description) ?>" />
<meta property="og:image" content="<?= $h($page_og_image) ?>" />
<meta property="og:image:alt" content="Syncsity — AI business transformation studio" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:locale" content="en_GB" />
<meta property="og:locale:alternate" content="en_US" />
<meta property="article:modified_time" content="<?= $h($page_modified) ?>" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@syncsity" />
<meta name="twitter:creator" content="@syncsity" />
<meta name="twitter:title" content="<?= $h($og_title) ?>" />
<meta name="twitter:description" content="<?= $h($page_description) ?>" />
<meta name="twitter:image" content="<?= $h($page_og_image) ?>" />

<link rel="preconnect" href="https://www.youtube.com" />
<link rel="preconnect" href="https://i.ytimg.com" />

<meta name="ai-content-declaration" content="human-authored" />
<link rel="alternate" type="text/plain" title="LLM-friendly summary" href="https://syncsity.com/llms.txt" />

<!-- JSON-LD: Organization (universal — site-wide identity) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://syncsity.com/#organization",
  "name": "Syncsity",
  "url": "https://syncsity.com",
  "logo": "https://syncsity.com/assets/img/logo.png",
  "image": "https://syncsity.com/assets/img/og-image.svg",
  "description": "AI business transformation studio. We find the operational constraints blocking growth, remove the waste, and apply AI where it multiplies results.",
  "foundingDate": "2018",
  "founder": { "@type": "Person", "name": "Edward Hadome" },
  "sameAs": [
    "https://twitter.com/syncsity",
    "https://www.linkedin.com/company/syncsity"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "contactType": "customer support",
    "email": "edward@syncsity.com",
    "areaServed": ["GB","US","AE","CA","AU"],
    "availableLanguage": ["English"]
  }
}
</script>

<!-- JSON-LD: WebSite (universal) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "@id": "https://syncsity.com/#website",
  "url": "https://syncsity.com",
  "name": "Syncsity",
  "publisher": { "@id": "https://syncsity.com/#organization" },
  "inLanguage": "en-GB"
}
</script>

<!-- JSON-LD: WebPage (page-specific) -->
<script type="application/ld+json"><?= $webpage_json ?></script>

<!-- JSON-LD: BreadcrumbList (page-specific) -->
<script type="application/ld+json"><?= $breadcrumb_json ?></script>

<?= $page_extra_jsonld ?>

<link rel="stylesheet" href="<?= $h($page_path_prefix) ?>assets/css/static-page.css" />
<link rel="stylesheet" href="<?= $h($page_path_prefix) ?>assets/css/site-nav.css" />
