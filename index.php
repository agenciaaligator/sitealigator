<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$seo_title = 'Aligator';
$seo_desc  = 'Tech Marketing Company. Sistemas digitais, automação com IA e crescimento previsível. Desde 2009.';

register_pageview('home', 'Home');

// ── Fetch services ──────────────────────────────────────────
// s_ordem adicionado pela migration; fallback para s_id se coluna não existir
try {
    $servicos = db()->query(
        'SELECT * FROM servicos WHERE s_visivel = 1 ORDER BY s_ordem, s_id LIMIT 6'
    )->fetchAll();
} catch (Exception $e) {
    $servicos = db()->query(
        'SELECT * FROM servicos WHERE s_visivel = 1 ORDER BY s_id LIMIT 6'
    )->fetchAll();
}

// ── Fetch latest posts ──────────────────────────────────────
$posts = db()->query(
    'SELECT p.*, pc.pc_titulo as cat_titulo
     FROM posts p
     LEFT JOIN posts_categorias pc ON p.p_categoria = pc.pc_id
     WHERE p.p_ativo = 1
     ORDER BY p.p_data DESC LIMIT 3'
)->fetchAll();

// ── Fetch depoimentos ──────────────────────────────────────
// Ativos com texto preenchido, ordenados por d_ordem, depois os mais antigos (conteúdo mais rico)
try {
    $depos_all = db()->query(
        "SELECT * FROM depoimentos
         WHERE d_ativo = 1 AND d_texto IS NOT NULL AND d_texto != ''
         ORDER BY CASE WHEN d_ordem = 0 OR d_ordem IS NULL THEN 9999 ELSE d_ordem END ASC, d_id ASC
         LIMIT 20"
    )->fetchAll();
    // Filter out rows where text is only HTML tags/whitespace
    $depos = [];
    foreach ($depos_all as $row) {
        $_rt = preg_replace('/<br\s*\/?>/i', ' ', $row['d_texto'] ?? '');
        $_rt = strip_tags($_rt);
        $_rt = str_replace(['\\r\\n','\\r','\\n','\\t'], ' ', $_rt);
        $_rt = str_replace(["\r\n","\r","\n","\t"], ' ', $_rt);
        $t   = trim(preg_replace('/\s+/', ' ', $_rt));
        if (strlen($t) > 5) { $depos[] = $row; }
        if (count($depos) >= 3) break;
    }
} catch (Exception $e) {
    $depos = db()->query(
        "SELECT * FROM depoimentos WHERE d_ativo = 1 AND d_texto IS NOT NULL ORDER BY d_id ASC LIMIT 3"
    )->fetchAll();
}

require __DIR__ . '/includes/header.php';
?>

<!-- ====================================================
     HERO
===================================================== -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid" aria-hidden="true"></div>

  <div class="container">
    <div class="hero-content">
      <div class="label" data-reveal>Tech Marketing Company</div>

      <h1 class="display-1" data-reveal data-reveal-delay="1">
        Crescimento<br>
        <em>previsível</em> com<br>
        tecnologia e IA
      </h1>

      <p data-reveal data-reveal-delay="2">
        Desenvolvemos sistemas digitais, automações inteligentes e estratégias
        de performance que transformam sua empresa em uma máquina de crescimento.
      </p>

      <div class="hero-actions" data-reveal data-reveal-delay="3">
        <a href="<?= BASE_URL ?>/contato" class="btn btn-primary btn-lg">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          Agendar Reunião Estratégica
        </a>
        <a href="<?= BASE_URL ?>/solucoes" class="btn btn-outline btn-lg">Ver Soluções</a>
      </div>
    </div>

    <!-- Dashboard mockup visual -->
    <div class="hero-visual" aria-hidden="true">
    <div class="dashboard-mock" data-reveal data-reveal-delay="2">
      <div class="dash-header">
        <div class="dash-dots">
          <span></span><span></span><span></span>
        </div>
        <div class="dash-title">aligator · analytics</div>
      </div>
      <div class="dash-body">
        <div class="dash-kpis">
          <div class="dash-kpi">
            <div class="kpi-label">Leads</div>
            <div class="kpi-value">2.847</div>
            <div class="kpi-delta">↑ 34%</div>
          </div>
          <div class="dash-kpi">
            <div class="kpi-label">Conversão</div>
            <div class="kpi-value">8.4%</div>
            <div class="kpi-delta">↑ 12%</div>
          </div>
          <div class="dash-kpi">
            <div class="kpi-label">ROI</div>
            <div class="kpi-value">4.2x</div>
            <div class="kpi-delta">↑ 18%</div>
          </div>
        </div>
        <div class="dash-chart">
          <div class="dash-chart-label">Desempenho — últimos 12 meses</div>
          <div class="dash-chart-bars">
            <?php for ($i = 0; $i < 12; $i++): ?>
            <div class="bar<?= $i === 11 ? ' active' : '' ?>" style="height:0"></div>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     STATS
