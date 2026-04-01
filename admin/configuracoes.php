<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
admin_check();
$base = BASE_URL . '/admin';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'META_TITLE','META_DESCRIPTION','META_KEYWORDS',
        'EMAIL_SENDER','smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_name',
        'calendly_url','whatsapp_numero','analytics_id','gtm_id',
        'instagram_url','linkedin_url','webhook_url',
    ];
    foreach ($fields as $f) {
        $val = trim($_POST[$f] ?? '');
        // Check if row exists, then UPDATE or INSERT
        $exists = db()->prepare('SELECT sc_id FROM sis_configuracoes WHERE sc_key=? LIMIT 1');
        $exists->execute([$f]);
        if ($exists->fetch()) {
            db()->prepare('UPDATE sis_configuracoes SET sc_valor=? WHERE sc_key=?')->execute([$val, $f]);
        } else {
            db()->prepare('INSERT INTO sis_configuracoes (sc_key,sc_valor) VALUES(?,?)')->execute([$f, $val]);
        }
    }
    $msg = 'Configurações salvas com sucesso.';
}

// Load current
$raw = db()->query('SELECT sc_key,sc_valor FROM sis_configuracoes')->fetchAll();
$conf = [];
foreach ($raw as $r) $conf[$r['sc_key']] = $r['sc_valor'];
function cv(array $c, string $k, string $d=''): string { return htmlspecialchars($c[$k]??$d,ENT_QUOTES,'UTF-8'); }

$admin_active = 'config';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Configurações — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="admin-layout">
<?php require __DIR__ . '/includes/layout.php'; ?>
<main class="admin-main">
  <div class="admin-topbar">
    <span class="admin-topbar-title">Configurações</span>
  </div>
  <div class="admin-page">
    <div class="admin-page-title">Configurações do Site</div>
    <div class="admin-page-sub">Gerencie informações globais, SEO e integrações.</div>
    <?php if ($msg): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>

    <form method="post" action="<?= $base ?>/configuracoes.php">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

        <!-- SEO Global -->
        <div class="admin-card">
          <div class="admin-card-header"><span class="admin-card-title">SEO Global</span></div>
          <div class="form-group">
            <label>Meta Title padrão</label>
            <input type="text" name="META_TITLE" class="form-control" value="<?= cv($conf,'META_TITLE') ?>">
          </div>
          <div class="form-group">
            <label>Meta Description padrão</label>
            <textarea name="META_DESCRIPTION" class="form-control" rows="2"><?= cv($conf,'META_DESCRIPTION') ?></textarea>
          </div>
          <div class="form-group">
            <label>Meta Keywords</label>
            <input type="text" name="META_KEYWORDS" class="form-control" value="<?= cv($conf,'META_KEYWORDS') ?>" placeholder="palavra1, palavra2">
          </div>
        </div>

        <!-- Integrações -->
        <div class="admin-card">
          <div class="admin-card-header"><span class="admin-card-title">Integrações</span></div>
          <div class="form-group">
            <label>Calendly URL</label>
            <input type="url" name="calendly_url" class="form-control" value="<?= cv($conf,'calendly_url','https://calendly.com/agenciaaligator') ?>">
          </div>
          <div class="form-group">
            <label>WhatsApp (DDI+DDD+número)</label>
            <input type="text" name="whatsapp_numero" class="form-control" value="<?= cv($conf,'whatsapp_numero','5511979577468') ?>">
          </div>
          <div class="form-group">
            <label>Google Analytics 4 (Measurement ID)</label>
            <input type="text" name="analytics_id" class="form-control" placeholder="G-XXXXXXXXXX" value="<?= cv($conf,'analytics_id') ?>">
          </div>
          <div class="form-group">
            <label>Google Tag Manager ID</label>
            <input type="text" name="gtm_id" class="form-control" placeholder="GTM-XXXXXXX" value="<?= cv($conf,'gtm_id') ?>">
          </div>
        </div>

        <!-- E-mail / SMTP -->
        <div class="admin-card">
          <div class="admin-card-header"><span class="admin-card-title">E-mail / SMTP</span></div>
          <div class="form-group">
            <label>E-mail remetente</label>
            <input type="email" name="EMAIL_SENDER" class="form-control" value="<?= cv($conf,'EMAIL_SENDER','site@aligator.com.br') ?>">
          </div>
          <div style="display:grid;grid-template-columns:1fr 80px;gap:10px">
            <div class="form-group">
              <label>Servidor SMTP</label>
              <input type="text" name="smtp_host" class="form-control" placeholder="smtp.gmail.com" value="<?= cv($conf,'smtp_host') ?>">
            </div>
            <div class="form-group">
              <label>Porta</label>
              <input type="number" name="smtp_port" class="form-control" placeholder="587" value="<?= cv($conf,'smtp_port','587') ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Usuário SMTP</label>
            <input type="text" name="smtp_user" class="form-control" value="<?= cv($conf,'smtp_user') ?>">
          </div>
          <div class="form-group">
            <label>Senha SMTP</label>
            <input type="password" name="smtp_pass" class="form-control" value="<?= cv($conf,'smtp_pass') ?>" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label>Nome do remetente</label>
            <input type="text" name="smtp_from_name" class="form-control" placeholder="Aligator" value="<?= cv($conf,'smtp_from_name','Aligator') ?>">
          </div>
        </div>

        <!-- Redes Sociais -->
        <div class="admin-card">
          <div class="admin-card-header"><span class="admin-card-title">Redes Sociais</span></div>
          <div class="form-group">
            <label>Instagram URL</label>
            <input type="url" name="instagram_url" class="form-control" value="<?= cv($conf,'instagram_url','https://instagram.com/agenciaaligator') ?>">
          </div>
          <div class="form-group">
            <label>LinkedIn URL (opcional)</label>
            <input type="url" name="linkedin_url" class="form-control" value="<?= cv($conf,'linkedin_url') ?>">
          </div>
        </div>

      </div>
      <div style="margin-top:8px">
        <button type="submit" class="btn btn-primary btn-lg">Salvar todas as configurações</button>
      </div>
    </form>
  </div>
</main>
</div>
</body></html>
