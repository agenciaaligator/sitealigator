<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$seo_title = 'Soluções em Marketing Digital e Tecnologia';
$seo_desc  = 'Sistemas personalizados, automação com IA, growth performance, SEO técnico e muito mais. Conheça as soluções da Aligator.';

register_pageview('solucoes', 'Soluções');
require __DIR__ . '/includes/header.php';

$solucoes = [
  [
    'id'      => 'sistemas',
    'icon'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'tag'     => 'SaaS · Micro SaaS · PHP',
    'titulo'  => 'Sistemas Personalizados',
    'desc'    => 'Desenvolvemos plataformas digitais sob medida — desde painéis administrativos até SaaS completos. Cada sistema é construído com PHP e MySQL, compatível com hospedagem compartilhada, com admin intuitivo, SEO integrado e escalabilidade planejada desde o início.',
    'items'   => ['Plataformas SaaS e Micro SaaS','Painéis administrativos com CRUD','Sistemas de cadastro e gestão','Integração com APIs externas','E-commerce personalizado','Área de membros e portais'],
    'cta'     => 'Solicitar orçamento',
  ],
  [
    'id'      => 'automacao',
    'icon'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
    'tag'     => 'Make · API · IA',
    'titulo'  => 'Automação & Inteligência Artificial',
    'desc'    => 'Elimine tarefas manuais e conecte seus sistemas com automações inteligentes. Utilizamos Make.com, APIs REST, OpenAI e ferramentas complementares para criar fluxos que trabalham por você 24/7, capturando leads, enviando notificações e alimentando relatórios automaticamente.',
    'items'   => ['Automação de marketing (CRM, e-mail, WhatsApp)','Fluxos Make.com / n8n personalizados','Integração entre plataformas via API','IA para atendimento e qualificação de leads','Relatórios automáticos no Google Sheets / Looker Studio','Chatbots e assistentes com LLMs'],
    'cta'     => 'Ver como funciona',
  ],
  [
    'id'      => 'growth',
    'icon'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
    'tag'     => 'Google Ads · Meta Ads · CRO',
    'titulo'  => 'Growth Performance',
    'desc'    => 'Estratégias de aquisição pagas com foco absoluto em CAC e ROI. Criamos e gerenciamos campanhas no Google, Meta e YouTube com acompanhamento de conversões, testes A/B e relatórios transparentes. Não gerenciamos "cliques" — gerenciamos resultados.',
    'items'   => ['Google Ads (Search, Display, Shopping, YouTube)','Meta Ads (Facebook e Instagram)','Remarketing avançado e lookalike','Rastreamento e atribuição com GA4 + GTM','Otimização de landing pages (CRO)','Relatórios mensais com insights estratégicos'],
    'cta'     => 'Falar sobre sua campanha',
  ],
  [
    'id'      => 'seo',
    'icon'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>',
    'tag'     => 'Orgânico · Técnico · Conteúdo',
    'titulo'  => 'SEO Técnico',
    'desc'    => 'Visibilidade orgânica construída para durar. Realizamos auditorias técnicas completas, corrigimos problemas de rastreamento e indexação, construímos a arquitetura de informação ideal e produzimos conteúdo evergreen que atrai visitantes qualificados mês após mês.',
    'items'   => ['Auditoria técnica completa (Core Web Vitals, indexação)','Arquitetura de URLs e estrutura de informação','Produção de conteúdo evergreen com IA','Link building estratégico','Schema markup e dados estruturados','SEO local para negócios regionais'],
    'cta'     => 'Solicitar auditoria',
  ],
  [
    'id'      => 'eventos',
    'icon'    => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    'tag'     => 'Eventos · Ingressos · Marketing',
    'titulo'  => 'Soluções para Eventos',
    'desc'    => 'Infraestrutura digital completa para eventos: sistemas de venda de ingressos, landing pages de alta conversão, estratégias de divulgação e gestão de presença online. Já apoiamos dezenas de eventos desde festas locais até congressos.',
    'items'   => ['Sistemas customizados para organizadores de eventos','Landing pages com contador e urgência','Campanhas de divulgação (Google + Meta)','Web aplicativo personalizado','E-mail marketing pré e pós-evento','Relatórios de performance'],
    'cta'     => 'Planejar meu evento',
  ],
];
?>

