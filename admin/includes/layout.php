<?php
/**
 * Admin layout helper - shared sidebar + topbar
 * Usage: 
 *   $admin_page_title = 'Título';
 *   $admin_active = 'leads';
 *   require __DIR__ . '/layout.php';
 */
$logo_url = 'https://aligator.com.br/media/aligator.png';
$admin_nome = $_SESSION['admin_nome'] ?? 'Admin';
$leads_novos = 0;
try {
    $leads_novos = (int) db()->query("SELECT COUNT(*) FROM leads WHERE l_status='novo'")->fetchColumn();
} catch (Throwable $e) {
    // leads table may not exist yet
}

function admin_nav_link(string $href, string $label, string $icon_svg, string $active_key, string $current): string {
    $active = ($current === $active_key) ? ' active' : '';
    return "<a href=\"{$href}\" class=\"{$active}\">{$icon_svg} {$label}</a>";
}
$base = BASE_URL . '/admin';

$ico = [
    'dashboard' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
    'leads'     => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    'blog'      => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    'servicos'  => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>',
    'paginas'   => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
    'config'    => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
    'site'      => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
    'logout'    => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
];
$cur = $admin_active ?? '';
?>
<!-- Mobile sidebar toggle -->
<button class="admin-menu-btn" id="adminMenuBtn" aria-label="Menu">
  <span></span><span></span><span></span>
</button>
<div class="admin-sidebar-overlay" id="adminOverlay"></div>

<div class="admin-sidebar" id="adminSidebar">
  <div class="admin-sidebar-logo">
    <a href="<?= BASE_URL ?>/" target="_blank">
      <img src="<?= $logo_url ?>" alt="Aligator"
           onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
      <span class="logo-fallback" style="display:none">Ali<span>gator</span></span>
    </a>
  </div>
  <nav class="admin-nav">
    <div class="admin-nav-section">Principal</div>
    <?= admin_nav_link("$base/", 'Dashboard', $ico['dashboard'], 'dashboard', $cur) ?>
    <a href="<?= $base ?>/leads.php" class="<?= $cur==='leads'?'active':'' ?>">
      <?= $ico['leads'] ?> Leads
      <?php if ($leads_novos): ?><span class="badge"><?= $leads_novos ?></span><?php endif; ?>
    </a>

    <div class="admin-nav-section">Conteúdo</div>
    <?= admin_nav_link("$base/blog.php", 'Blog / Posts', $ico['blog'], 'blog', $cur) ?>
    <?= admin_nav_link("$base/servicos.php", 'Serviços', $ico['servicos'], 'servicos', $cur) ?>
    <?= admin_nav_link("$base/paginas.php", 'Páginas CMS', $ico['paginas'], 'paginas', $cur) ?>
    <?= admin_nav_link("$base/depoimentos.php", 'Depoimentos', '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>', 'depoimentos', $cur) ?>

    <div class="admin-nav-section">Sistema</div>
    <?= admin_nav_link("$base/configuracoes.php", 'Configurações', $ico['config'], 'config', $cur) ?>
    <a href="<?= BASE_URL ?>/" target="_blank"><?= $ico['site'] ?> Ver Site</a>
    <a href="<?= $base ?>/?logout=1" style="color:rgba(255,100,100,.7)!important"><?= $ico['logout'] ?> Sair</a>
  </nav>
  <div class="admin-sidebar-footer">
    Aligator Admin v2.0
  </div>
</div>
<script>
(function() {
  var btn     = document.getElementById('adminMenuBtn');
  var sidebar = document.getElementById('adminSidebar');
  var overlay = document.getElementById('adminOverlay');
  if (!btn || !sidebar) return;

  function openMenu() {
    btn.classList.add('open');
    sidebar.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    btn.classList.remove('open');
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }
  btn.addEventListener('click', function() {
    sidebar.classList.contains('open') ? closeMenu() : openMenu();
  });
  overlay.addEventListener('click', closeMenu);
  // Close on nav link click
  sidebar.querySelectorAll('a').forEach(function(a) {
    a.addEventListener('click', closeMenu);
  });
})();
</script>
