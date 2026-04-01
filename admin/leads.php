<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();

$base = BASE_URL . '/admin';

// Delete lead
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    db()->prepare('DELETE FROM leads WHERE l_id=?')->execute([(int)$_GET['delete']]);
    redirect("$base/leads.php?msg=deleted");
}
if (($_GET['msg']??'') === 'deleted') { /* msg shown below */ }

// Update status via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['lead_id'];
    $st = $_POST['novo_status'] ?? '';
    $allowed = ['novo','contato_feito','proposta','convertido','perdido'];
    if ($id && in_array($st, $allowed)) {
        db()->prepare('UPDATE leads SET l_status=? WHERE l_id=?')->execute([$st, $id]);
    }
    // Redirect back to same page preserving filters
    $qs = http_build_query(array_filter([
        'status' => $_POST['_status'] ?? '',
        'q'      => $_POST['_q'] ?? '',
        'p'      => $_POST['_p'] ?? '',
    ]));
    redirect("$base/leads.php" . ($qs ? "?$qs" : ''));
}

$filter_status = $_GET['status'] ?? '';
$filter_q      = trim($_GET['q'] ?? '');
$page    = max(1,(int)($_GET['p'] ?? 1));
$perPage = 25;
$offset  = ($page-1)*$perPage;

$where = '1=1'; $params = [];
if ($filter_status) { $where .= ' AND l_status=?'; $params[] = $filter_status; }
if ($filter_q) {
    $where .= ' AND (l_nome LIKE ? OR l_email LIKE ? OR l_empresa LIKE ?)';
    $like = "%$filter_q%";
    array_push($params, $like, $like, $like);
}

$stTotal = db()->prepare("SELECT COUNT(*) FROM leads WHERE $where");
$stTotal->execute($params);
$total = (int)$stTotal->fetchColumn();
$pages = ceil($total/$perPage);

$stLeads = db()->prepare("SELECT * FROM leads WHERE $where ORDER BY l_criacao DESC LIMIT $perPage OFFSET $offset");
$stLeads->execute($params);
$leads = $stLeads->fetchAll();

// Export CSV
if (isset($_GET['export'])) {
    $stAll = db()->prepare("SELECT * FROM leads WHERE $where ORDER BY l_criacao DESC");
    $stAll->execute($params);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="leads-'.date('Y-m-d').'.csv"');
    $fp = fopen('php://output','w');
    fputcsv($fp, ['ID','Nome','E-mail','Telefone','Empresa','Status','Fonte','Campanha','Data']);
    foreach ($stAll->fetchAll() as $l) {
        fputcsv($fp, [$l['l_id'],$l['l_nome'],$l['l_email'],$l['l_telefone'],$l['l_empresa'],$l['l_status'],$l['l_utm_source'],$l['l_utm_campaign'],date('d/m/Y H:i',strtotime($l['l_criacao']))]);
    }
    fclose($fp); exit;
}

