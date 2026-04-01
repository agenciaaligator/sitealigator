<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();

$base   = BASE_URL . '/admin';
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$msg    = '';
$error  = '';

// Categories
$categorias = [];
try {
    $categorias = db()->query('SELECT * FROM posts_categorias ORDER BY pc_titulo')->fetchAll();
} catch (Exception $e) {}

// DELETE
if ($action === 'delete' && $id) {
    try { db()->prepare('DELETE FROM posts WHERE p_id=?')->execute([$id]); } catch(Exception $e){}
    header('Location: ' . $base . '/blog.php?msg=deleted');
    exit;
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']   ?? '');
    $slug_in   = trim($_POST['slug']     ?? '');
    $resumo    = trim($_POST['resumo']   ?? '');
    $texto     = $_POST['texto'] ?? '';
    $categoria = (int)($_POST['categoria'] ?? 0);
    $data_pub  = $_POST['data']           ?? date('Y-m-d H:i:s');
    $title_seo = trim($_POST['title_seo']      ?? '');
    $desc_seo  = trim($_POST['description_seo'] ?? '');
    $destaque  = isset($_POST['destaque']) ? 1 : 0;
    $ativo     = isset($_POST['ativo'])    ? 1 : 0;

    if (!$titulo) $error = 'Título é obrigatório.';

    if (!$error) {
        $slug_final = $slug_in ?: slug($titulo);
        $imagem = null;

        if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                $filename   = bin2hex(random_bytes(8)) . '.' . $ext;
                // __DIR__ = .../sitenovo/admin
                // dirname(__DIR__) = .../sitenovo
                // dirname(dirname(__DIR__)) = raiz do domínio (onde fica /media/)
                $upload_dir = dirname(dirname(__DIR__)) . '/media/posts/';
                if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $upload_dir . $filename)) {
                    $imagem = $filename;
                } else {
                    $error = 'Erro ao salvar imagem. Caminho tentado: ' . $upload_dir;
                }
            }
        }

        try {
            if ($id) {
                $sql  = 'UPDATE posts SET p_titulo=?,p_slug=?,p_resumo=?,p_texto=?,p_categoria=?,p_data=?,p_title=?,p_description=?,p_destaque=?,p_ativo=?,p_alteracao=NOW()';
                $args = [$titulo,$slug_final,$resumo,$texto,$categoria,$data_pub,$title_seo,$desc_seo,$destaque,$ativo];
                if ($imagem) { $sql .= ',p_imagem=?'; $args[] = $imagem; }
                $sql .= ' WHERE p_id=?'; $args[] = $id;
                db()->prepare($sql)->execute($args);
                $msg = 'Post atualizado com sucesso.';
            } else {
                db()->prepare(
                    'INSERT INTO posts (p_titulo,p_slug,p_resumo,p_texto,p_categoria,p_data,p_title,p_description,p_destaque,p_ativo,p_imagem,p_criacao)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())'
                )->execute([$titulo,$slug_final,$resumo,$texto,$categoria,$data_pub,$title_seo,$desc_seo,$destaque,$ativo,$imagem]);
                $new_id = (int)db()->lastInsertId();
                header("Location: $base/blog.php?msg=created"); exit;
            }
        } catch (Exception $e) {
            $error = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

// Load post for edit
$post = [];
if ($action === 'edit' && $id) {
    try {
        $st = db()->prepare('SELECT * FROM posts WHERE p_id=?');
        $st->execute([$id]);
        $post = $st->fetch() ?: [];
    } catch (Exception $e) { $post = []; }
    if (!$post) redirect("$base/blog.php");
}

// Messages from redirect
if (($_GET['msg'] ?? '') === 'created') $msg = 'Post criado com sucesso.';
if (($_GET['msg'] ?? '') === 'deleted') $msg = 'Post removido.';

// List all posts with search
$search  = trim($_GET['q'] ?? '');
$page_n  = max(1, (int)($_GET['p'] ?? 1));
$per_p   = 20;
$offset  = ($page_n - 1) * $per_p;
$total   = 0;
$posts   = [];
try {
    if ($search) {
        $like = '%' . $search . '%';
        $stc  = db()->prepare('SELECT COUNT(*) FROM posts WHERE p_titulo LIKE ? OR p_slug LIKE ?');
        $stc->execute([$like, $like]);
        $total = (int)$stc->fetchColumn();
        $stp   = db()->prepare(
            "SELECT p.*,pc.pc_titulo as cat_titulo
             FROM posts p LEFT JOIN posts_categorias pc ON p.p_categoria=pc.pc_id
             WHERE p.p_titulo LIKE ? OR p.p_slug LIKE ?
             ORDER BY p.p_criacao DESC LIMIT $per_p OFFSET $offset"
        );
        $stp->execute([$like, $like]);
        $posts = $stp->fetchAll();
    } else {
        $total = (int)db()->query('SELECT COUNT(*) FROM posts')->fetchColumn();
        $posts = db()->query(
            "SELECT p.*,pc.pc_titulo as cat_titulo
             FROM posts p
             LEFT JOIN posts_categorias pc ON p.p_categoria=pc.pc_id
             ORDER BY p.p_criacao DESC
             LIMIT $per_p OFFSET $offset"
        )->fetchAll();
    }
} catch (Exception $e) {}

$admin_active = 'blog';

// Safe content for JS (avoid PHP outputting HTML that breaks JS)
$post_content_js = 'null';
if (!empty($post['p_texto'])) {
    // Ensure valid UTF-8 before json_encode (latin1 bytes may have invalid sequences)
    $_ptexto = mb_convert_encoding($post['p_texto'], 'UTF-8', 'UTF-8');
    if (!$_ptexto) $_ptexto = utf8_encode($post['p_texto']);
    $post_content_js = json_encode($_ptexto, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($post_content_js === false) $post_content_js = json_encode(utf8_encode($post['p_texto']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Blog — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">

  <div class="admin-topbar">
    <span class="admin-topbar-title">
      <?= $action === 'list' ? 'Blog / Posts' : ($action === 'new' ? 'Novo Post' : 'Editar Post') ?>
    </span>
    <?php if ($action === 'list'): ?>
      <a href="?action=new" class="btn btn-primary btn-sm">+ Novo Post</a>
    <?php else: ?>
      <a href="<?= $base ?>/blog.php" class="btn btn-ghost btn-sm">← Voltar à lista</a>
    <?php endif; ?>
  </div>

  <div class="admin-page">
    <?php if ($msg):   ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>

    <?php if ($action === 'list'): ?>
    <!-- ═══ LISTA ═══════════════════════════════════════════ -->
    <div class="admin-page-title">Posts do Blog</div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
      <div class="admin-page-sub" style="margin:0"><?= number_format($total) ?> post(s)<?= $search ? ' para "<strong>'.h($search).'</strong>"' : '' ?></div>
      <form method="get" action="<?= $base ?>/blog.php" style="display:flex;gap:8px">
        <input type="text" name="q" class="form-control" placeholder="Buscar por título ou slug..."
               value="<?= h($search) ?>" style="width:280px">
        <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
        <?php if ($search): ?>
        <a href="<?= $base ?>/blog.php" class="btn btn-ghost btn-sm">✕ Limpar</a>
        <?php endif; ?>
      </form>
    </div>
    <div class="admin-card" style="padding:0;overflow:hidden">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Título / Slug</th>
            <th>Categoria</th>
            <th>Views</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $p): ?>
          <tr>
            <td style="color:var(--text-3)"><?= $p['p_id'] ?></td>
            <td>
              <div style="font-weight:600;color:var(--text)"><?= h($p['p_titulo']) ?></div>
              <?php if ($p['p_slug'] ?? ''): ?>
              <div style="font-size:.7rem;color:var(--text-3)">/blog/<?= h($p['p_slug']) ?></div>
              <?php endif; ?>
            </td>
            <td style="font-size:.82rem"><?= h($p['cat_titulo'] ?? '—') ?></td>
            <td><?= number_format((int)$p['p_views']) ?></td>
            <td>
              <span class="badge <?= ($p['p_ativo'] ?? 0) ? 'badge-green' : 'badge-gray' ?>">
                <?= ($p['p_ativo'] ?? 0) ? 'Publicado' : 'Rascunho' ?>
              </span>
              <?php if ($p['p_destaque'] ?? 0): ?>
              <span class="badge badge-gold" style="margin-left:4px">Destaque</span>
              <?php endif; ?>
            </td>
            <td style="font-size:.75rem;color:var(--text-3)"><?= date('d/m/Y', strtotime($p['p_data'])) ?></td>
            <td>
              <div style="display:flex;gap:6px;flex-wrap:nowrap">
                <a href="?action=edit&id=<?= $p['p_id'] ?>" class="btn btn-outline btn-sm">Editar</a>
                <?php if ($p['p_slug'] ?? ''): ?>
                <a href="<?= BASE_URL ?>/blog/<?= h($p['p_slug']) ?>" target="_blank" class="btn btn-ghost btn-sm" title="Ver no site">↗</a>
                <?php endif; ?>
                <a href="?action=delete&id=<?= $p['p_id'] ?>"
                   class="btn btn-sm" style="color:var(--red-err)"
                   onclick="return confirm('Remover este post?')">✕</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($posts)): ?>
          <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-3)">Nenhum post encontrado.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php else: ?>
    <!-- ═══ FORM EDIÇÃO / NOVO ══════════════════════════════ -->
    <div class="admin-page-title">
      <?= $action === 'new' ? 'Novo Post' : 'Editar: ' . h($post['p_titulo'] ?? '') ?>
    </div>

    <form method="post" enctype="multipart/form-data" id="postForm"
          action="<?= $base ?>/blog.php?action=<?= h($action) ?><?= $id ? "&amp;id=$id" : '' ?>">

      <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:flex-start">

        <!-- ── Coluna principal ── -->
        <div>
          <!-- Título e slug -->
          <div class="admin-card">
            <div class="form-group">
              <label>Título *</label>
              <input type="text" name="titulo" class="form-control" required
                     placeholder="Título do post"
                     value="<?= h($post['p_titulo'] ?? '') ?>"
                     oninput="autoSlug(this.value)">
            </div>
            <div class="form-group">
              <label>Slug (URL amigável)</label>
              <input type="text" id="slug" name="slug" class="form-control"
                     placeholder="slug-do-post"
                     value="<?= h($post['p_slug'] ?? '') ?>">
              <small style="color:var(--text-3)">
                <?= BASE_URL ?>/blog/<span id="slugPreview"><?= h($post['p_slug'] ?? '') ?></span>
              </small>
            </div>
            <div class="form-group">
              <label>Resumo (aparece nas listagens)</label>
              <textarea name="resumo" class="form-control" rows="2"
                        placeholder="Breve descrição do post..."><?= h($post['p_resumo'] ?? '') ?></textarea>
            </div>
          </div>

          <!-- Editor de conteúdo -->
          <div class="admin-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
              <label style="font-size:.82rem;font-weight:700;color:var(--text-2);letter-spacing:.04em">CONTEÚDO *</label>
              <button type="button" id="htmlBtn" onclick="toggleHtml()"
                      style="font-size:.75rem;padding:5px 12px;border:1px solid #cbd5e1;border-radius:4px;cursor:pointer;background:#f8fafc;color:#4a5568">
                &lt;/&gt; HTML
              </button>
            </div>
            <!-- Quill mounts here -->
            <div id="quill-editor" style="min-height:380px"></div>
            <!-- HTML textarea (hidden by default) -->
            <textarea id="html-area" style="display:none;width:100%;min-height:380px;font-family:monospace;font-size:12px;padding:12px;border:1px solid #cbd5e1;border-radius:0 0 4px 4px;background:#f8fafc;color:#1a202c;resize:vertical"></textarea>
            <!-- Hidden field that gets submitted -->
            <textarea name="texto" id="texto-hidden" style="display:none"></textarea>
          </div>

          <!-- SEO -->
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:16px">SEO</div>
            <div class="form-group">
              <label>Title Tag <small style="font-weight:400;color:var(--text-3)">(máx 60 chars)</small></label>
              <input type="text" name="title_seo" class="form-control"
                     placeholder="Título para buscadores"
                     value="<?= h($post['p_title'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Meta Description <small style="font-weight:400;color:var(--text-3)">(máx 155 chars)</small></label>
              <textarea name="description_seo" class="form-control" rows="2"
                        placeholder="Descrição para buscadores"><?= h($post['p_description'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- ── Sidebar direita ── -->
        <div>
          <!-- Publicação -->
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:16px">PUBLICAÇÃO</div>
            <div class="form-group">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="ativo" value="1"
                       <?= ($post['p_ativo'] ?? 1) ? 'checked' : '' ?>>
                Publicado (visível no site)
              </label>
            </div>
            <div class="form-group">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="destaque" value="1"
                       <?= ($post['p_destaque'] ?? 0) ? 'checked' : '' ?>>
                Destaque
              </label>
            </div>
            <div class="form-group">
              <label>Data de publicação</label>
              <input type="datetime-local" name="data" class="form-control"
                     value="<?= date('Y-m-d\TH:i', strtotime($post['p_data'] ?? 'now')) ?>">
            </div>
            <div style="display:flex;gap:8px;margin-top:8px">
              <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center">
                💾 Salvar
              </button>
              <?php if ($id && ($post['p_slug'] ?? '')): ?>
              <a href="<?= BASE_URL ?>/blog/<?= h($post['p_slug']) ?>"
                 target="_blank" class="btn btn-ghost" title="Ver post no site">↗</a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Categoria -->
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px">CATEGORIA *</div>
            <?php if (empty($categorias)): ?>
            <p style="font-size:.85rem;color:var(--text-3)">Nenhuma categoria encontrada.</p>
            <?php else: ?>
            <?php foreach ($categorias as $cat): ?>
            <label style="display:flex;align-items:center;gap:8px;margin-bottom:10px;cursor:pointer;font-size:.88rem">
              <input type="radio" name="categoria" value="<?= $cat['pc_id'] ?>"
                     <?= ($post['p_categoria'] ?? 0) == $cat['pc_id'] ? 'checked' : '' ?>>
              <?= h($cat['pc_titulo']) ?>
            </label>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Imagem de capa -->
          <div class="admin-card">
            <div style="font-size:.8rem;font-weight:700;color:var(--text-2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px">IMAGEM DE CAPA</div>
            <?php if (!empty($post['p_imagem'])): ?>
            <img src="<?= MEDIA_URL . h($post['p_imagem']) ?>"
                 onerror="this.onerror=null;this.src='https://aligator.com.br/media/posts/'+this.src.split('/').pop()"
                 alt="Capa atual"
                 style="width:100%;border-radius:6px;margin-bottom:10px;border:1px solid #e2e8f0">
            <p style="font-size:.72rem;color:var(--text-3);margin-bottom:10px">Imagem atual. Envie nova para substituir.</p>
            <?php endif; ?>
            <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif"
                   class="form-control" style="font-size:.8rem">
            <small style="color:var(--text-3)">JPG, PNG, WEBP — máx 5MB</small>
          </div>
        </div>

      </div>
    </form>
    <?php endif; ?>

  </div>
</main>
</div>

<!-- Quill JS (loaded at bottom to avoid render-blocking) -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function() {
  // Only init editor when in edit/new mode
  const editorEl = document.getElementById('quill-editor');
  if (!editorEl) return;

  // Init Quill
  const quill = new Quill('#quill-editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'code-block'],
        ['link', 'image'],
        [{ color: [] }, { background: [] }],
        ['clean']
      ]
    }
  });

  // Load saved content safely
  var initialContent = <?= $post_content_js ?>;
  if (initialContent) {
    quill.root.innerHTML = initialContent;
  }

  // HTML source toggle
  var htmlMode = false;
  var htmlArea = document.getElementById('html-area');
  var htmlBtn  = document.getElementById('htmlBtn');

  window.toggleHtml = function() {
    htmlMode = !htmlMode;
    if (htmlMode) {
      htmlArea.value = quill.root.innerHTML;
      editorEl.style.display = 'none';
      htmlArea.style.display = 'block';
      htmlBtn.textContent = '✓ Fechar HTML';
      htmlBtn.style.background = '#2F6DAD';
      htmlBtn.style.color = '#fff';
    } else {
      quill.root.innerHTML = htmlArea.value;
      htmlArea.style.display = 'none';
      editorEl.style.display = 'block';
      htmlBtn.textContent = '</> HTML';
      htmlBtn.style.background = '';
      htmlBtn.style.color = '';
    }
  };

  // On submit: sync content to hidden textarea
  var form = document.getElementById('postForm');
  if (form) {
    form.addEventListener('submit', function() {
      var content = htmlMode ? htmlArea.value : quill.root.innerHTML;
      document.getElementById('texto-hidden').value = content;
    });
  }

  // Slug auto-generation
  var slugInput   = document.getElementById('slug');
  var slugPreview = document.getElementById('slugPreview');
  var slugEdited  = !!(slugInput && slugInput.value.trim());

  function makeSlug(str) {
    var map = {'á':'a','à':'a','ã':'a','â':'a','é':'e','è':'e','ê':'e',
               'í':'i','ì':'i','ó':'o','ò':'o','õ':'o','ô':'o','ú':'u','ù':'u','ç':'c'};
    return str.toLowerCase()
      .replace(/[áàãâéèêíìóòõôúùç]/g, function(c){ return map[c]||c; })
      .replace(/[^a-z0-9\s\-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  }

  window.autoSlug = function(val) {
    if (slugEdited) return;
    var s = makeSlug(val);
    if (slugInput)   slugInput.value = s;
    if (slugPreview) slugPreview.textContent = s;
  };

  if (slugInput) {
    slugInput.addEventListener('input', function() {
      slugEdited = true;
      if (slugPreview) slugPreview.textContent = this.value;
    });
    if (slugPreview && slugInput.value) {
      slugPreview.textContent = slugInput.value;
    }
  }

})();
</script>
</body>
</html>
