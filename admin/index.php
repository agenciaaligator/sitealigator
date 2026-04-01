<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();
if (isset($_GET['logout'])) admin_logout();

$total_leads   = 0; $leads_mes = 0; $leads_novos = 0; $total_posts = 0; $total_views = 0;
try {
    $total_leads = (int)db()->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    $leads_mes   = (int)db()->query("SELECT COUNT(*) FROM leads WHERE MONTH(l_criacao)=MONTH(NOW()) AND YEAR(l_criacao)=YEAR(NOW())")->fetchColumn();
    $leads_novos = (int)db()->query("SELECT COUNT(*) FROM leads WHERE l_status='novo'")->fetchColumn();
    $total_posts = (int)db()->query('SELECT COUNT(*) FROM posts WHERE p_ativo=1')->fetchColumn();
    $total_views = (int)db()->query('SELECT COALESCE(SUM(pv_views),0) FROM page_views')->fetchColumn();
} catch(Exception $e){}

$recent_leads = [];
try { $recent_leads = db()->query("SELECT * FROM leads ORDER BY l_criacao DESC LIMIT 8")->fetchAll(); } catch(Exception $e){}
$top_pages = [];
try { $top_pages = db()->query('SELECT * FROM page_views ORDER BY pv_views DESC LIMIT 8')->fetchAll(); } catch(Exception $e){}

$status_labels = ['novo'=>'Novo','contato_feito'=>'Contatado','proposta'=>'Proposta','convertido'=>'Convertido','perdido'=>'Perdido'];
$badge_map     = ['novo'=>'badge-blue','contato_feito'=>'badge-gold','proposta'=>'badge-gold','convertido'=>'badge-green','perdido'=>'badge-red'];
$admin_active  = 'dashboard';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <span class="admin-topbar-title">Dashboard</span>
    <div class="admin-topbar-right">
      <span><?= date('d/m/Y') ?></span>
      <span>•</span>
      <span><?= h($_SESSION['admin_nome']) ?></span>
    </div>
  </div>
  <div class="admin-page">
    <div class="admin-page-title">Visão Geral</div>
    <div class="admin-page-sub">Acompanhe os indicadores do site.</div>

    <div class="admin-kpi-grid">
      <?php foreach ([
        ['💬','Total de Leads',     number_format($total_leads), ''],
        ['📅','Leads este mês',     number_format($leads_mes),   ''],
        ['🔔','Aguardando contato', number_format($leads_novos), 'color:#D69B1A'],
        ['📝','Posts publicados',   number_format($total_posts), ''],
      ] as $k): ?>
      <div class="admin-kpi">
        <div class="admin-kpi-icon"><?= $k[0] ?></div>
        <div class="admin-kpi-value" style="<?= $k[3] ?>"><?= $k[2] ?></div>
        <div class="admin-kpi-label"><?= $k[1] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:20px">
      <div class="admin-card" style="margin-bottom:0">
        <div class="admin-card-header">
          <span class="admin-card-title">Últimos Leads</span>
          <a href="<?= BASE_URL ?>/admin/leads.php" class="btn btn-outline btn-sm">Ver todos</a>
        </div>
        <table class="admin-table">
          <thead><tr><th>Nome</th><th>E-mail</th><th>Origem</th><th>Status</th><th>Data</th></tr></thead>
          <tbody>
            <?php foreach ($recent_leads as $l): ?>
            <tr>
              <td style="font-weight:600;color:var(--text)"><?= h($l['l_nome']) ?></td>
              <td><?= h($l['l_email']) ?></td>
              <td><?= h($l['l_utm_source'] ?: '—') ?></td>
              <td><span class="badge <?= $badge_map[$l['l_status']] ?? 'badge-gray' ?>"><?= $status_labels[$l['l_status']] ?? $l['l_status'] ?></span></td>
              <td><?= date('d/m H:i', strtotime($l['l_criacao'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($recent_leads)): ?><tr><td colspan="5" style="text-align:center;color:var(--text-3);padding:24px">Nenhum lead ainda.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="admin-card" style="margin-bottom:0">
        <div class="admin-card-header"><span class="admin-card-title">Páginas mais vistas</span></div>
        <table class="admin-table">
          <thead><tr><th>Página</th><th>Views</th></tr></thead>
          <tbody>
            <?php foreach ($top_pages as $pv): ?>
            <tr>
              <td><?= h($pv['pv_titulo'] ?: $pv['pv_slug']) ?></td>
              <td style="font-weight:600;color:var(--blue)"><?= number_format($pv['pv_views']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($top_pages)): ?><tr><td colspan="2" style="text-align:center;color:var(--text-3);padding:24px">Sem dados.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</div>
</body></html>
