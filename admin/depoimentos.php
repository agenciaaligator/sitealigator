<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();

// Clean depoimento HTML to plain text (latin1-safe, no encoding conversion)
function dep_text(string $raw): string {
    $t = preg_replace('/<br\s*\/?>/i', ' ', $raw);
    $t = strip_tags($t);
    // Replace both literal escape sequences and actual control chars
    $t = str_replace(['\\r\\n','\\r','\\n','\\t'], ' ', $t);
    $t = str_replace(["\r\n","\r","\n","\t"], ' ', $t);
    return trim(preg_replace('/\s+/', ' ', $t));
}
$base   = BASE_URL . '/admin';
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$msg    = ''; $error = '';

// Toggle ativo
if ($action === 'toggle' && $id) {
    $v = (int)$_GET['v'];
    db()->prepare('UPDATE depoimentos SET d_ativo=? WHERE d_id=?')->execute([$v, $id]);
    header('Location: ' . $base . '/depoimentos.php?msg=updated'); exit;
}

// Delete
if ($action === 'delete' && $id) {
    db()->prepare('DELETE FROM depoimentos WHERE d_id=?')->execute([$id]);
    header('Location: ' . $base . '/depoimentos.php?msg=deleted'); exit;
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = trim($_POST['nome']    ?? '');
    $empresa = trim($_POST['empresa'] ?? '');
    $url     = trim($_POST['url']     ?? '');
    $texto   = trim($_POST['texto']   ?? '');
    $ativo   = isset($_POST['ativo']) ? 1 : 0;
    $ordem   = (int)($_POST['ordem'] ?? 0);

    if (!$nome)  $error = 'Nome é obrigatório.';
    if (!$texto) $error = $error ?: 'Depoimento é obrigatório.';

    if (!$error) {
        if ($id) {
            db()->prepare('UPDATE depoimentos SET d_nome=?,d_empresa=?,d_url=?,d_texto=?,d_ativo=?,d_ordem=?,d_alteracao=NOW() WHERE d_id=?')
               ->execute([$nome, $empresa, $url, $texto, $ativo, $ordem, $id]);
            header('Location: ' . $base . '/depoimentos.php?msg=updated'); exit;
        } else {
            db()->prepare('INSERT INTO depoimentos (d_nome,d_empresa,d_url,d_texto,d_ativo,d_ordem,d_criacao) VALUES(?,?,?,?,?,?,NOW())')
               ->execute([$nome, $empresa, $url, $texto, $ativo, $ordem]);
            header('Location: ' . $base . '/depoimentos.php?msg=created'); exit;
        }
    }
}

// Load for edit
$dep = null;
if ($action === 'edit' && $id) {
    $st = db()->prepare('SELECT * FROM depoimentos WHERE d_id=?'); $st->execute([$id]);
    $dep = $st->fetch();
    if (!$dep) { header('Location: ' . $base . '/depoimentos.php'); exit; }
}
if (($_GET['msg'] ?? '') === 'created') $msg = 'Depoimento criado com sucesso.';
if (($_GET['msg'] ?? '') === 'updated') $msg = 'Status atualizado.';
if (($_GET['msg'] ?? '') === 'deleted') $msg = 'Depoimento removido.';

// List with search (only needed on list view)
$search = trim($_GET['q'] ?? '');
$deps   = [];
try {
    // Try with d_ordem column (requires migration)
    $order_sql = "CASE WHEN COALESCE(d_ordem,0)=0 THEN 9999 ELSE d_ordem END ASC, d_id DESC";
    if ($search) {
        $like = '%' . $search . '%';
        $st = db()->prepare("SELECT * FROM depoimentos WHERE d_nome LIKE ? OR d_empresa LIKE ? ORDER BY d_ativo DESC, $order_sql");
        $st->execute([$like, $like]);
        $deps = $st->fetchAll();
    } else {
        $deps = db()->query("SELECT * FROM depoimentos ORDER BY d_ativo DESC, $order_sql")->fetchAll();
    }
} catch (Exception $e) {
    // Fallback: d_ordem column may not exist yet
    try {
        if ($search) {
            $like = '%' . $search . '%';
            $st = db()->prepare("SELECT * FROM depoimentos WHERE d_nome LIKE ? OR d_empresa LIKE ? ORDER BY d_id DESC");
            $st->execute([$like, $like]);
            $deps = $st->fetchAll();
        } else {
            $deps = db()->query("SELECT * FROM depoimentos ORDER BY d_ativo DESC, d_id DESC")->fetchAll();
        }
    } catch (Exception $e2) {}
}

