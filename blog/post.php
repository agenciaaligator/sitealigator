<?php
require __DIR__ . '/../config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$slug_or_id = trim($_GET['slug'] ?? '');
if (!$slug_or_id) { redirect(BASE_URL . '/blog'); }

// Try exact slug match first, then numeric ID
$post = null;
$stmt = db()->prepare(
    'SELECT p.*, pc.pc_titulo as cat_titulo
     FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
     WHERE p.p_slug = ? AND p.p_ativo = 1 LIMIT 1'
);
$stmt->execute([$slug_or_id]);
$post = $stmt->fetch();

// Fallback: numeric ID
if (!$post && is_numeric($slug_or_id)) {
    $stmt2 = db()->prepare(
        'SELECT p.*, pc.pc_titulo as cat_titulo
         FROM posts p
         LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
         WHERE p.p_id = ? AND p.p_ativo = 1 LIMIT 1'
    );
    $stmt2->execute([(int)$slug_or_id]);
    $post = $stmt2->fetch();
}

// Fallback: match generated slug against title (for posts with NULL p_slug)
if (!$post) {
    $all = db()->query(
        'SELECT p.*, pc.pc_titulo as cat_titulo
         FROM posts p
         LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
         WHERE (p.p_slug IS NULL OR p.p_slug = "") AND p.p_ativo = 1'
    )->fetchAll();
    foreach ($all as $candidate) {
        if (slug($candidate['p_titulo']) === $slug_or_id) {
            $post = $candidate;
            // Auto-save the slug so it works directly next time
            try {
                db()->prepare('UPDATE posts SET p_slug=? WHERE p_id=?')
                   ->execute([$slug_or_id, $candidate['p_id']]);
            } catch (Exception $e) {}
            break;
        }
    }
}

if (!$post) {
    http_response_code(404);
    redirect(BASE_URL . '/blog');
}

// Register view
try {
    db()->prepare('UPDATE posts SET p_views = p_views + 1 WHERE p_id = ?')
       ->execute([$post['p_id']]);
} catch (Exception $e) {}

$_pv_slug = $post['p_slug'] ?: slug($post['p_titulo'] ?? 'post');
try { register_pageview('blog/' . $_pv_slug, $post['p_titulo'] ?? ''); } catch (Exception $e) {}

// Related posts
$related = [];
try {
    $rel_stmt = db()->prepare(
        'SELECT p.*, pc.pc_titulo as cat_titulo
         FROM posts p
         LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
         WHERE p.p_categoria = ? AND p.p_id != ? AND p.p_ativo = 1
         ORDER BY p.p_data DESC LIMIT 3'
    );
    $rel_stmt->execute([$post['p_categoria'], $post['p_id']]);
    $related = $rel_stmt->fetchAll();
} catch (Exception $e) {}

$seo_title    = $post['p_title'] ?: $post['p_titulo'];
$seo_desc     = $post['p_description'] ?: mb_strimwidth(strip_tags($post['p_resumo'] ?? ''), 0, 155, '…');
$_img_base    = $post['p_imagem'] ?? '';
$seo_og_image = $_img_base ? MEDIA_URL . $_img_base : '';

require __DIR__ . '/../includes/header.php';

$_post_slug = $post['p_slug'] ?: slug($post['p_titulo'] ?? '');
?>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Article","headline":"<?= h($post['p_titulo']) ?>","description":"<?= h($seo_desc) ?>","datePublished":"<?= date('c', strtotime($post['p_data'])) ?>","author":{"@type":"Organization","name":"Aligator"},"publisher":{"@type":"Organization","name":"Aligator","url":"<?= SITE_URL ?>"},"url":"<?= SITE_URL . BASE_URL ?>/blog/<?= h($_post_slug) ?>"}
</script>

