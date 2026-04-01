<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$seo_title = 'Sobre a Aligator — Tech Marketing Company desde 2009';
$seo_desc  = 'Conheça a história, a missão e os valores da Aligator. Uma empresa que une tecnologia, estratégia e execução para fazer negócios crescerem.';

register_pageview('sobre', 'Sobre');
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <div class="label">Sobre</div>
    <h1 class="display-2" style="margin-bottom:12px">
      O olhar atento que<br>
      <span class="text-green">guia o crescimento</span>
    </h1>
    <p>Fundada em 2009, a Aligator combina estratégia de marketing com desenvolvimento tecnológico
       para criar resultados mensuráveis e duradouros.</p>
  </div>
</div>

<!-- Manifesto -->
<section>
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center" class="two-col">
      <div data-reveal>
        <div class="label" style="margin-bottom:20px">Nossa história</div>
        <h2 class="display-3" style="margin-bottom:24px">
          15 anos criando<br>presença digital real
        </h2>
        <p style="font-size:1rem;color:var(--text-2);line-height:1.8;margin-bottom:20px">
          A Aligator nasceu em 2009 com uma missão simples: ajudar empresas a crescerem
          de verdade no ambiente digital — não apenas a "ter presença".
        </p>
        <p style="font-size:1rem;color:var(--text-2);line-height:1.8;margin-bottom:20px">
          Com o passar dos anos, evoluímos de agência de marketing para uma
          <strong style="color:var(--text)">Tech Marketing Company</strong>: uma empresa que une
          o melhor da tecnologia com estratégia de crescimento previsível. Hoje
          desenvolvemos sistemas, automações e estratégias que trabalham em conjunto
          para multiplicar resultados.
        </p>
        <p style="font-size:1rem;color:var(--text-2);line-height:1.8">
          Como o jacaré — nosso símbolo — observamos o mercado com atenção, agimos
          com precisão e dominamos o território com inteligência.
        </p>
      </div>
      <div data-reveal data-reveal-delay="2">
        <!-- Timeline simplificada -->
        <div style="position:relative;padding-left:28px">
          <div style="position:absolute;left:0;top:0;bottom:0;width:2px;
                      background:linear-gradient(180deg,var(--green),transparent)"></div>
          <?php $timeline = [
            ['2009','Fundação da Aligator em São Bernardo do Campo, SP'],
            ['2012','Primeiros sistemas personalizados para clientes'],
            ['2015','Expansão para tráfego pago e SEO técnico'],
            ['2018','Integração de automações com Make e APIs'],
            ['2021','Início do desenvolvimento de SaaS próprios'],
            ['2024','Reposicionamento como Tech Marketing Company'],
            ['2025','IA integrada em todas as soluções'],
          ]; foreach ($timeline as $t): ?>
          <div style="position:relative;margin-bottom:24px;padding-bottom:24px;
                      border-bottom:1px solid var(--border)">
            <div style="position:absolute;left:-34px;top:2px;
                        width:12px;height:12px;border-radius:50%;
                        background:var(--green);box-shadow:0 0 8px rgba(0,232,122,.5)"></div>
            <div style="font-family:var(--font-d);font-weight:700;color:var(--green);
                        font-size:.85rem;margin-bottom:4px"><?= $t[0] ?></div>
            <div style="font-size:.9rem;color:var(--text-2)"><?= $t[1] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Missão / Visão / Valores -->
<section style="background:var(--bg-3);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container">
    <div class="section-header text-center" data-reveal>
      <div class="label">DNA Aligator</div>
      <h2 class="display-3">O que nos move</h2>
    </div>
    <div class="grid-3">
      <?php $mvv = [
        ['<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>','Missão','Transformar dados em ações estratégicas, orientadas a resultados, para que marcas e empresas cresçam com confiança e previsibilidade no ambiente digital.'],
        ['<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>','Visão','Ser reconhecida como referência em soluções que unem tecnologia, dados e estratégia para gerar crescimento real e mensurável para nossos clientes.'],
        ['<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>','Valores','Vigilância e precisão em cada decisão. Foco total em resultados. Parceria de verdade com cada cliente. Inovação constante e adaptabilidade ao mercado.'],
      ]; foreach ($mvv as $i => $v): ?>
      <div class="card" data-reveal data-reveal-delay="<?= $i + 1 ?>">
        <div style="font-size:2rem;margin-bottom:16px"><?= $v[0] ?></div>
        <h3 style="font-size:1rem;color:var(--green);letter-spacing:.08em;
                   text-transform:uppercase;margin-bottom:12px"><?= $v[1] ?></h3>
        <p style="font-size:.9rem;color:var(--text-2);line-height:1.7"><?= $v[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Clientes -->
<?php
$clientes = db()->query('SELECT * FROM clientes ORDER BY c_id')->fetchAll();
if ($clientes):
?>
<section>
  <div class="container">
    <div class="section-header text-center" data-reveal>
      <div class="label">Portfólio</div>
      <h2 class="display-3">Empresas que confiam na Aligator</h2>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center" data-reveal>
      <?php foreach ($clientes as $c): ?>
      <div style="background:var(--surface);border:1px solid var(--border);
                  border-radius:var(--r);padding:14px 20px;font-size:.85rem;
                  color:var(--text-2);transition:border-color .2s"
           onmouseover="this.style.borderColor='var(--border-2)'"
           onmouseout="this.style.borderColor='var(--border)'">
        <?= h($c['c_titulo']) ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:40px" data-reveal>
      <p style="font-size:.9rem;color:var(--text-3)">
        +200 projetos entregues desde 2009 em diversos segmentos
      </p>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content" data-reveal>
      <h2 class="display-2">
        Vamos construir sua<br>
        <span class="text-green">próxima fase?</span>
      </h2>
      <p>15 anos de experiência a serviço do seu crescimento. Agende uma conversa.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/contato" class="btn btn-primary btn-lg">Falar com a Aligator</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
