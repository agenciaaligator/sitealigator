<?php
/**
 * Aligator — config.php
 * IMPORTANTE: Altere BASE_URL para '' quando mover para a raiz do domínio
 */

date_default_timezone_set('America/Sao_Paulo');
mb_internal_encoding('UTF-8');

// ── Banco de Dados ──────────────────────────────────────────
define('DB_HOST', 'bd_aligator.vpscronos0371.mysql.dbaas.com.br');
define('DB_NAME', 'bd_aligator');
define('DB_USER', 'bd_aligator');
define('DB_PASS', 'Ywxl757Ywxl757@');
define('DB_CHARSET', 'utf8mb4');

// ── Site ────────────────────────────────────────────────────
define('SITE_URL',   'https://aligator.com.br');
define('SITE_NAME',  'Aligator');
define('SITE_EMAIL', 'contato@aligator.com.br');
define('SITE_PHONE', '(11) 9 7957-7468');
define('SITE_WHATS', '5511979577468');

// ── BASE_URL — /sitenovo em subpasta, '' na raiz ─────────────
define('BASE_URL', '');

// ── Paths ────────────────────────────────────────────────────
define('BASE_PATH',  __DIR__);
// Todas as imagens (antigas e novas) estão em /media/posts/
define('MEDIA_URL',        'https://aligator.com.br/media/posts/');
define('MEDIA_URL_LEGACY', 'https://aligator.com.br/media/posts/');

// ── Conexão PDO ──────────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // Data stored as UTF-8 bytes in latin1 columns (legacy encoding)
        // SET NAMES latin1 = send raw bytes without conversion → PHP gets correct UTF-8
        // ATTR_EMULATE_PREPARES true = text protocol, avoids collation conflict on writes
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=latin1';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => true,
            ]);
            $pdo->exec("SET NAMES 'latin1'");
        } catch (PDOException $e) {
            http_response_code(503);
            die('Erro de conexão com o banco de dados.');
        }
    }
    return $pdo;
}

// ── Configurações do banco ────────────────────────────────────
function cfg(string $key, string $default = ''): string {
    static $cache = [];
    if (!isset($cache[$key])) {
        try {
            $stmt = db()->prepare('SELECT sc_valor FROM sis_configuracoes WHERE sc_key = ? LIMIT 1');
            $stmt->execute([$key]);
            $cache[$key] = $stmt->fetchColumn() ?: $default;
        } catch (Exception $e) { $cache[$key] = $default; }
    }
    return $cache[$key];
}

// ── Helpers ───────────────────────────────────────────────────
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES|ENT_HTML5, 'UTF-8');
}
function slug(string $str): string {
    $str = mb_strtolower($str, 'UTF-8');
    $map = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','è'=>'e','ê'=>'e',
            'í'=>'i','ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ú'=>'u','ù'=>'u',
            'û'=>'u','ç'=>'c','ñ'=>'n'];
    $str = strtr($str, $map);
    return trim(preg_replace('/[\s-]+/', '-', preg_replace('/[^a-z0-9\s-]/', '', $str)), '-');
}
function redirect(string $url): void { header("Location: $url", true, 302); exit; }
function current_url(): string {
    $s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $s . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
function capture_utms(): array {
    $keys = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content'];
    $u = [];
    foreach ($keys as $k) {
        if (!empty($_GET[$k])) $_SESSION[$k] = $_GET[$k];
        $u[$k] = $_SESSION[$k] ?? '';
    }
    if (!isset($_SESSION['landing_page'])) $_SESSION['landing_page'] = current_url();
    $u['landing_page'] = $_SESSION['landing_page'];
    $u['referrer'] = $_SERVER['HTTP_REFERER'] ?? '';
    return $u;
}
function register_pageview(string $slug, string $titulo = ''): void {
    try {
        db()->prepare('INSERT INTO page_views (pv_slug,pv_titulo,pv_views) VALUES(?,?,1)
                       ON DUPLICATE KEY UPDATE pv_views=pv_views+1,pv_titulo=VALUES(pv_titulo)')
           ->execute([$slug, $titulo]);
    } catch (Exception $e) {}
}
function save_lead(array $data): int|false {
    $u = capture_utms();
    $stmt = db()->prepare(
        'INSERT INTO leads (l_form,l_nome,l_email,l_telefone,l_empresa,l_mensagem,
         l_utm_source,l_utm_medium,l_utm_campaign,l_utm_term,l_utm_content,
         l_landing_page,l_referrer,l_ip,l_user_agent)
         VALUES(:form,:nome,:email,:fone,:emp,:msg,:src,:med,:camp,:term,:cont,:land,:ref,:ip,:ua)'
    );
    $ok = $stmt->execute([
        ':form'=>$data['form']??'contato', ':nome'=>$data['nome']??'',
        ':email'=>$data['email']??'', ':fone'=>$data['fone']??'',
        ':emp'=>$data['empresa']??'', ':msg'=>$data['mensagem']??'',
        ':src'=>$u['utm_source'], ':med'=>$u['utm_medium'],
        ':camp'=>$u['utm_campaign'], ':term'=>$u['utm_term'],
        ':cont'=>$u['utm_content'], ':land'=>$u['landing_page'],
        ':ref'=>$u['referrer'], ':ip'=>$_SERVER['REMOTE_ADDR']??'',
        ':ua'=>$_SERVER['HTTP_USER_AGENT']??'',
    ]);
    return $ok ? (int)db()->lastInsertId() : false;
}

// Convert UTF-8 text to latin1-safe for legacy DB tables
// Characters not in latin1 are transliterated (e.g. smart quotes → straight quotes)
function to_db(string $s): string {
    if (empty($s)) return $s;
    $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $s);
    return ($converted !== false) ? $converted : $s;
}

if (session_status() === PHP_SESSION_NONE) session_start();