===================================================== -->
<section style="padding: 0">
  <div class="container">
    <div class="stats-row" data-reveal>
      <div class="stat-item">
        <div class="stat-number" data-target="15" data-suffix="+">0+</div>
        <div class="stat-label">Anos de mercado</div>
      </div>
      <div class="stat-item">
        <div class="stat-number" data-target="200" data-suffix="+">0+</div>
        <div class="stat-label">Projetos entregues</div>
      </div>
      <div class="stat-item">
        <div class="stat-number" data-target="4.2" data-suffix="x">0x</div>
        <div class="stat-label">ROI médio dos clientes</div>
      </div>
      <div class="stat-item">
        <div class="stat-number" data-target="97" data-suffix="%">0%</div>
        <div class="stat-label">Taxa de retenção</div>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     SOLUÇÕES
===================================================== -->
<section id="solucoes">
  <div class="container">
    <div class="section-header" data-reveal>
      <div class="label">O que fazemos</div>
      <h2 class="display-2" style="max-width:600px">
        Soluções que movem<br>
        <span class="text-green">negócios de verdade</span>
      </h2>
      <p class="subtitle" style="max-width:520px;margin-top:16px">
        Não entregamos serviços genéricos. Construímos sistemas, estratégias e
        automações sob medida para cada etapa do crescimento da sua empresa.
      </p>
    </div>

    <div class="grid-3">
      <?php
      // Serviços da página Soluções — com links para páginas individuais
      $home_servicos = [
        [
          'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
          'tag'   => 'SaaS · Micro SaaS · PHP',
          'title' => 'Sistemas Personalizados',
          'desc'  => 'Plataformas digitais sob medida — painéis administrativos, SaaS e micro SaaS com PHP e MySQL, compatíveis com hospedagem compartilhada.',
          'url'   => BASE_URL . '/sistemas',
        ],
        [
          'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
          'tag'   => 'Make · API · IA',
          'title' => 'Automação & Inteligência Artificial',
          'desc'  => 'Fluxos automatizados com Make.com, APIs e OpenAI que capturam leads, enviam notificações e geram relatórios 24/7 sem intervenção humana.',
          'url'   => BASE_URL . '/automacao',
        ],
        [
          'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
          'tag'   => 'Google Ads · Meta Ads · CRO',
          'title' => 'Growth Performance',
          'desc'  => 'Campanhas pagas no Google e Meta com foco absoluto em CAC e ROI. Rastreamento, testes A/B e relatórios transparentes.',
          'url'   => BASE_URL . '/growth',
        ],
        [
          'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>',
          'tag'   => 'Orgânico · Técnico · Conteúdo',
          'title' => 'SEO Técnico',
          'desc'  => 'Auditoria técnica, arquitetura de URLs e conteúdo evergreen para visibilidade orgânica duradoura e autoridade no seu nicho.',
          'url'   => BASE_URL . '/seo-tecnico',
        ],
        [
          'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
          'tag'   => 'Eventos · Ingressos · Marketing',
          'title' => 'Soluções para Eventos',
          'desc'  => 'Sistemas customizados, landing pages de alta conversão e campanhas de divulgação para eventos de qualquer porte.',
          'url'   => BASE_URL . '/solucoes-eventos',
        ],
        [
          'icon'  => '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
          'tag'   => '1:1 · Grupo · Intensiva',
          'title' => 'Mentoria Estratégica',
          'desc'  => 'Diagnóstico honesto, plano de ação personalizado e acompanhamento real para escalar com método e clareza.',
          'url'   => BASE_URL . '/mentoria-estrategica',
        ],
      ];
      foreach ($home_servicos as $i => $sv): $delay = $i % 3 + 1; ?>
      <a href="<?= h($sv['url']) ?>" class="card service-card" style="text-decoration:none;display:flex;flex-direction:column" data-reveal data-reveal-delay="<?= $delay ?>">
        <div class="service-icon" aria-hidden="true"><?= $sv['icon'] ?></div>
        <span class="service-tag"><?= h($sv['tag']) ?></span>
        <h3><?= h($sv['title']) ?></h3>
        <p><?= h($sv['desc']) ?></p>
        <span class="service-link">Saiba mais</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ====================================================
     AUTOMATION FLOW
