<?php
/**
 * llms.php
 * Serve o arquivo llms.txt com conteúdo dinâmico (posts recentes)
 */
require __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=86400');

$posts = db()->query(
    "SELECT p_titulo, p_slug, p_resumo, p_data
     FROM posts
     WHERE p_ativo = 1 AND p_slug IS NOT NULL
     ORDER BY p_data DESC LIMIT 10"
)->fetchAll();
?>
# Aligator — Tech Marketing Company

> Aligator é uma empresa brasileira fundada em 2009, especializada em sistemas digitais, automação com IA, growth performance e SEO técnico.

## O que fazemos

- **Sistemas Personalizados**: Desenvolvimento de plataformas SaaS e Micro SaaS em PHP e MySQL
- **Automação & IA**: Fluxos automatizados com Make.com, APIs e Inteligência Artificial
- **Growth Performance**: Tráfego pago (Google Ads, Meta Ads) e CRO orientados por dados
- **SEO Técnico**: Arquitetura de informação, auditoria técnica e conteúdo evergreen
- **Soluções para Eventos**: Sistemas de ingressos, landing pages e marketing
- **Mentorias**: Acompanhamento estratégico individual e em grupo

## Páginas principais

- [Home](<?= SITE_URL ?>/)
- [Sobre](<?= SITE_URL ?>/sobre)
- [Soluções](<?= SITE_URL ?>/solucoes)
- [Mentoria](<?= SITE_URL ?>/mentoria)
- [Blog](<?= SITE_URL ?>/blog)
- [Contato](<?= SITE_URL ?>/contato)

<?php if (!empty($posts)): ?>
## Artigos recentes do Blog

<?php foreach ($posts as $p): ?>
- [<?= strip_tags($p['p_titulo']) ?>](<?= SITE_URL ?>/blog/<?= $p['p_slug'] ?>) — <?= date('Y-m-d', strtotime($p['p_data'])) ?>
<?php endforeach; ?>
<?php endif; ?>

## Contato

- Site: <?= SITE_URL ?>

- E-mail: <?= SITE_EMAIL ?>

- Localização: Santo André, SP — Brasil
- Atendimento: Segunda a sexta, 9h às 18h
