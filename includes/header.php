<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
    require BASE_PATH . '/config.php';
}
$_bp = BASE_URL;
// Use page-specific SEO, fallback to DB config, then default
$og_image   = $seo_og_image ?? (SITE_URL . '/media/og-default.jpg');
$_db_title  = ''; try { $_db_title = cfg('META_TITLE'); } catch(Exception $e){}
$_db_desc   = ''; try { $_db_desc  = cfg('META_DESCRIPTION'); } catch(Exception $e){}
$meta_title = ($seo_title ?? ($_db_title ?: 'Aligator')) . ' — Tech Marketing Company';
$meta_desc  = $seo_desc ?? ($_db_desc ?: 'Tech Marketing Company. Sistemas digitais, automação com IA e crescimento previsível. Desde 2009.');
$canonical  = SITE_URL . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$gtm = ''; try { $gtm = cfg('gtm_id'); } catch(Exception $e){}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="base-url" content="<?= BASE_URL ?>">
<title><?= h($meta_title) ?></title>
<meta name="description" content="<?= h($meta_desc) ?>">
<link rel="canonical" href="<?= h($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= h($canonical) ?>">
<meta property="og:title" content="<?= h($meta_title) ?>">
<meta property="og:description" content="<?= h($meta_desc) ?>">
<meta property="og:image" content="<?= h($og_image) ?>">
<meta property="og:locale" content="pt_BR">
<meta property="og:site_name" content="Aligator">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= h($meta_title) ?>">
<meta name="twitter:description" content="<?= h($meta_desc) ?>">
<meta name="twitter:image" content="<?= h($og_image) ?>">
<script type="application/ld+json">{"@context":"https://schema.org","@type":"MarketingAgency","name":"Aligator","url":"<?= SITE_URL ?>","description":"Tech Marketing Company. Sistemas digitais e crescimento previsível.","foundingDate":"2009","address":{"@type":"PostalAddress","addressLocality":"São Bernardo do Campo","addressRegion":"SP","addressCountry":"BR"},"contactPoint":{"@type":"ContactPoint","contactType":"customer service","email":"<?= SITE_EMAIL ?>","availableLanguage":"pt-BR"},"sameAs":["https://instagram.com/agenciaaligator"]}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="<?= $_bp ?>/css/style.css">
<?php if(isset($extra_head)) echo $extra_head; ?>
<?php if($gtm): ?><script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= h($gtm) ?>');</script><?php endif; ?>
</head>
<body class="<?= h($body_class ?? '') ?>">

<nav id="navbar" role="navigation" aria-label="Menu principal">
  <div class="nav-inner">
    <a href="<?= $_bp ?>/" class="nav-logo-link" aria-label="Aligator">
      <img src="https://aligator.com.br/media/aligator.png"
           alt="Aligator" height="40"
           style="height:40px;width:auto;display:block"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <span class="nav-logo" style="display:none">
        <span style="color:var(--blue-2)">Ali</span>gator
      </span>
    </a>
    <ul class="nav-links" role="list">
      <li><a href="<?= $_bp ?>/">Home</a></li>
      <li><a href="<?= $_bp ?>/sobre">Sobre</a></li>
      <li><a href="<?= $_bp ?>/solucoes">Soluções</a></li>
      <li><a href="<?= $_bp ?>/mentoria">Mentoria</a></li>
      <li><a href="<?= $_bp ?>/blog">Blog</a></li>
      <li><a href="<?= $_bp ?>/contato">Contato</a></li>
    </ul>
    <div class="nav-actions">
      <a href="<?= $_bp ?>/contato" class="btn btn-primary btn-sm">Agendar Reunião</a>
      <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>
<div id="mobileMenu" class="mobile-menu" role="dialog" aria-label="Menu mobile">
  <a href="<?= $_bp ?>/">Home</a>
  <a href="<?= $_bp ?>/sobre">Sobre</a>
  <a href="<?= $_bp ?>/solucoes">Soluções</a>
  <a href="<?= $_bp ?>/mentoria">Mentoria</a>
  <a href="<?= $_bp ?>/blog">Blog</a>
  <a href="<?= $_bp ?>/contato">Contato</a>
  <a href="<?= $_bp ?>/contato" class="btn btn-primary" style="margin-top:16px">Agendar Reunião</a>
</div>
