<?php
/**
 * sitemap.php
 * Gera sitemap.xml dinâmico
 */
require __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$urls = [];

// ── Páginas estáticas ─────────────────────────────────────────
$static_pages = [
    ['loc' => '',           'priority' => '1.00', 'changefreq' => 'weekly'],
    ['loc' => '/sobre',     'priority' => '0.80', 'changefreq' => 'monthly'],
    ['loc' => '/solucoes',  'priority' => '0.90', 'changefreq' => 'monthly'],
    ['loc' => '/mentoria',  'priority' => '0.85', 'changefreq' => 'monthly'],
    ['loc' => '/blog',      'priority' => '0.80', 'changefreq' => 'daily'],
    ['loc' => '/contato',   'priority' => '0.75', 'changefreq' => 'monthly'],
];
foreach ($static_pages as $p) {
    $urls[] = array_merge($p, ['lastmod' => date('Y-m-d')]);
}

// ── Posts do blog ─────────────────────────────────────────────
$posts = db()->query(
    'SELECT p_slug, p_titulo, p_data, p_alteracao
     FROM posts WHERE p_ativo = 1 AND p_slug IS NOT NULL
     ORDER BY p_data DESC'
)->fetchAll();

foreach ($posts as $p) {
    $lastmod = $p['p_alteracao'] ?? $p['p_data'];
    $urls[] = [
        'loc'        => '/blog/' . $p['p_slug'],
        'lastmod'    => date('Y-m-d', strtotime($lastmod)),
        'changefreq' => 'monthly',
        'priority'   => '0.65',
    ];
}

// ── Páginas CMS ───────────────────────────────────────────────
$pages = db()->query(
    "SELECT pag_slug, pag_alteracao FROM cms_paginas
     WHERE pag_slug NOT IN ('---', '') AND pag_slug IS NOT NULL"
)->fetchAll();

foreach ($pages as $pg) {
    $urls[] = [
        'loc'        => '/' . $pg['pag_slug'],
        'lastmod'    => $pg['pag_alteracao'] ? date('Y-m-d', strtotime($pg['pag_alteracao'])) : date('Y-m-d'),
        'changefreq' => 'yearly',
        'priority'   => '0.40',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

foreach ($urls as $url) {
    echo '  <url>' . "\n";
    echo '    <loc>' . SITE_URL . htmlspecialchars($url['loc']) . '</loc>' . "\n";
    echo '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
    echo '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
    echo '    <priority>' . $url['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>';