$status_labels = ['novo'=>'Novo','contato_feito'=>'Contatado','proposta'=>'Proposta','convertido'=>'Convertido','perdido'=>'Perdido'];
$badge_map     = ['novo'=>'badge-blue','contato_feito'=>'badge-gold','proposta'=>'badge-gold','convertido'=>'badge-green','perdido'=>'badge-red'];
$admin_active  = 'leads';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Leads — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <span class="admin-topbar-title">Leads</span>
    <div class="admin-topbar-right">
      <a href="?<?= http_build_query(array_merge($_GET,['export'=>1])) ?>" class="btn btn-outline btn-sm">↓ Exportar CSV</a>
    </div>
  </div>
  <div class="admin-page">
    <div class="admin-page-title">Gerenciar Leads</div>
    <div class="admin-page-sub"><?= number_format($total) ?> lead(s) encontrado(s)</div>

    <!-- Filtros -->
    <form method="get" action="<?= $base ?>/leads.php" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
      <input type="text" name="q" class="form-control" placeholder="Nome, e-mail ou empresa..." value="<?= h($filter_q) ?>" style="max-width:280px">
      <select name="status" class="form-control" style="max-width:180px">
        <option value="">Todos os status</option>
        <?php foreach ($status_labels as $k=>$v): ?>
        <option value="<?= $k ?>" <?= $filter_status===$k?'selected':'' ?>><?= $v ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
      <?php if ($filter_status || $filter_q): ?>
      <a href="<?= $base ?>/leads.php" class="btn btn-ghost btn-sm">Limpar</a>
      <?php endif; ?>
    </form>

    <div class="admin-card" style="padding:0;overflow:hidden;margin-bottom:16px">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th><th>Nome / Empresa</th><th>Contato</th><th>Formulário</th>
            <th>Origem</th><th>Status</th><th>Data</th><th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($leads as $l): ?>
          <tr>
            <td style="color:var(--text-3)"><?= $l['l_id'] ?></td>
            <td>
              <div style="font-weight:600;color:var(--text)"><?= h($l['l_nome']) ?></div>
              <?php if($l['l_empresa']): ?><div style="font-size:.75rem;color:var(--text-3)"><?= h($l['l_empresa']) ?></div><?php endif; ?>
            </td>
            <td>
              <a href="mailto:<?= h($l['l_email']) ?>" style="font-size:.82rem"><?= h($l['l_email']) ?></a>
              <?php if($l['l_telefone']): ?>
              <br><a href="https://wa.me/55<?= preg_replace('/\D/','',$l['l_telefone']) ?>" target="_blank" style="font-size:.75rem;color:var(--text-3)"><?= h($l['l_telefone']) ?></a>
              <?php endif; ?>
            </td>
            <td style="font-size:.78rem"><?= h($l['l_form']) ?></td>
            <td style="font-size:.78rem">
              <?= h($l['l_utm_source']?:'—') ?>
              <?php if($l['l_utm_campaign']): ?><br><span style="color:var(--text-3)"><?= h(mb_strimwidth($l['l_utm_campaign'],0,20,'…')) ?></span><?php endif; ?>
            </td>
            <td>
              <form method="post" action="<?= $base ?>/leads.php">
                <input type="hidden" name="update_status" value="1">
                <input type="hidden" name="lead_id" value="<?= $l['l_id'] ?>">
                <input type="hidden" name="_status" value="<?= h($filter_status) ?>">
                <input type="hidden" name="_q" value="<?= h($filter_q) ?>">
                <input type="hidden" name="_p" value="<?= $page ?>">
                <select name="novo_status" class="form-control" style="font-size:.78rem;padding:4px 8px;width:auto" onchange="this.form.submit()">
                  <?php foreach ($status_labels as $k=>$v): ?>
                  <option value="<?= $k ?>" <?= $l['l_status']===$k?'selected':'' ?>><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td style="font-size:.75rem;color:var(--text-3)"><?= date('d/m/y H:i',strtotime($l['l_criacao'])) ?></td>
            <td>
              <div style="display:flex;gap:4px">
                <a href="mailto:<?= h($l['l_email']) ?>?subject=Re: Contato Aligator&body=Olá, <?= urlencode($l['l_nome']) ?>!" class="btn btn-ghost btn-sm" title="Responder e-mail">✉</a>
                <a href="?delete=<?= $l['l_id'] ?>&<?= http_build_query(array_filter(['status'=>$filter_status,'q'=>$filter_q,'p'=>$page])) ?>"
                   class="btn btn-sm" style="color:var(--red-err)" title="Excluir lead"
                   onclick="return confirm('Excluir este lead permanentemente?')">✕</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($leads)): ?>
          <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-3)">Nenhum lead encontrado.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
    <nav class="pagination">
      <?php if ($page > 1): ?><a href="?<?= http_build_query(array_merge($_GET,['p'=>$page-1])) ?>">‹</a><?php endif; ?>
      <?php for ($i = max(1,$page-2); $i <= min($pages,$page+2); $i++): ?>
      <?php if($i===$page): ?><span class="current"><?= $i ?></span>
      <?php else: ?><a href="?<?= http_build_query(array_merge($_GET,['p'=>$i])) ?>"><?= $i ?></a><?php endif; ?>
      <?php endfor; ?>
      <?php if ($page < $pages): ?><a href="?<?= http_build_query(array_merge($_GET,['p'=>$page+1])) ?>">›</a><?php endif; ?>
    </nav>
    <?php endif; ?>
  </div>
</main>
</div>
</body></html>