===================================================== -->
<section style="background:var(--bg-3);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center" class="service-section-grid two-col">
      <div data-reveal>
        <div class="label" style="margin-bottom:20px">Como funciona</div>
        <h2 class="display-3" style="margin-bottom:20px">
          Tecnologia que<br>
          trabalha por você
        </h2>
        <p class="subtitle" style="margin-bottom:32px">
          Desenvolvemos fluxos de automação que conectam suas ferramentas,
          capturam leads automaticamente e alimentam sua equipe com dados precisos.
        </p>
        <ul style="display:flex;flex-direction:column;gap:12px">
          <?php $benefits = [
            ['icon'=>'✓','text'=>'Integração com CRMs, ERPs e plataformas de e-mail'],
            ['icon'=>'✓','text'=>'Relatórios automáticos no Google Sheets ou Looker Studio'],
            ['icon'=>'✓','text'=>'Notificações em tempo real via WhatsApp ou Slack'],
            ['icon'=>'✓','text'=>'Redução de 80% em tarefas manuais repetitivas'],
          ]; foreach ($benefits as $b): ?>
          <li style="display:flex;gap:12px;font-size:.95rem;color:var(--text-2)">
            <span style="color:var(--green);font-weight:700;flex-shrink:0"><?= $b['icon'] ?></span>
            <?= h($b['text']) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="<?= BASE_URL ?>/solucoes#automacao" class="btn btn-outline" style="margin-top:32px">
          Ver Como Funciona
        </a>
      </div>

      <div data-reveal data-reveal-delay="2">
        <div class="flow-visual">
          <div class="flow-nodes">
            <?php $nodes = [
              ['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>','Site'],['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>','Trigger'],['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>','IA'],['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>','E-mail'],['<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>','CRM'],
            ]; foreach ($nodes as $i => $n): ?>
            <div class="flow-node">
              <div class="fn-icon"><?= $n[0] ?></div>
              <div class="fn-label"><?= $n[1] ?></div>
            </div>
            <?php if ($i < count($nodes) - 1): ?>
            <div class="flow-arrow">
              <div class="flow-pulse" style="animation-delay:<?= $i * 0.4 ?>s"></div>
            </div>
            <?php endif; endforeach; ?>
          </div>
        </div>

        <!-- Mini metrics panel -->
        <div class="card" style="margin-top:24px;padding:20px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <span style="font-size:.8rem;color:var(--text-3);letter-spacing:.08em;text-transform:uppercase;font-weight:700">Automações Ativas</span>
            <span class="badge badge-green">● Ao vivo</span>
          </div>
          <?php $mock_flows = [
            ['Lead Form → CRM → WhatsApp', '1.247 exec.'],
            ['Proposta Enviada → Acompanhamento', '389 exec.'],
            ['Relatório Semanal → Google Sheets', '52 exec.'],
          ]; foreach ($mock_flows as $mf): ?>
          <div style="display:flex;justify-content:space-between;align-items:center;
                      padding:10px 0;border-bottom:1px solid var(--border);font-size:.85rem">
            <span style="color:var(--text-2)"><?= h($mf[0]) ?></span>
            <span style="color:var(--green);font-weight:600"><?= h($mf[1]) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ====================================================
     DEPOIMENTOS
