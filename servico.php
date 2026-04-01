<?php
/**
 * servico.php — Página individual de serviço
 * Rota: /sitenovo/sistemas, /sitenovo/automacao, etc.
 */
require __DIR__ . '/config.php';
$calendly_url = cfg('calendly_url', 'https://calendly.com/agenciaaligator');
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$slug = $_GET['slug'] ?? '';

$servicos = [
  'sistemas' => [
    'titulo'      => 'Sistemas Personalizados',
    'tag'         => 'SaaS · Micro SaaS · PHP',
    'seo_title'   => 'Sistemas Personalizados e SaaS sob medida — Aligator',
    'seo_desc'    => 'Desenvolvemos sistemas digitais, plataformas SaaS e Micro SaaS sob medida em PHP e MySQL. Da concepção ao deploy.',
    'hero_text'   => 'Sistemas que <em>escalam</em><br>com seu negócio',
    'intro'       => 'Desenvolvemos plataformas digitais sob medida — desde painéis administrativos até SaaS completos. Cada sistema é construído com PHP e MySQL, compatível com hospedagem compartilhada, com admin intuitivo, SEO integrado e escalabilidade planejada desde o início.',
    'icon'        => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'items'       => ['Plataformas SaaS e Micro SaaS','Painéis administrativos com CRUD','Sistemas de cadastro e gestão','Integração com APIs externas','E-commerce personalizado','Área de membros e portais'],
    'beneficios'  => [
      ['título'=>'100% personalizado','texto'=>'Nada de templates ou soluções prontas. Cada sistema é desenvolvido do zero para o seu processo específico.'],
      ['título'=>'Hospedagem simples','texto'=>'Compatível com cPanel, Locaweb e qualquer servidor PHP. Sem dependência de infraestrutura cara.'],
      ['título'=>'Admin completo','texto'=>'Painel administrativo intuitivo com gestão de conteúdo, relatórios e controle total para sua equipe.'],
      ['título'=>'SEO técnico nativo','texto'=>'URLs amigáveis, meta tags dinâmicas, sitemap e schema markup incluídos em todos os projetos.'],
    ],
    'faq' => [
      ['p'=>'Quanto tempo leva para desenvolver?','r'=>'Depende da complexidade. Um Micro SaaS simples pode ser entregue em 3–4 semanas. Sistemas maiores de 2–4 meses.'],
      ['p'=>'Preciso de servidor dedicado?','r'=>'Não. Nossos sistemas rodam em hospedagem compartilhada cPanel padrão, o que reduz significativamente o custo.'],
      ['p'=>'Como fica a manutenção?','r'=>'Entregamos o código-fonte completo e documentação. Podemos manter em contrato mensal ou treinar sua equipe.'],
    ],
  ],
  'automacao' => [
    'titulo'      => 'Automação & Inteligência Artificial',
    'tag'         => 'Make · API · OpenAI',
    'seo_title'   => 'Automação de Marketing e IA para empresas — Aligator',
    'seo_desc'    => 'Eliminamos tarefas manuais com automações Make.com, APIs e IA. Leads capturados, CRM alimentado e relatórios automáticos 24h.',
    'hero_text'   => 'Automações que<br><em>trabalham por você</em>',
    'intro'       => 'Eliminamos tarefas manuais e conectamos seus sistemas com automações inteligentes. Utilizamos Make.com, APIs REST, OpenAI e ferramentas complementares para criar fluxos que trabalham 24/7.',
    'icon'        => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
    'items'       => ['Automação de marketing (CRM, e-mail, WhatsApp)','Fluxos Make.com e n8n personalizados','Integração entre plataformas via API','IA para atendimento e qualificação de leads','Relatórios automáticos no Google Sheets','Chatbots e assistentes com LLMs'],
    'beneficios'  => [
      ['título'=>'Economia de tempo','texto'=>'Processos que levam horas viram minutos. Sua equipe foca no que realmente importa.'],
      ['título'=>'Zero código necessário','texto'=>'Usamos ferramentas visuais como Make.com. Você consegue manter e ajustar os fluxos sem programar.'],
      ['título'=>'Escalável','texto'=>'O mesmo fluxo atende 10 ou 10.000 leads. Sem custo adicional de equipe.'],
      ['título'=>'Integrado ao seu stack','texto'=>'Conectamos com CRM, ERP, e-mail marketing, WhatsApp Business API e qualquer plataforma com API.'],
    ],
    'faq' => [
      ['p'=>'Preciso conhecer tecnologia?','r'=>'Não. Entregamos tudo configurado e documentado. Treinamos sua equipe para monitorar os fluxos.'],
      ['p'=>'O que é Make.com?','r'=>'É uma plataforma visual de automação (similar ao Zapier) que conecta centenas de aplicativos sem código.'],
      ['p'=>'E se uma automação falhar?','r'=>'Configure alertas por e-mail ou WhatsApp. O Make.com registra todos os erros com detalhes para diagnóstico.'],
    ],
  ],
  'growth' => [
    'titulo'      => 'Growth Performance',
    'tag'         => 'Google Ads · Meta Ads · CRO',
    'seo_title'   => 'Gestão de Google Ads e Meta Ads com foco em ROI — Aligator',
    'seo_desc'    => 'Estratégias de tráfego pago com foco em CAC e ROI. Google Ads, Meta Ads e CRO para empresas que querem resultados mensuráveis.',
    'hero_text'   => 'Tráfego que<br><em>converte de verdade</em>',
    'intro'       => 'Estratégias de aquisição pagas com foco absoluto em CAC e ROI. Criamos e gerenciamos campanhas no Google, Meta e YouTube com acompanhamento de conversões, testes A/B e relatórios transparentes.',
    'icon'        => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
    'items'       => ['Google Ads (Search, Display, Shopping, YouTube)','Meta Ads (Facebook e Instagram)','Remarketing avançado e lookalike','Rastreamento e atribuição com GA4 + GTM','Otimização de landing pages (CRO)','Relatórios mensais com insights estratégicos'],
    'beneficios'  => [
      ['título'=>'Focado em resultado','texto'=>'Não gerenciamos cliques — gerenciamos CAC, ROAS e LTV. Cada decisão é baseada em dados.'],
      ['título'=>'Setup completo','texto'=>'Cuidamos de tudo: pixel, conversões, audiences, criativos e copies. Você aprova, nós executamos.'],
      ['título'=>'Relatório transparente','texto'=>'Dashboard em tempo real. Você vê exatamente onde cada real foi investido e qual retorno gerou.'],
      ['título'=>'Sem amarras','texto'=>'Sem fidelidade mínima. Ficamos porque os resultados justificam, não porque o contrato obriga.'],
    ],
    'faq' => [
      ['p'=>'Qual o investimento mínimo em mídia?','r'=>'Recomendamos R$ 3.000/mês em mídia para Google Ads e R$ 1.500/mês para Meta Ads como ponto de partida.'],
      ['p'=>'Em quanto tempo vejo resultados?','r'=>'Google Search costuma gerar resultados em 2–4 semanas. Meta Ads pode levar 4–6 semanas para otimizar.'],
      ['p'=>'Vocês criam os criativos?','r'=>'Sim. Entregamos textos, copies e orientação para arte. Para vídeo, parceiros de produção.'],
    ],
  ],
  'seo' => [
    'titulo'      => 'SEO Técnico',
    'tag'         => 'Técnico · Conteúdo · Autoridade',
    'seo_title'   => 'SEO Técnico e Estratégia de Conteúdo — Agência Aligator',
    'seo_desc'    => 'Auditoria técnica, arquitetura de informação e produção de conteúdo evergreen para posicionamento orgânico duradouro.',
    'hero_text'   => 'Visibilidade orgânica<br><em>construída para durar</em>',
    'intro'       => 'Visibilidade orgânica construída para durar. Realizamos auditorias técnicas completas, corrigimos problemas de rastreamento e indexação, e produzimos conteúdo evergreen que atrai visitantes qualificados mês após mês.',
    'icon'        => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>',
    'items'       => ['Auditoria técnica completa (Core Web Vitals)','Arquitetura de URLs e estrutura de informação','Produção de conteúdo evergreen','Link building estratégico','Schema markup e dados estruturados','SEO local para negócios regionais'],
    'beneficios'  => [
      ['título'=>'Resultados duradouros','texto'=>'Diferente de tráfego pago, o SEO gera audiência crescente que não para quando o orçamento acaba.'],
      ['título'=>'Autoridade de marca','texto'=>'Aparecer no topo das buscas do seu nicho posiciona sua empresa como referência no mercado.'],
      ['título'=>'Auditoria profunda','texto'=>'Identificamos todos os problemas técnicos que impedem seu site de ranquear — de velocidade a indexação.'],
      ['título'=>'Conteúdo estratégico','texto'=>'Produzimos artigos otimizados que respondem as dúvidas do seu cliente ideal e ranqueiam palavras-chave de valor.'],
    ],
    'faq' => [
      ['p'=>'Quanto tempo para ranquear?','r'=>'Para palavras competitivas, 4–8 meses. Para long-tail e palavras locais, às vezes 6–8 semanas.'],
      ['p'=>'SEO serve para qualquer negócio?','r'=>'Sim, especialmente negócios locais, profissionais liberais, e-commerce e SaaS B2B se beneficiam muito.'],
      ['p'=>'Vocês escrevem o conteúdo?','r'=>'Sim. Nossa equipe escreve, otimiza e publica. Você revisa e aprova antes da publicação.'],
    ],
  ],
  'eventos' => [
    'titulo'      => 'Soluções para Eventos',
    'tag'         => 'Ingressos · Marketing · Sistemas',
    'seo_title'   => 'Marketing Digital para Eventos — Ingressos e Divulgação | Aligator',
    'seo_desc'    => 'Sistemas de venda de ingressos, landing pages de alta conversão e estratégias de divulgação para eventos de todos os portes.',
    'hero_text'   => 'Eventos que<br><em>lotam e vendem</em>',
    'intro'       => 'Infraestrutura digital completa para eventos: sistemas de venda de ingressos, landing pages de alta conversão, estratégias de divulgação e gestão de presença online. Apoiamos dezenas de eventos desde festas locais até congressos.',
    'icon'        => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    'items'       => ['Sistemas customizados para organizadores de eventos','Landing pages com contador e urgência','Campanhas de divulgação (Google + Meta)','Web aplicativo personalizado','E-mail marketing pré e pós-evento','Relatórios de performance'],
    'beneficios'  => [
      ['título'=>'Venda online 24h','texto'=>'Sistema próprio de ingressos ou integração com plataformas existentes. Venda começa no dia do lançamento.'],
      ['título'=>'Landing page que converte','texto'=>'Páginas com countdown, prova social e urgência. Taxa de conversão acima de 8% em média.'],
      ['título'=>'Divulgação segmentada','texto'=>'Campanhas direcionadas para o público exato do seu evento, na cidade e perfil certos.'],
      ['título'=>'Check-in digital','texto'=>'App ou sistema web de check-in. Fila zero, controle total da entrada em tempo real.'],
    ],
    'faq' => [
      ['p'=>'Atendem eventos de qual porte?','r'=>'De 50 a 5.000+ pessoas. Temos experiência com festas, congressos, shows, feiras e formações.'],
      ['p'=>'Quanto tempo de antecedência?','r'=>'Ideal 45+ dias. Com 30 dias conseguimos entregar. Abaixo disso, consulte disponibilidade.'],
      ['p'=>'Integram com qual plataforma de ingresso?','r'=>'Sympla, Eventbrite, Ingresse, ou sistema próprio. Avaliamos o melhor para cada caso.'],
    ],
  ],
  'mentoria' => [
    'titulo'      => 'Mentoria Estratégica',
    'tag'         => 'Individual · Grupo · Intensiva',
    'seo_title'   => 'Mentoria em Marketing Digital — Aligator Agência',
    'seo_desc'    => 'Mentoria individual e em grupo para empreendedores e equipes de marketing que querem escalar com método, clareza e acompanhamento real.',
    'hero_text'   => 'Clareza e método<br><em>para crescer de verdade</em>',
    'intro'       => 'Para empreendedores e equipes que querem escalar com inteligência — sem adivinhar o próximo passo. Diagnóstico honesto, plano de ação personalizado e acompanhamento real.',
    'icon'        => '<svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
    'items'       => ['Diagnóstico do negócio e gargalos','Plano de ação 90 dias personalizado','Estratégias de crescimento validadas','Ferramentas e automações para cada fase','Suporte entre sessões via WhatsApp','Métricas e KPIs de acompanhamento'],
    'beneficios'  => [
      ['título'=>'Sem fórmulas mágicas','texto'=>'Diagnóstico honesto do seu momento atual. Sem promessas irreais, com plano concreto e executável.'],
      ['título'=>'Experiência real','texto'=>'15 anos desenvolvendo e executando estratégias digitais para empresas de diferentes segmentos e tamanhos.'],
      ['título'=>'Acompanhamento contínuo','texto'=>'Não é só uma reunião. Você tem suporte entre sessões para tirar dúvidas e ajustar a rota.'],
      ['título'=>'Resultado mensurável','texto'=>'Definimos KPIs no início. Acompanhamos números reais a cada sessão para garantir progresso.'],
    ],
    'faq' => [
      ['p'=>'Para quem é a mentoria?','r'=>'Empreendedores digitais, gestores de marketing, profissionais em transição e equipes de agências.'],
      ['p'=>'É presencial ou online?','r'=>'Online via Google Meet. Sessões presenciais em São Bernardo do Campo/SP sob consulta.'],
      ['p'=>'Qual a duração de cada sessão?','r'=>'Sessões avulsas: 60 min. Acompanhamento mensal: 4x sessões de 60 min + suporte WhatsApp.'],
    ],
  ],
];