<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <div class="label">Soluções</div>
    <h1 class="display-2" style="margin-bottom:12px">
      Tecnologia e estratégia<br>
      <span class="text-green">em um só lugar</span>
    </h1>
    <p>Da concepção à execução. Cada solução é desenhada para o seu momento de crescimento.</p>
  </div>
</div>

<!-- Âncoras de navegação -->
<div style="background:var(--bg-3);border-bottom:1px solid var(--border);position:sticky;top:64px;z-index:90">
  <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;-ms-overflow-style:none">
    <div style="display:flex;gap:0;min-width:max-content;padding:0 18px">
      <?php foreach ($solucoes as $s): ?>
      <a href="#<?= $s['id'] ?>"
         style="display:inline-flex;align-items:center;gap:8px;
                padding:14px 18px;font-size:.82rem;font-weight:600;color:var(--text-2);
                white-space:nowrap;border-bottom:2px solid transparent;
                transition:color .2s,border-color .2s;flex-shrink:0;text-decoration:none"
         onmouseover="this.style.color='var(--blue-2)';this.style.borderBottomColor='var(--blue-2)'"
         onmouseout="this.style.color='var(--text-2)';this.style.borderBottomColor='transparent'">
        <?= $s['icon'] ?> <?= h($s['titulo']) ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<style>.sol-nav::-webkit-scrollbar{display:none}</style>

<?php foreach ($solucoes as $i => $s): ?>
<section id="<?= $s['id'] ?>"
         style="<?= $i % 2 === 1 ? 'background:var(--bg-2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)' : '' ?>">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;" class="two-col"
                <?= $i % 2 === 1 ? 'direction:rtl' : '' ?>">
      <div style="<?= $i % 2 === 1 ? 'direction:ltr' : '' ?>" data-reveal>
        <div class="service-icon" style="margin-bottom:24px"><?= $s['icon'] ?></div>
        <span class="service-tag"><?= h($s['tag']) ?></span>
        <h2 class="display-3" style="margin:8px 0 20px"><?= h($s['titulo']) ?></h2>
        <p style="font-size:1rem;color:var(--text-2);line-height:1.75;margin-bottom:32px">
          <?= h($s['desc']) ?>
        </p>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <a href="<?= BASE_URL ?>/contato?servico=<?= $s['id'] ?>" class="btn btn-primary">
            <?= h($s['cta']) ?>
          </a>
          <a href="<?= BASE_URL ?>/<?= ['sistemas'=>'sistemas','automacao'=>'automacao','growth'=>'growth','seo'=>'seo-tecnico','eventos'=>'solucoes-eventos'][$s['id']] ?? 'solucoes' ?>"
             class="btn btn-outline">
            Ver detalhes
          </a>
        </div>
      </div>
      <div style="<?= $i % 2 === 1 ? 'direction:ltr' : '' ?>" data-reveal data-reveal-delay="2">
        <div class="card">
          <div style="font-size:.75rem;font-weight:700;letter-spacing:.1em;
                      text-transform:uppercase;color:var(--text-3);margin-bottom:20px">
            O que inclui
          </div>
          <ul style="display:flex;flex-direction:column;gap:12px">
            <?php foreach ($s['items'] as $item): ?>
            <li style="display:flex;gap:12px;align-items:flex-start;font-size:.9rem;color:var(--text-2)">
              <span style="color:var(--green);font-weight:700;flex-shrink:0;font-size:.8rem;margin-top:3px">✓</span>
              <?= h($item) ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endforeach; ?>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content" data-reveal>
      <div class="label" style="margin:0 auto 20px">Pronto para começar?</div>
      <h2 class="display-2">
        Qual solução faz sentido<br>para <span class="text-green">você agora?</span>
      </h2>
      <p>Agende uma conversa de 30 minutos. Identificamos exatamente onde atuar para gerar o maior impacto.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/contato" class="btn btn-primary btn-lg">Falar com especialista</a>
        <a href="https://wa.me/<?= SITE_WHATS ?>" target="_blank" rel="noopener"
           class="btn btn-outline btn-lg">WhatsApp direto</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
