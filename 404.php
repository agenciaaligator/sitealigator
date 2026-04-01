<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');
http_response_code(404);
$seo_title = 'Página não encontrada';
$seo_desc  = 'A página que você procura não existe ou foi movida.';
require __DIR__ . '/includes/header.php';
?>
<section style="min-height:80vh;display:flex;align-items:center;padding:120px 0 60px">
  <div class="container" style="text-align:center">
    <div data-reveal>
      <div style="font-family:var(--font-d);font-size:clamp(6rem,18vw,12rem);font-weight:800;
                  color:var(--surface);line-height:1;letter-spacing:-.05em;
                  text-shadow:0 0 80px rgba(47,109,173,.1)">404</div>
      <div class="label" style="margin:0 auto 20px">PÁGINA NÃO ENCONTRADA</div>
      <h1 class="display-3" style="margin-bottom:12px">
        O jacaré não encontrou<br>o que você procura
      </h1>
      <p style="font-size:1rem;color:var(--text-2);max-width:420px;margin:0 auto 36px">
        A página pode ter sido removida, renomeada ou o endereço foi digitado incorretamente.
      </p>
      <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/" class="btn btn-primary btn-lg">← Voltar ao início</a>
        <a href="<?= BASE_URL ?>/blog" class="btn btn-outline btn-lg">Ver o Blog</a>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