===================================================== -->
<?php if (!empty($depos)): ?>
<section>
  <div class="container">
    <div class="section-header text-center" data-reveal>
      <div class="label">Prova social</div>
      <h2 class="display-3">O que nossos clientes dizem</h2>
    </div>
    <div class="grid-3">
      <?php foreach ($depos as $i => $d):
        // Strip HTML tags - latin1-safe, no encoding conversion
        $_dt = preg_replace('/<br\s*\/?>/i', ' ', $d['d_texto'] ?? '');
        $_dt = strip_tags($_dt);
        $_dt = str_replace(['\\r\\n','\\r\\n','\\r','\\n','\\t'], ' ', $_dt);
        $_dt = str_replace(["\r\n","\r","\n","\t"], ' ', $_dt);
        $texto_limpo = trim(preg_replace('/\s+/', ' ', $_dt));
        // Truncate to ~220 chars
        if (mb_strlen($texto_limpo) > 220) {
          $texto_limpo = mb_substr($texto_limpo, 0, 220);
          $texto_limpo = mb_substr($texto_limpo, 0, mb_strrpos($texto_limpo, ' ')) . '…';
        }
        $inicial = mb_strtoupper(mb_substr($d['d_nome'], 0, 1));
        $colors  = ['#2F6DAD','#4BA9E2','#F9A21D','#38A169','#805AD5','#E53E3E'];
        $cor     = $colors[$d['d_id'] % count($colors)];
      ?>
      <div class="card testimonial-card" data-reveal data-reveal-delay="<?= $i + 1 ?>"
           style="display:flex;flex-direction:column;justify-content:space-between;gap:20px;padding:28px">
        <!-- Stars -->
        <div style="color:#F9A21D;font-size:.9rem;letter-spacing:2px;margin-bottom:2px">★★★★★</div>
        <!-- Quote text -->
        <p style="font-size:.92rem;line-height:1.75;color:var(--text-2);flex:1;margin:0">
          "<?= h($texto_limpo) ?>"
        </p>
        <!-- Author -->
        <div style="display:flex;align-items:center;gap:12px;padding-top:16px;border-top:1px solid var(--border)">
          <div style="width:42px;height:42px;border-radius:50%;background:<?= $cor ?>;
                      display:flex;align-items:center;justify-content:center;
                      font-weight:700;color:#fff;font-size:1rem;flex-shrink:0">
            <?= $inicial ?>
          </div>
          <div>
            <div style="font-weight:700;font-size:.9rem"><?= h($d['d_nome']) ?></div>
            <?php if ($d['d_empresa']): ?>
            <div style="font-size:.78rem;color:var(--text-3)"><?= h($d['d_empresa']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ====================================================
     BLOG PREVIEW
===================================================== -->
<?php if (!empty($posts)): ?>
<section style="background:var(--bg-2);border-top:1px solid var(--border)">
  <div class="container">
    <div class="section-header" data-reveal style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:48px">
      <div>
        <div class="label" style="margin-bottom:12px">Blog</div>
        <h2 class="display-3">Insights que geram resultado</h2>
      </div>
      <a href="<?= BASE_URL ?>/blog" class="btn btn-outline" style="flex-shrink:0">Ver todos</a>
    </div>
    <div class="grid-3">
      <?php foreach ($posts as $i => $p):
        $slug_post = $p['p_slug'] ?: slug($p['p_titulo']); ?>
      <article class="post-card" data-reveal data-reveal-delay="<?= $i + 1 ?>">
        <div class="post-thumb">
          <?php if ($p['p_imagem']): ?>
          <img src="<?= MEDIA_URL . h($p['p_imagem']) ?>" onerror="this.onerror=null;this.src='https://aligator.com.br/media/posts/'+this.src.split('/').pop()"
               alt="<?= h($p['p_titulo']) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:var(--bg-3);
                      display:flex;align-items:center;justify-content:center;
                      color:var(--text-3);font-size:2rem">📝</div>
          <?php endif; ?>
        </div>
        <div class="post-meta">
          <span class="cat"><?= h($p['cat_titulo'] ?? 'Marketing') ?></span>
          <span><?= date('d/m/Y', strtotime($p['p_data'])) ?></span>
        </div>
        <h3><a href="<?= BASE_URL ?>/blog/<?= h($slug_post) ?>"><?= h($p['p_titulo']) ?></a></h3>
        <p class="excerpt"><?= h(mb_strimwidth(strip_tags($p['p_resumo'] ?? ''), 0, 100, '…')) ?></p>
        <a href="<?= BASE_URL ?>/blog/<?= h($slug_post) ?>" class="post-read">Ler artigo</a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ====================================================
     CTA FINAL
===================================================== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content" data-reveal>
      <div class="label" style="margin:0 auto 20px">Próximo passo</div>
      <h2 class="display-2">
        Pronto para crescer<br>com <span class="text-green">inteligência</span>?
      </h2>
      <p>
        Agende uma reunião estratégica gratuita. Em 30 minutos, mapeamos
        as oportunidades do seu negócio e mostramos um plano concreto de crescimento.
      </p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/contato" class="btn btn-primary btn-lg">Agendar Reunião Gratuita</a>
        <a href="https://wa.me/<?= SITE_WHATS ?>?text=Ol%C3%A1%2C+gostaria+de+saber+mais+sobre+as+solu%C3%A7%C3%B5es+da+Aligator."
           target="_blank" rel="noopener"
           class="btn btn-outline btn-lg">Falar no WhatsApp</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
