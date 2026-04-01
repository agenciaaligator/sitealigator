<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
    require BASE_PATH . '/config.php';
}
function admin_check(): void {
    if (empty($_SESSION['admin_id'])) {
        redirect(BASE_URL . '/admin/login.php');
    }
}
function admin_login(string $email, string $senha): bool {
    $stmt = db()->prepare('SELECT sa_id,sa_nome,sa_nivel FROM sis_admins WHERE sa_email=? AND sa_senha=SHA1(?) LIMIT 1');
    $stmt->execute([$email, $senha]);
    $a = $stmt->fetch();
    if ($a) {
        $_SESSION['admin_id']    = $a['sa_id'];
        $_SESSION['admin_nome']  = $a['sa_nome'];
        $_SESSION['admin_nivel'] = $a['sa_nivel'];
        return true;
    }
    return false;
}
function admin_logout(): void {
    session_destroy();
    redirect(BASE_URL . '/admin/login.php');
}
