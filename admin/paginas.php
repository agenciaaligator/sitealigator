<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();
$base = BASE_URL . '/admin';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$msg = ''; $error = '';

if ($action === 'delete' && $id) {
    db()->prepare('DELETE FROM cms_paginas WHERE pag_id=?')->execute([$id]);
    redirect("$base/paginas.php?msg=deleted");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $slug   = trim($_POST['slug']   ?? '');
    $texto  = $_POST['texto']        ?? '';
    $desc   = trim($_POST['description'] ?? '');
    $kw     = trim($_POST['keywords']    ?? '');
    if (!$titulo) $error = 'Título obrigatório.';
    if (!$error) {
        $slug_f = $slug ?: slug($titulo);
        if ($id) {
            db()->prepare('UPDATE cms_paginas SET pag_titulo=?,pag_slug=?,pag_texto=?,pag_description=?,pag_keywords=?,pag_alteracao=NOW() WHERE pag_id=?')
               ->execute([$titulo,$slug_f,$texto,$desc,$kw,$id]);
            $msg = 'Página atualizada.';
        } else {
            db()->prepare('INSERT INTO cms_paginas (pag_titulo,pag_slug,pag_texto,pag_description,pag_keywords,pag_criacao) VALUES(?,?,?,?,?,NOW())')
               ->execute([$titulo,$slug_f,$texto,$desc,$kw]);
            redirect("$base/paginas.php?msg=created");
        }
    }
}
$pagina = null;
if ($action === 'edit' && $id) {
    $st = db()->prepare('SELECT * FROM cms_paginas WHERE pag_id=?'); $st->execute([$id]);
    $pagina = $st->fetch();
    if (!$pagina) redirect("$base/paginas.php");
}
if (($_GET['msg']??'')==='created') $msg = 'Página criada.';
if (($_GET['msg']??'')==='deleted') $msg = 'Página removida.';
$paginas = db()->query('SELECT * FROM cms_paginas ORDER BY pag_criacao DESC')->fetchAll();
$admin_active = 'paginas';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Páginas — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <span class="admin-topbar-title"><?= $action==='list'?'Páginas CMS':'Editar Página' ?></span>
    <?php if($action==='list'): ?><a href="?action=new" class="btn btn-primary btn-sm">+ Nova Página</a>
    <?php else: ?><a href="<?= $base ?>/paginas.php" class="btn btn-ghost btn-sm">← Voltar</a><?php endif; ?>
  </div>
  <div class="admin-page">
    <?php if($msg): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <?php if ($action === 'list'): ?>
    <div class="admin-card" style="padding:0;overflow:hidden">
      <table class="admin-table">
        <thead><tr><th>Título</th><th>Slug</th><th>Atualizado</th><th>Ações</th></tr></thead>
        <tbody>
          <?php foreach ($paginas as $p): ?>
          <tr>
            <td style="font-weight:600;color:var(--text)"><?= h($p['pag_titulo']) ?></td>
            <td><a href="<?= BASE_URL ?>/<?= h($p['pag_slug']??'') ?>" target="_blank" style="font-size:.8rem"><?= h($p['pag_slug']??'—') ?> ↗</a></td>
            <td style="font-size:.75rem;color:var(--text-3)"><?= $p['pag_alteracao']?date('d/m/Y',strtotime($p['pag_alteracao'])):'—' ?></td>
            <td><div style="display:flex;gap:6px">
              <a href="?action=edit&id=<?= $p['pag_id'] ?>" class="btn btn-outline btn-sm">Editar</a>
              <a href="?action=delete&id=<?= $p['pag_id'] ?>" class="btn btn-sm" style="color:var(--red-err)" onclick="return confirm('Remover?')">✕</a>
            </div></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <form method="post" id="paginaForm" action="<?= $base ?>/paginas.php?action=<?= $action ?><?= $id?"&id=$id":'' ?>">
      <div style="display:grid;grid-template-columns:1fr 280px;gap:20px">
        <div>
          <div class="admin-card">
            <div class="form-group"><label>Título *</label><input type="text" name="titulo" class="form-control" required value="<?= h($pagina['pag_titulo']??'') ?>"></div>
            <div class="form-group"><label>Slug (URL)</label><input type="text" name="slug" class="form-control" value="<?= h($pagina['pag_slug']??'') ?>"></div>
          </div>
          <div class="admin-card">
            <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:10px">CONTEÚDO</label>
            <div id="editor"></div>
            <textarea id="texto" name="texto" style="display:none"></textarea>
          </div>
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">SEO</div>
            <div class="form-group"><label>Meta Description</label><textarea name="description" class="form-control" rows="2"><?= h($pagina['pag_description']??'') ?></textarea></div>
            <div class="form-group"><label>Keywords</label><input type="text" name="keywords" class="form-control" value="<?= h($pagina['pag_keywords']??'') ?>"></div>
          </div>
        </div>
        <div class="admin-card" style="align-self:flex-start">
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Salvar</button>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>
</main>
</div>
<script>
<?php
$_pag_texto = $pagina['pag_texto'] ?? '';
$_pag_json  = json_encode($_pag_texto, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE);
if ($_pag_json === false) $_pag_json = json_encode(utf8_encode($_pag_texto));
?>
const q = new Quill('#editor',{theme:'snow',modules:{toolbar:[[{header:[1,2,3,false]}],['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['blockquote','link'],['clean']]}});
const _pagContent = <?= $_pag_json ?? 'null' ?>;
if (_pagContent) { q.root.innerHTML = _pagContent; document.getElementById('texto').value = _pagContent; }
document.getElementById('paginaForm')?.addEventListener('submit',()=>{document.getElementById('texto').value=q.root.innerHTML;});
</script>
</body></html>
