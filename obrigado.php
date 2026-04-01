<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');
$seo_title = 'Mensagem Enviada — Obrigado!';
$seo_desc  = 'Recebemos sua mensagem. Entraremos em contato em breve.';
require __DIR__ . '/includes/header.php';
?>

<section style="min-height:80vh;display:flex;align-items:center">
  <div class="container" style="text-align:center">
    <div data-reveal>

      <!-- Check icon -->
      <div style="width:80px;height:80px;border-radius:50%;
                  background:var(--blue-dim);border:2px solid var(--blue-2);
                  display:flex;align-items:center;justify-content:center;
                  margin:0 auto 32px;box-shadow:0 0 40px var(--blue-glow)">
        <svg width="36" height="36" fill="none" stroke="var(--blue-2)" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M5 13l4 4L19 7"/>
        </svg>
      </div>

      <div class="label" style="margin:0 auto 20px">Mensagem recebida</div>
      <h1 class="display-2" style="margin-bottom:16px">
        Obrigado pelo<br>
        <span style="color:var(--blue-2)">contato!</span>
      </h1>
      <p style="font-size:1.1rem;color:var(--text-2);max-width:480px;
                margin:0 auto 40px;line-height:1.75">
        Nossa equipe recebeu sua mensagem e entrará em contato
        em até <strong style="color:var(--text)">1 dia útil</strong>.
        <br><br>
        Enquanto isso, que tal conhecer melhor nossas soluções?
      </p>

      <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/solucoes" class="btn btn-primary btn-lg">
          Ver Soluções
        </a>
        <a href="<?= BASE_URL ?>/" class="btn btn-outline btn-lg">
          Voltar ao início
        </a>
      </div>

      <!-- Quick contact -->
      <div style="margin-top:48px;padding-top:32px;border-top:1px solid var(--border)">
        <p style="font-size:.88rem;color:var(--text-3);margin-bottom:16px">
          Prefere falar agora?
        </p>
        <a href="https://wa.me/<?= SITE_WHATS ?>?text=Ol%C3%A1%2C+acabei+de+enviar+uma+mensagem+pelo+site+e+quero+agilizar+o+atendimento."
           target="_blank" rel="noopener"
           class="btn btn-outline" style="gap:10px">
          <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
          </svg>
          Falar no WhatsApp agora
        </a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