<div class="page-header" style="padding-bottom:60px">
  <div class="page-header-bg"></div>
  <div class="container page-header-content" style="max-width:780px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
      <a href="<?= BASE_URL ?>/blog" style="color:var(--text-3);font-size:.85rem;display:inline-flex;align-items:center;gap:4px">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5m7-7l-7 7 7 7"/></svg> Blog
      </a>
      <?php if ($post['cat_titulo']): ?>
      <span style="color:var(--text-3)">·</span>
      <a href="<?= BASE_URL ?>/blog?categoria=<?= (int)$post['p_categoria'] ?>"
         style="font-size:.78rem;font-weight:600;color:var(--blue-2)"><?= h($post['cat_titulo']) ?></a>
      <?php endif; ?>
    </div>
    <h1 style="font-size:clamp(1.8rem,4vw,3rem);line-height:1.15;margin-bottom:20px">
      <?= h($post['p_titulo']) ?>
    </h1>
    <div style="display:flex;align-items:center;gap:16px;color:var(--text-3);font-size:.85rem;flex-wrap:wrap">
      <span>Aligator</span>
      <span>·</span>
      <?php
        $meses_pt = ['','janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
        $_ts = strtotime($post['p_data']);
        echo date('d', $_ts) . ' de ' . $meses_pt[(int)date('n', $_ts)] . ' de ' . date('Y', $_ts);
      ?>
      <?php if ($post['p_views']): ?>
      <span>·</span>
      <span><?= number_format($post['p_views']) ?> leituras</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<section>
  <div style="max-width:780px;margin:0 auto;padding:0 24px">
    <?php if ($_img_base): ?>
    <div style="border-radius:var(--r-xl);overflow:hidden;margin-bottom:48px;border:1px solid var(--border)">
      <img src="<?= MEDIA_URL . h($_img_base) ?>"
           onerror="this.onerror=null;this.src='https://aligator.com.br/media/posts/'+this.src.split('/').pop()"
           alt="<?= h($post['p_titulo']) ?>" style="width:100%;display:block">
    </div>
    <?php endif; ?>

    <?php if ($post['p_resumo']): ?>
    <p style="font-size:1.1rem;color:var(--text-2);line-height:1.75;padding:20px 24px;
              background:var(--surface);border-radius:var(--r);border-left:3px solid var(--blue-2);margin-bottom:36px">
      <?= h($post['p_resumo']) ?>
    </p>
    <?php endif; ?>

    <div class="post-content">
      <?= $post['p_texto'] ?>
    </div>

    <div style="margin-top:40px;padding-top:28px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
      <div style="display:flex;gap:8px;align-items:center;font-size:.85rem;color:var(--text-3)">
        Compartilhar:
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(SITE_URL . BASE_URL . '/blog/' . $_post_slug) ?>"
           target="_blank" rel="noopener" class="btn btn-ghost btn-sm">LinkedIn</a>
        <a href="https://wa.me/?text=<?= urlencode($post['p_titulo'] . ' ' . SITE_URL . BASE_URL . '/blog/' . $_post_slug) ?>"
           target="_blank" rel="noopener" class="btn btn-ghost btn-sm">WhatsApp</a>
      </div>
      <a href="<?= BASE_URL ?>/blog" class="btn btn-outline btn-sm">← Voltar ao Blog</a>
    </div>
  </div>
</section>

<?php if (!empty($related)): ?>
<section style="background:var(--bg-2);border-top:1px solid var(--border)">
  <div class="container">
    <h2 style="font-size:1.4rem;margin-bottom:28px" data-reveal>Artigos relacionados</h2>
    <div class="grid-3">
      <?php foreach ($related as $i => $r):
        $rsl = $r['p_slug'] ?: slug($r['p_titulo']); ?>
      <article class="post-card" data-reveal data-reveal-delay="<?= $i+1 ?>">
        <div class="post-thumb">
          <?php if ($r['p_imagem']): ?>
          <img src="<?= MEDIA_URL . h($r['p_imagem']) ?>"
               onerror="this.onerror=null;this.src='https://aligator.com.br/media/posts/'+this.src.split('/').pop()"
               alt="<?= h($r['p_titulo']) ?>" loading="lazy">
          <?php endif; ?>
        </div>
        <div class="post-meta">
          <span class="cat"><?= h($r['cat_titulo'] ?? '') ?></span>
          <span><?= date('d/m/Y', strtotime($r['p_data'])) ?></span>
        </div>
        <h3><a href="<?= BASE_URL ?>/blog/<?= h($rsl) ?>"><?= h($r['p_titulo']) ?></a></h3>
        <a href="<?= BASE_URL ?>/blog/<?= h($rsl) ?>" class="post-read">Ler</a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="cta-section" style="padding:64px 0">
  <div class="container">
    <div class="cta-content" data-reveal>
      <h2 class="display-3">Gostou do conteúdo?</h2>
      <p style="margin-bottom:28px">Descubra como aplicar essas estratégias no seu negócio.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/contato" class="btn btn-primary btn-lg">Falar com especialista</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
