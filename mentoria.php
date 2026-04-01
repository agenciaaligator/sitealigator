<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$seo_title = 'Mentoria Estratégica em Marketing Digital';
$seo_desc  = 'Mentoria individual e em grupo para empreendedores e equipes que querem escalar com método, clareza e acompanhamento real.';
$calendly  = cfg('calendly_url', 'https://calendly.com/agenciaaligator');

register_pageview('mentoria', 'Mentoria');
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <div class="label">Mentoria Estratégica</div>
    <h1 class="display-2" style="margin-bottom:12px">
      Clareza, método e<br>
      <span class="text-green">acompanhamento real</span>
    </h1>
    <p>Para empreendedores e equipes que querem escalar com inteligência — sem adivinhar o próximo passo.</p>
  </div>
</div>

<!-- Para quem é -->
<section>
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center" class="two-col">
      <div data-reveal>
        <div class="label" style="margin-bottom:20px">Para quem é?</div>
        <h2 class="display-3" style="margin-bottom:24px">
          Para quem está pronto<br>para avançar com precisão
        </h2>
        <p style="font-size:1rem;color:var(--text-2);line-height:1.8;margin-bottom:32px">
          Não é para quem busca fórmulas mágicas. É para quem entende que crescimento
          exige estratégia, execução e ajuste constante — e quer ter alguém experiente
          ao lado durante esse processo.
        </p>
        <?php $targets = [
          'Empreendedores digitais que querem escalar com método',
          'Gestores de marketing que precisam de visão estratégica',
          'Profissionais migrando para o mercado digital',
          'Agências e freelancers buscando posicionamento premium',
          'Equipes que precisam estruturar seu processo de growth',
        ]; foreach ($targets as $t): ?>
        <div style="display:flex;gap:12px;align-items:flex-start;
                    margin-bottom:12px;font-size:.9rem;color:var(--text-2)">
          <span style="color:var(--green);font-weight:700;flex-shrink:0">🐊</span>
          <?= h($t) ?>
        </div>
        <?php endforeach; ?>
      </div>
      <div data-reveal data-reveal-delay="2">
        <div class="card" style="padding:40px">
          <div style="font-size:.75rem;font-weight:700;letter-spacing:.1em;
                      text-transform:uppercase;color:var(--text-3);margin-bottom:24px">
            O que você vai obter
          </div>
          <?php $gains = [
            ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>','Diagnóstico honesto do seu negócio e dos seus gargalos'],
            ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>','Plano de ação personalizado com prioridades claras'],
            ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>','Estratégias de crescimento validadas e aplicáveis'],
            ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>','Indicações de ferramentas e automações para cada fase'],
            ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>','Acompanhamento e suporte entre as sessões'],
            ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>','Métricas e KPIs para monitorar o progresso'],
          ]; foreach ($gains as $g): ?>
          <div style="display:flex;gap:14px;align-items:flex-start;
                      padding:12px 0;border-bottom:1px solid var(--border)">
            <span style="font-size:1.1rem;flex-shrink:0"><?= $g[0] ?></span>
            <span style="font-size:.9rem;color:var(--text-2)"><?= $g[1] ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Formatos -->
<section style="background:var(--bg-3);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container">
    <div class="section-header text-center" data-reveal>
      <div class="label">Formatos disponíveis</div>
      <h2 class="display-3">Escolha o modelo ideal</h2>
    </div>
    <div class="grid-3">
      <?php $planos = [
        [
          'nome'   => 'Sessão Avulsa',
          'preco'  => '1x',
          'desc'   => 'Ideal para resolver um problema pontual, tirar dúvidas estratégicas ou ter uma visão geral do seu negócio.',
          'itens'  => ['60 minutos via Google Meet','Gravação da sessão','Resumo e plano de ação','Suporte por 7 dias'],
          'cta'    => 'Agendar sessão',
          'dest'   => false,
        ],
        [
          'nome'   => 'Acompanhamento Mensal',
          'preco'  => 'Mensal',
          'desc'   => 'Para quem quer evoluir de forma consistente, com sessões regulares, suporte contínuo e ajuste de rota.',
          'itens'  => ['4 sessões de 60 min/mês','Suporte contínuo via WhatsApp','Revisão de estratégias e conteúdos','Relatório mensal de progresso','Acesso a materiais exclusivos'],
          'cta'    => 'Quero o acompanhamento',
          'dest'   => true,
        ],
        [
          'nome'   => 'Imersão Intensiva',
          'preco'  => '1 dia',
          'desc'   => 'Um dia completo dedicado a mergulhar no seu negócio, mapear oportunidades e sair com um plano de 90 dias.',
          'itens'  => ['6–8h de imersão presencial ou online','Diagnóstico completo do negócio','Plano estratégico 90 dias','Mapeamento de automações','Suporte por 30 dias'],
          'cta'    => 'Consultar disponibilidade',
          'dest'   => false,
        ],
      ]; foreach ($planos as $i => $p): ?>
      <div class="card <?= $p['dest'] ? '' : '' ?>"
           style="<?= $p['dest'] ? 'border-color:var(--border-2);box-shadow:var(--shadow-green);position:relative' : '' ?>"
           data-reveal data-reveal-delay="<?= $i + 1 ?>">
        <?php if ($p['dest']): ?>
        <div style="position:absolute;top:-12px;left:50%;transform:translateX(-50%)">
          <span class="badge badge-green">Mais escolhido</span>
        </div>
        <?php endif; ?>
        <div style="font-size:.75rem;font-weight:700;letter-spacing:.1em;
                    text-transform:uppercase;color:var(--text-3);margin-bottom:4px">
          <?= h($p['nome']) ?>
        </div>
        <div style="font-family:var(--font-d);font-size:1.8rem;font-weight:800;
                    color:<?= $p['dest'] ? 'var(--green)' : 'var(--text)' ?>;margin-bottom:16px">
          <?= h($p['preco']) ?>
        </div>
        <p style="font-size:.88rem;color:var(--text-2);line-height:1.65;margin-bottom:24px">
          <?= h($p['desc']) ?>
        </p>
        <ul style="display:flex;flex-direction:column;gap:10px;margin-bottom:28px">
          <?php foreach ($p['itens'] as $item): ?>
          <li style="display:flex;gap:8px;font-size:.85rem;color:var(--text-2)">
            <span style="color:var(--green)">✓</span> <?= h($item) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="<?= h($calendly) ?>" target="_blank" rel="noopener"
           class="btn <?= $p['dest'] ? 'btn-primary' : 'btn-outline' ?>"
           style="width:100%;justify-content:center">
          <?= h($p['cta']) ?>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Calendly embed -->
<section>
  <div class="container-s text-center">
    <div data-reveal>
      <div class="label" style="margin:0 auto 20px">Agende agora</div>
      <h2 class="display-3" style="margin-bottom:12px">Escolha data e horário</h2>
      <p class="subtitle" style="margin-bottom:40px">
        Selecione o melhor momento para uma conversa inicial gratuita de 20 minutos.
      </p>
    </div>
    <!-- Calendly inline widget (lazy load) -->
    <div id="calendly-container" style="background:var(--surface);border:1px solid var(--border);
         border-radius:var(--r-xl);overflow:hidden;min-height:200px;
         display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px"
         data-reveal>
      <p style="color:var(--text-2);font-size:.9rem">Clique para abrir a agenda de agendamentos</p>
      <a href="<?= h($calendly) ?>" target="_blank" rel="noopener"
         class="btn btn-primary btn-lg">
        📅 Abrir agenda
      </a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
