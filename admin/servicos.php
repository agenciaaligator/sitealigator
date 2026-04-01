<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();
$base   = BASE_URL . '/admin';
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$msg    = ''; $error = '';

// Toggle visível
if ($action === 'toggle' && $id) {
    $vis = (int)$_GET['v'];
    db()->prepare('UPDATE servicos SET s_visivel=? WHERE s_id=?')->execute([$vis, $id]);
    redirect("$base/servicos.php?msg=updated");
}
// DELETE
if ($action === 'delete' && $id) {
    db()->prepare('DELETE FROM servicos WHERE s_id=?')->execute([$id]);
    redirect("$base/servicos.php?msg=deleted");
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo   = trim($_POST['titulo']   ?? '');
    $slug     = trim($_POST['slug']     ?? '');
    $menu     = trim($_POST['menu']     ?? '');
    $resumo   = $_POST['resumo']         ?? '';
    $texto    = $_POST['texto']          ?? '';
    $title    = trim($_POST['title_seo'] ?? '');
    $desc     = trim($_POST['desc_seo']  ?? '');
    $visivel  = isset($_POST['visivel']) ? 1 : 0;
    $ordem    = (int)($_POST['ordem']   ?? 0);

    if (!$titulo) $error = 'Título obrigatório.';
    if (!$error) {
        $slug_f = $slug ?: slug($titulo);
        if ($id) {
            db()->prepare('UPDATE servicos SET s_titulo=?,s_slug=?,s_menu=?,s_resumo=?,s_texto=?,s_title=?,s_description=?,s_visivel=?,s_ordem=?,s_alteracao=NOW() WHERE s_id=?')
               ->execute([$titulo,$slug_f,$menu,$resumo,$texto,$title,$desc,$visivel,$ordem,$id]);
            $msg = 'Serviço atualizado.';
        } else {
            db()->prepare('INSERT INTO servicos (s_titulo,s_slug,s_menu,s_resumo,s_texto,s_title,s_description,s_visivel,s_ordem,s_criacao) VALUES(?,?,?,?,?,?,?,?,?,NOW())')
               ->execute([$titulo,$slug_f,$menu,$resumo,$texto,$title,$desc,$visivel,$ordem]);
            redirect("$base/servicos.php?msg=created");
        }
    }
}

$servico = null;
if (in_array($action,['edit']) && $id) {
    $st = db()->prepare('SELECT * FROM servicos WHERE s_id=?'); $st->execute([$id]);
    $servico = $st->fetch();
    if (!$servico) redirect("$base/servicos.php");
}
if (($_GET['msg']??'')==='created') $msg = 'Serviço criado.';
if (($_GET['msg']??'')==='updated') $msg = 'Status atualizado.';
if (($_GET['msg']??'')==='deleted') $msg = 'Serviço removido.';

$servicos = [];
try {
    $servicos = db()->query('SELECT * FROM servicos ORDER BY s_ordem,s_id')->fetchAll();
} catch(Exception $e){}

$admin_active = 'servicos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Serviços — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <span class="admin-topbar-title"><?= $action==='list'?'Serviços':'Editar Serviço' ?></span>
    <?php if ($action==='list'): ?>
    <a href="?action=new" class="btn btn-primary btn-sm">+ Novo Serviço</a>
    <?php else: ?>
    <a href="<?= $base ?>/servicos.php" class="btn btn-ghost btn-sm">← Voltar</a>
    <?php endif; ?>
  </div>
  <div class="admin-page">
    <?php if ($msg): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

    <?php if ($action === 'list'): ?>
    <div class="admin-page-title">Serviços</div>
    <div class="admin-page-sub">Gerencie os serviços exibidos no site. Arraste para reordenar (em breve).</div>
    <div class="admin-card" style="padding:0;overflow:hidden">
      <table class="admin-table">
        <thead><tr><th>Ord.</th><th>Título</th><th>Slug</th><th>Status</th><th>Ações</th></tr></thead>
        <tbody>
          <?php foreach ($servicos as $s): ?>
          <tr>
            <td style="color:var(--text-3)"><?= (int)($s['s_ordem']??0) ?></td>
            <td style="font-weight:600;color:var(--text)"><?= h($s['s_titulo']) ?></td>
            <td style="font-size:.78rem;color:var(--text-3)"><?= h($s['s_slug']??'') ?></td>
            <td>
              <?php if ($s['s_visivel']): ?>
              <span class="badge badge-green">Visível</span>
              <a href="?action=toggle&id=<?= $s['s_id'] ?>&v=0" class="btn btn-ghost btn-sm" style="margin-left:6px">Ocultar</a>
              <?php else: ?>
              <span class="badge badge-gray">Oculto</span>
              <a href="?action=toggle&id=<?= $s['s_id'] ?>&v=1" class="btn btn-outline btn-sm" style="margin-left:6px">Ativar</a>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="?action=edit&id=<?= $s['s_id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                <a href="?action=delete&id=<?= $s['s_id'] ?>" class="btn btn-sm" style="color:var(--red-err)" onclick="return confirm('Remover serviço?')">✕</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($servicos)): ?><tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text-3)">Nenhum serviço.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php else: ?>
    <div class="admin-page-title"><?= $action==='new'?'Novo Serviço':'Editar: '.h($servico['s_titulo']??'') ?></div>
    <form method="post" action="<?= $base ?>/servicos.php?action=<?= $action ?><?= $id?"&id=$id":'' ?>">
      <div style="display:grid;grid-template-columns:1fr 280px;gap:20px">
        <div>
          <div class="admin-card">
            <div class="form-group">
              <label>Título *</label>
              <input type="text" name="titulo" class="form-control" required value="<?= h($servico['s_titulo']??'') ?>">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div class="form-group">
                <label>Slug (URL)</label>
                <input type="text" name="slug" class="form-control" value="<?= h($servico['s_slug']??'') ?>">
              </div>
              <div class="form-group">
                <label>Item de menu</label>
                <input type="text" name="menu" class="form-control" placeholder="Ex: SEO" value="<?= h($servico['s_menu']??'') ?>">
              </div>
            </div>
            <div class="form-group">
              <label>Resumo (aparece nas listagens)</label>
              <textarea name="resumo" class="form-control" rows="3"><?= h($servico['s_resumo']??'') ?></textarea>
            </div>
            <div class="form-group">
              <label>Descrição completa</label>
              <textarea name="texto" class="form-control" rows="6"><?= h(strip_tags($servico['s_texto']??'')) ?></textarea>
            </div>
          </div>
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px">SEO</div>
            <div class="form-group">
              <label>Title Tag</label>
              <input type="text" name="title_seo" class="form-control" value="<?= h($servico['s_title']??'') ?>">
            </div>
            <div class="form-group">
              <label>Meta Description</label>
              <textarea name="desc_seo" class="form-control" rows="2"><?= h($servico['s_description']??'') ?></textarea>
            </div>
          </div>
        </div>
        <div>
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px">Configurações</div>
            <div class="form-group">
              <label><input type="checkbox" name="visivel" value="1" <?= ($servico['s_visivel']??1)?'checked':'' ?>>&nbsp; Visível no site</label>
            </div>
            <div class="form-group">
              <label>Ordem de exibição</label>
              <input type="number" name="ordem" class="form-control" min="0" value="<?= (int)($servico['s_ordem']??0) ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Salvar</button>
          </div>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>
</main>
</div>
</body></html>
