<?php
require __DIR__ . '/../config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$page    = max(1, (int)($_GET['p'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;
$catId   = (int)($_GET['categoria'] ?? 0); // filtro por pc_id

// ── Fetch categories ─────────────────────────────────────────
$categorias = db()->query(
    'SELECT pc.*, COUNT(p.p_id) as total
     FROM posts_categorias pc
     LEFT JOIN posts p ON p.p_categoria = pc.pc_id
     GROUP BY pc.pc_id ORDER BY pc.pc_titulo'
)->fetchAll();

// ── Build query ──────────────────────────────────────────────
$where = 'WHERE p.p_ativo = 1';
$params = [];
if ($catId) {
    $where .= ' AND p.p_categoria = ?';
    $params[] = $catId;
}

$total = (int) db()->prepare(
    "SELECT COUNT(*) FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id $where"
)->execute($params) ? db()->prepare(
    "SELECT COUNT(*) FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id $where"
)->execute($params) : 0;

$stTotal = db()->prepare(
    "SELECT COUNT(*) FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id $where"
);
$stTotal->execute($params);
$total = (int) $stTotal->fetchColumn();

$stPosts = db()->prepare(
    "SELECT p.*, pc.pc_titulo as cat_titulo
     FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
     $where ORDER BY p.p_data DESC LIMIT $perPage OFFSET $offset"
);
$stPosts->execute($params);
$posts = $stPosts->fetchAll();

$pages = ceil($total / $perPage);

// ── Post destaque ─────────────────────────────────────────────
$destaque = db()->query(
    'SELECT p.*, pc.pc_titulo as cat_titulo
     FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
     WHERE p.p_ativo = 1 AND p.p_destaque = 1
     ORDER BY p.p_data DESC LIMIT 1'
)->fetch();

$seo_title = 'Blog — Marketing Digital & Tecnologia';
$seo_desc  = 'Insights, estratégias e tutoriais sobre marketing digital, automação, SEO e crescimento de negócios.';

register_pageview('blog', 'Blog');
require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <div class="label">Blog</div>
    <h1 class="display-2" style="margin-bottom:12px">Insights que geram<br>resultado real</h1>
    <p>Estratégias, tutoriais e análises sobre marketing digital, tecnologia e crescimento.</p>
  </div>
</div>

