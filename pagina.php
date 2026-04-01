<?php
/**
 * pagina.php
 * Renderiza páginas do CMS (cms_paginas)
 * Rota: /politica-de-privacidade, /termos-de-uso, etc.
 */
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$slug = $_GET['slug'] ?? trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$stmt = db()->prepare(
    'SELECT * FROM cms_paginas WHERE pag_slug = ? LIMIT 1'
);
$stmt->execute([$slug]);
$pagina = $stmt->fetch();

if (!$pagina) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$seo_title = $pagina['pag_titulo'] ?? 'Página';
$seo_desc  = $pagina['pag_description'] ?? '';

register_pageview($slug, $pagina['pag_titulo']);
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <h1 class="display-3" style="margin-bottom:8px"><?= h($pagina['pag_titulo']) ?></h1>
    <?php if ($pagina['pag_alteracao']): ?>
    <p style="font-size:.82rem;color:var(--text-3)">
      Última atualização: <?= date('d/m/Y', strtotime($pagina['pag_alteracao'])) ?>
    </p>
    <?php endif; ?>
  </div>
</div>

<section>
  <div class="container-s">
    <div class="post-content" data-reveal>
      <?= $pagina['pag_texto'] ?>
    </div>
    <div style="margin-top:48px;padding-top:32px;border-top:1px solid var(--border)">
      <a href="<?= BASE_URL ?>/" class="btn btn-outline btn-sm">← Voltar ao início</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
