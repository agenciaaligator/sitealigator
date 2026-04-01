<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/config.php';
require __DIR__ . '/includes/auth.php';
if (!empty($_SESSION['admin_id'])) redirect(BASE_URL . '/admin/');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    if ($email && $senha) {
        if (admin_login($email, $senha)) redirect(BASE_URL . '/admin/');
        else $error = 'E-mail ou senha incorretos.';
    } else {
        $error = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — Admin Aligator</title>
<link rel="stylesheet" href="../css/admin.css">
<meta name="robots" content="noindex,nofollow">
<style>
body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f0f4f8; }
.login-box { width:100%; max-width:420px; padding:24px; }
.login-logo { text-align:center; margin-bottom:32px; }
.login-logo img { height:48px; width:auto; }
</style>
</head>
<body>
<div class="login-box">
  <div class="login-logo">
    <img src="https://aligator.com.br/media/aligator.png" alt="Aligator"
         onerror="this.outerHTML='<div style=\'font-family:Inter,sans-serif;font-weight:700;font-size:1.5rem;color:#1A202C\'>Aligator</div>'">
    <p style="font-size:.82rem;color:var(--text-3);margin-top:6px">Painel Administrativo</p>
  </div>
  <div class="admin-card" style="padding:36px">
    <h1 style="font-size:1.25rem;font-weight:700;margin-bottom:4px">Entrar</h1>
    <p style="font-size:.85rem;color:var(--text-2);margin-bottom:24px">Acesse o painel de gerenciamento</p>
    <?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/admin/login.php">
      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="admin@aligator.com.br" required autofocus value="<?= h($_POST['email']??'') ?>">
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:6px;font-size:.95rem">
        Entrar no Painel
      </button>
    </form>
  </div>
  <p style="text-align:center;margin-top:16px">
    <a href="<?= BASE_URL ?>/" style="font-size:.82rem;color:var(--text-3)">← Voltar ao site</a>
  </p>
</div>
</body></html>