if (!isset($servicos[$slug])) {
    http_response_code(404);
    require __DIR__ . '/includes/header.php';
    echo '<section style="min-height:60vh;display:flex;align-items:center;justify-content:center"><div class="container" style="text-align:center"><h1>Serviço não encontrado</h1><a href="' . BASE_URL . '/solucoes" class="btn btn-outline" style="margin-top:24px">Ver todas as soluções</a></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$s = $servicos[$slug];
$seo_title = $s['seo_title'];
$seo_desc  = $s['seo_desc'];

register_pageview("servico/$slug", $s['titulo']);
require __DIR__ . '/includes/header.php';
?>

<!-- Page header -->
<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <a href="<?= BASE_URL ?>/solucoes" style="font-size:.82rem;color:var(--text-3);display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;transition:color .2s"
       onmouseover="this.style.color='var(--blue-2)'" onmouseout="this.style.color='var(--text-3)'">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5m7-7l-7 7 7 7"/></svg>
      Todas as soluções
    </a>
    <div class="label" style="margin-bottom:16px"><?= h($s['tag']) ?></div>
    <h1 class="display-2" style="margin-bottom:16px"><?= $s['hero_text'] ?></h1>
    <p style="font-size:1.1rem;color:var(--text-2);max-width:580px"><?= h($s['intro']) ?></p>
  </div>
</div>

<!-- O que inclui -->
<section>
  <div class="container">
    <div class="servico-grid two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:flex-start">
      <div data-reveal>
        <div class="label" style="margin-bottom:20px">O que está incluído</div>
        <h2 class="display-3" style="margin-bottom:32px"><?= h($s['titulo']) ?></h2>
        <ul style="display:flex;flex-direction:column;gap:14px">
          <?php foreach ($s['items'] as $item): ?>
          <li style="display:flex;gap:12px;font-size:.95rem;color:var(--text-2)">
            <span style="color:var(--blue-2);flex-shrink:0;margin-top:2px">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
            </span>
            <?= h($item) ?>
          </li>
          <?php endforeach; ?>
        </ul>
        <div style="margin-top:36px;display:flex;gap:12px;flex-wrap:wrap">
          <a href="<?= BASE_URL ?>/contato?servico=<?= $slug ?>" class="btn btn-primary">
            Solicitar orçamento
          </a>
          <a href="https://wa.me/<?= SITE_WHATS ?>?text=Ol%C3%A1%2C+tenho+interesse+em+<?= urlencode($s['titulo']) ?>"
             target="_blank" rel="noopener" class="btn btn-outline">
            Falar agora
          </a>
        </div>
      </div>
      <div data-reveal data-reveal-delay="2">
        <div style="display:flex;flex-direction:column;gap:16px">
          <?php foreach ($s['beneficios'] as $i => $b): ?>
          <div class="card" style="padding:24px;display:flex;gap:16px">
            <div style="width:40px;height:40px;border-radius:var(--r);background:var(--blue-dim);
                        border:1px solid var(--border-2);display:flex;align-items:center;
                        justify-content:center;flex-shrink:0;color:var(--blue-2)">
              <?= $i + 1 ?>
            </div>
            <div>
              <div style="font-weight:700;margin-bottom:4px"><?= h($b['título']) ?></div>
              <div style="font-size:.88rem;color:var(--text-2)"><?= h($b['texto']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section style="background:var(--bg-2);border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
  <div class="container-s">
    <div class="section-header text-center" data-reveal>
      <div class="label">FAQ</div>
      <h2 class="display-3">Perguntas frequentes</h2>
    </div>
    <div style="display:flex;flex-direction:column;gap:16px">
      <?php foreach ($s['faq'] as $i => $faq): ?>
      <div class="card" data-reveal data-reveal-delay="<?= $i + 1 ?>">
        <div style="font-weight:700;margin-bottom:8px;display:flex;gap:12px;align-items:flex-start">
          <span style="color:var(--blue-2);flex-shrink:0">P:</span>
          <?= h($faq['p']) ?>
        </div>
        <div style="font-size:.9rem;color:var(--text-2);padding-left:24px"><?= h($faq['r']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Outros serviços -->
<section>
  <div class="container">
    <div class="section-header text-center" data-reveal>
      <div class="label">Explore mais</div>
      <h2 class="display-3">Outras soluções Aligator</h2>
    </div>
    <div class="grid-3">
      <?php foreach (array_filter(array_keys($servicos), fn($k) => $k !== $slug) as $i => $other_slug):
        if ($i >= 3) break;
        $other = $servicos[$other_slug]; ?>
      <a href="<?= BASE_URL ?>/<?= $other_slug ?>" class="card service-card" style="text-decoration:none" data-reveal data-reveal-delay="<?= $i + 1 ?>">
        <div class="service-icon" style="margin-bottom:16px"><?= $other['icon'] ?></div>
        <span class="service-tag"><?= h($other['tag']) ?></span>
        <h3 style="font-size:1.05rem;margin:6px 0 8px"><?= h($other['titulo']) ?></h3>
        <p style="font-size:.85rem;color:var(--text-2)"><?= h(mb_strimwidth($other['intro'], 0, 80, '…')) ?></p>
        <span class="service-link" style="margin-top:12px">Saiba mais</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section" style="padding:72px 0">
  <div class="container">
    <div class="cta-content" data-reveal>
      <div class="label" style="margin:0 auto 20px">Pronto para começar?</div>
      <h2 class="display-3">Vamos conversar sobre<br><span style="color:var(--blue-2)"><?= h($s['titulo']) ?></span>?</h2>
      <p style="margin-bottom:36px">Agende uma conversa de 30 minutos sem compromisso.</p>
      <div class="cta-actions">
        <a href="<?= BASE_URL ?>/contato?servico=<?= $slug ?>" class="btn btn-primary btn-lg">Solicitar proposta</a>
        <a href="<?= h($calendly_url) ?>" target="_blank" rel="noopener" class="btn btn-outline btn-lg">Agendar reunião</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