$admin_active = 'depoimentos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Depoimentos — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <span class="admin-topbar-title"><?= $action === 'list' ? 'Depoimentos' : ($action === 'new' ? 'Novo Depoimento' : 'Editar Depoimento') ?></span>
    <?php if ($action === 'list'): ?>
    <a href="?action=new" class="btn btn-primary btn-sm">+ Novo Depoimento</a>
    <?php else: ?>
    <a href="<?= $base ?>/depoimentos.php" class="btn btn-ghost btn-sm">← Voltar</a>
    <?php endif; ?>
  </div>
  <div class="admin-page">
    <?php if ($msg): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

    <?php if ($action === 'list'): ?>
    <div class="admin-page-title">Depoimentos</div>
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
      <div>
        <div class="admin-page-sub" style="margin:0"><?= count($deps) ?> depoimento(s)<?= $search ? ' para "<strong>'.h($search).'</strong>"' : '' ?></div>
        <small style="color:var(--text-3)">
          Os ativos com <strong>menor ordem</strong> aparecem primeiro. Ordem 0 vai para o final.
          Os <strong>3 primeiros ativos</strong> são exibidos na home.
        </small>
      </div>
      <form method="get" action="<?= $base ?>/depoimentos.php" style="display:flex;gap:8px">
        <input type="text" name="q" class="form-control" placeholder="Buscar por nome ou empresa..."
               value="<?= h($search) ?>" style="width:240px">
        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
        <?php if ($search): ?>
        <a href="<?= $base ?>/depoimentos.php" class="btn btn-ghost btn-sm">✕</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="admin-card" style="padding:0;overflow:hidden">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Ord.</th>
            <th>Cliente / Empresa</th>
            <th>Depoimento</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Count which active ones appear on home (top 3)
          $home_pos = 0;
          foreach ($deps as $d): 
            $preview  = mb_strimwidth(dep_text($d['d_texto'] ?? ''), 0, 90, '…');
            $is_home  = false;
            if ($d['d_ativo'] && $home_pos < 3) { $home_pos++; $is_home = true; }
          ?>
          <tr style="<?= $is_home ? 'background:rgba(47,109,173,.04)' : '' ?>">
            <td>
              <span style="font-size:.9rem;font-weight:700;color:<?= ((int)($d['d_ordem']??0)) > 0 ? 'var(--blue)' : 'var(--text-3)' ?>">
                <?= (int)($d['d_ordem'] ?? 0) === 0 ? '—' : (int)($d['d_ordem'] ?? 0) ?>
              </span>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <?php if ($is_home): ?>
                <span title="Aparece na home" style="font-size:.65rem;font-weight:700;background:#2F6DAD;color:#fff;padding:2px 6px;border-radius:10px">HOME <?= $home_pos ?></span>
                <?php endif; ?>
                <div>
                  <div style="font-weight:600;color:var(--text)"><?= h($d['d_nome']) ?></div>
                  <?php if ($d['d_empresa']): ?>
                  <div style="font-size:.75rem;color:var(--text-3)"><?= h($d['d_empresa']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td style="font-size:.82rem;color:var(--text-2);max-width:340px"><?= h($preview) ?></td>
            <td>
              <?php if ($d['d_ativo']): ?>
                <span class="badge badge-green">Ativo</span>
                <a href="?action=toggle&id=<?= $d['d_id'] ?>&v=0" class="btn btn-ghost btn-sm" style="margin-left:6px">Desativar</a>
              <?php else: ?>
                <span class="badge badge-gray">Inativo</span>
                <a href="?action=toggle&id=<?= $d['d_id'] ?>&v=1" class="btn btn-outline btn-sm" style="margin-left:6px">Ativar</a>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex;gap:6px">
                <a href="?action=edit&id=<?= $d['d_id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                <a href="?action=delete&id=<?= $d['d_id'] ?>" class="btn btn-sm" style="color:var(--red-err)"
                   onclick="return confirm('Excluir este depoimento permanentemente?')">✕</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($deps)): ?>
          <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-3)">Nenhum depoimento cadastrado.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php else: ?>
    <!-- Form -->
    <div class="admin-page-title"><?= $action === 'new' ? 'Novo Depoimento' : 'Editar: ' . h($dep['d_nome'] ?? '') ?></div>
    <form method="post" action="<?= $base ?>/depoimentos.php?action=<?= $action ?><?= $id ? "&id=$id" : '' ?>">
      <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:flex-start">
        <div>
          <div class="admin-card">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div class="form-group">
                <label>Nome do cliente *</label>
                <input type="text" name="nome" class="form-control" required
                       value="<?= h($dep['d_nome'] ?? '') ?>" placeholder="Ex: João Silva">
              </div>
              <div class="form-group">
                <label>Empresa / Cargo</label>
                <input type="text" name="empresa" class="form-control"
                       value="<?= h($dep['d_empresa'] ?? '') ?>" placeholder="Ex: CEO da Empresa X">
              </div>
            </div>
            <div class="form-group">
              <label>Site (opcional)</label>
              <input type="text" name="url" class="form-control"
                     value="<?= h($dep['d_url'] ?? '') ?>" placeholder="www.empresa.com.br">
            </div>
            <div class="form-group">
              <label>Depoimento * <small style="color:var(--text-3)">(máx. 300 caracteres para melhor exibição)</small></label>
              <textarea name="texto" class="form-control" rows="5" required
                        placeholder="Escreva o texto do depoimento em texto simples, sem HTML..."
                        oninput="document.getElementById('charCount').textContent=this.value.length"
                        ><?= h(dep_text($dep['d_texto'] ?? '')) ?></textarea>
              <small style="color:var(--text-3)"><span id="charCount"><?= mb_strlen(dep_text($dep['d_texto'] ?? '')) ?></span> caracteres</small>
            </div>
          </div>
        </div>
        <div>
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Configurações</div>
            <div class="form-group">
              <label>
                <input type="checkbox" name="ativo" value="1" <?= ($dep['d_ativo'] ?? 1) ? 'checked' : '' ?>>
                &nbsp;Ativo (exibir no site)
              </label>
            </div>
            <div class="form-group">
              <label>Ordem de exibição</label>
              <input type="number" name="ordem" class="form-control" min="0"
                     value="<?= (int)($dep['d_ordem'] ?? 0) ?>" placeholder="0 = primeiro">
              <small style="color:var(--text-3)">Menor número = aparece primeiro</small>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">
              Salvar depoimento
            </button>
          </div>
          <!-- Preview -->
          <div class="admin-card" style="margin-top:0">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Pré-visualização</div>
            <div style="background:var(--bg);border-radius:8px;padding:16px;border:1px solid var(--border)">
              <div style="color:#F9A21D;font-size:.8rem;margin-bottom:8px">★★★★★</div>
              <p style="font-size:.82rem;color:var(--text-2);line-height:1.6;margin-bottom:12px" id="previewText">
                "<?= h(mb_strimwidth(dep_text($dep['d_texto'] ?? ''), 0, 160, '…')) ?>"
              </p>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:34px;height:34px;border-radius:50%;background:#2F6DAD;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem" id="previewInitial">
                  <?= mb_strtoupper(mb_substr($dep['d_nome'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                  <div style="font-weight:700;font-size:.82rem" id="previewNome"><?= h($dep['d_nome'] ?? 'Nome do cliente') ?></div>
                  <div style="font-size:.72rem;color:var(--text-3)" id="previewEmpresa"><?= h($dep['d_empresa'] ?? 'Empresa') ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
    <script>
    // Live preview
    const nomeInput    = document.querySelector('input[name="nome"]');
    const empresaInput = document.querySelector('input[name="empresa"]');
    const textoArea    = document.querySelector('textarea[name="texto"]');
    function updatePreview() {
      const nome    = nomeInput?.value || 'Nome do cliente';
      const empresa = empresaInput?.value || '';
      const texto   = textoArea?.value || '';
      const trunc   = texto.length > 160 ? texto.substr(0, 160) + '…' : texto;
      document.getElementById('previewNome').textContent    = nome;
      document.getElementById('previewEmpresa').textContent = empresa;
      document.getElementById('previewText').textContent    = '"' + trunc + '"';
      document.getElementById('previewInitial').textContent = nome.charAt(0).toUpperCase();
    }
    nomeInput?.addEventListener('input', updatePreview);
    empresaInput?.addEventListener('input', updatePreview);
    textoArea?.addEventListener('input', updatePreview);
    </script>
    <?php endif; ?>
  </div>
</main>
</div>
</body>
</html>