<section>
  <div class="container">

    <!-- ── Categorias ── -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:48px" data-reveal>
      <a href="<?= BASE_URL ?>/blog"
         class="btn btn-sm <?= !$catId ? 'btn-primary' : 'btn-outline' ?>">
        Todos
      </a>
      <?php foreach ($categorias as $cat): ?>
      <a href="<?= BASE_URL ?>/blog?categoria=<?= (int)$cat['pc_id'] ?>"
         class="btn btn-sm <?= $catId === (int)$cat['pc_id'] ? 'btn-primary' : 'btn-outline' ?>">
        <?= h($cat['pc_titulo']) ?>
        <span style="opacity:.6;font-size:.8em">(<?= (int)$cat['total'] ?>)</span>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- ── Post destaque ── -->
    <?php if ($destaque && $page === 1 && !$catId):
      $sl = $destaque['p_slug'] ?: slug($destaque['p_titulo']); ?>
    <a href="<?= BASE_URL ?>/blog/<?= h($sl) ?>"
       style="display:grid;grid-template-columns:1fr 1fr;gap:0;
              background:var(--surface);border:1px solid var(--border);
              border-radius:var(--r-xl);overflow:hidden;margin-bottom:48px;
              text-decoration:none;transition:border-color .3s"
       class="featured-post two-col" data-reveal
       onmouseover="this.style.borderColor='var(--border-2)'"
       onmouseout="this.style.borderColor='var(--border)'">
      <div style="aspect-ratio:16/10;background:var(--bg-3);overflow:hidden">
        <?php if ($destaque['p_imagem']): ?>
        <img src="<?= MEDIA_URL . h($destaque['p_imagem']) ?>" onerror="this.onerror=null;this.src='https://aligator.com.br/media/posts/'+this.src.split('/').pop()"
             alt="<?= h($destaque['p_titulo']) ?>"
             style="width:100%;height:100%;object-fit:cover;transition:transform .5s"
             onmouseover="this.style.transform='scale(1.04)'"
             onmouseout="this.style.transform='scale(1)'">
        <?php endif; ?>
      </div>
      <div style="padding:40px;display:flex;flex-direction:column;justify-content:center">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
          <span class="badge badge-green">Destaque</span>
          <span style="font-size:.78rem;color:var(--text-3)">
            <?= date('d/m/Y', strtotime($destaque['p_data'])) ?>
          </span>
        </div>
        <h2 style="font-size:1.6rem;margin-bottom:12px;line-height:1.25">
          <?= h($destaque['p_titulo']) ?>
        </h2>
        <p style="font-size:.9rem;color:var(--text-2);margin-bottom:24px;line-height:1.65">
          <?= h(mb_strimwidth(strip_tags($destaque['p_resumo'] ?? ''), 0, 140, '…')) ?>
        </p>
        <span class="post-read">Ler artigo completo</span>
      </div>
    </a>
    <?php endif; ?>

    <!-- ── Grid posts ── -->
    <?php if (empty($posts)): ?>
    <div style="text-align:center;padding:60px 0;color:var(--text-2)">
      <p>Nenhum artigo encontrado nesta categoria.</p>
      <a href="<?= BASE_URL ?>/blog" class="btn btn-outline" style="margin-top:16px">Ver todos</a>
    </div>
    <?php else: ?>
    <div class="grid-3">
      <?php foreach ($posts as $i => $p):
        $sl = $p['p_slug'] ?: slug($p['p_titulo']); ?>
      <article class="post-card" data-reveal data-reveal-delay="<?= ($i % 3) + 1 ?>">
        <div class="post-thumb">
          <?php if ($p['p_imagem']): ?>
          <img src="<?= MEDIA_URL . h($p['p_imagem']) ?>"
               alt="<?= h($p['p_titulo']) ?>" loading="lazy"
               onerror="this.src='https://aligator.com.br/media/posts/'+this.src.split('/').pop()">
          <?php else: ?>
          <div style="width:100%;height:100%;background:var(--surface);
                      display:flex;align-items:center;justify-content:center;
                      color:var(--text-3);font-size:2.5rem">📝</div>
          <?php endif; ?>
        </div>
        <div class="post-meta">
          <a href="<?= BASE_URL ?>/blog?categoria=<?= (int)($p['p_categoria'] ?? 0) ?>" class="cat">
            <?= h($p['cat_titulo'] ?? 'Marketing') ?>
          </a>
          <span><?= date('d/m/Y', strtotime($p['p_data'])) ?></span>
          <?php if ($p['p_views']): ?>
          <span><?= number_format($p['p_views']) ?> views</span>
          <?php endif; ?>
        </div>
        <h3><a href="<?= BASE_URL ?>/blog/<?= h($sl) ?>"><?= h($p['p_titulo']) ?></a></h3>
        <p class="excerpt">
          <?= h(mb_strimwidth(strip_tags($p['p_resumo'] ?? ''), 0, 110, '…')) ?>
        </p>
        <a href="<?= BASE_URL ?>/blog/<?= h($sl) ?>" class="post-read">Ler artigo</a>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- ── Paginação ── -->
    <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Paginação">
      <?php if ($page > 1): ?>
      <a href="?p=<?= $page - 1 ?><?= $catId ? "&categoria=$catId" : '' ?>"
         aria-label="Anterior">‹</a>
      <?php endif; ?>

      <?php for ($i = max(1, $page-2); $i <= min($pages, $page+2); $i++): ?>
      <?php if ($i === $page): ?>
      <span class="current"><?= $i ?></span>
      <?php else: ?>
      <a href="?p=<?= $i ?><?= $catId ? "&categoria=$catId" : '' ?>"><?= $i ?></a>
      <?php endif; ?>
      <?php endfor; ?>

      <?php if ($page < $pages): ?>
      <a href="?p=<?= $page + 1 ?><?= $catId ? "&categoria=$catId" : '' ?>"
         aria-label="Próxima">›</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>
    <?php endif; ?>

  </div>
</section>

<!-- CTA inline -->
<section class="cta-section" style="padding:64px 0">
  <div class="container">
    <div class="cta-content" data-reveal>
      <h2 class="display-3">Quer crescer como<br>nossos clientes?</h2>
      <p style="margin-bottom:32px">Agende uma conversa e descubra qual solução faz mais sentido para o seu momento.</p>
      <a href="<?= BASE_URL ?>/contato" class="btn btn-primary btn-lg">Falar com especialista</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
