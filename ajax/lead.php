<?php
/**
 * ajax/lead.php — captura de leads com e-mail via SMTP
 */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false,'message'=>'Método não permitido.']); exit;
}

session_start();
$ip  = $_SERVER['REMOTE_ADDR'] ?? '';
$key = 'lead_count_'.date('YmdH').'_'.md5($ip);
$_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
if ($_SESSION[$key] > 10) {
    http_response_code(429);
    echo json_encode(['ok'=>false,'message'=>'Muitas requisições. Tente novamente mais tarde.']); exit;
}

require __DIR__ . '/../config.php';

// Honeypot
if (!empty($_POST['website'])) {
    echo json_encode(['ok'=>true,'message'=>'Obrigado!']); exit;
}

$nome     = trim(strip_tags($_POST['nome']     ?? ''));
$email    = trim($_POST['email']               ?? '');
$telefone = trim(strip_tags($_POST['telefone'] ?? ''));
$empresa  = trim(strip_tags($_POST['empresa']  ?? ''));
$servico  = trim(strip_tags($_POST['servico']  ?? ''));
$mensagem = trim(strip_tags($_POST['mensagem'] ?? ''));
$form     = trim(strip_tags($_POST['form']     ?? 'contato'));

$errors = [];
if (mb_strlen($nome) < 2)                       $errors[] = 'Nome muito curto.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
if (mb_strlen($mensagem) < 5 && !$telefone)     $errors[] = 'Preencha a mensagem ou o telefone.';

if (!empty($errors)) {
    echo json_encode(['ok'=>false,'message'=>implode(' ',$errors)]); exit;
}

$id = save_lead([
    'form'=>$form,'nome'=>$nome,'email'=>$email,
    'fone'=>$telefone,'empresa'=>$empresa,
    'mensagem'=>($servico ? "Serviço: {$servico}\n\n{$mensagem}" : $mensagem),
]);

if (!$id) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'message'=>'Erro ao salvar. Tente novamente.']); exit;
}

// ── E-mail via SMTP ────────────────────────────────────────
function send_smtp(string $to, string $subject, string $body, string $reply_to = ''): bool {
    $host   = cfg('ES_SMTP_HOST',   'smtp.zoho.com');
    $port   = (int)cfg('ES_SMTP_PORT',   '587');
    $user   = cfg('ES_SMTP_USER',   cfg('EMAIL_SENDER',''));
    $pass   = cfg('ES_SMTP_PASS',   '');
    $from   = cfg('EMAIL_SENDER',   SITE_EMAIL);
    $fname  = cfg('EMAIL_SENDER_NAME','Aligator');

    if (!$host || !$user || !$pass) {
        // Fallback: mail() nativo
        $headers = "From: $fname <$from>\r\nReply-To: $reply_to\r\nContent-Type: text/plain; charset=UTF-8";
        return @mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $body, $headers);
    }

    try {
        $ctx = stream_context_create(['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
        $secure = strtolower(cfg('ES_SMTP_SECURE','tls'));

        if ($secure === 'ssl') {
            $sock = @stream_socket_client("ssl://{$host}:{$port}", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
        } else {
            $sock = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 15);
        }
        if (!$sock) return false;

        $read = fn() => fgets($sock, 512);
        $send = fn($cmd) => fwrite($sock, $cmd."\r\n");

        $read(); // greeting
        $send("EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        while (true) { $r = $read(); if ($r[3] === ' ') break; }

        if ($secure === 'tls') {
            $send("STARTTLS");
            $read();
            stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $send("EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            while (true) { $r = $read(); if ($r[3] === ' ') break; }
        }

        $send("AUTH LOGIN");
        $read();
        $send(base64_encode($user)); $read();
        $send(base64_encode($pass)); $r = $read();
        if (strpos($r,'235') === false) { fclose($sock); return false; }

        $send("MAIL FROM:<{$from}>"); $read();
        $send("RCPT TO:<{$to}>");     $read();
        $send("DATA");                $read();

        $subj_enc = '=?UTF-8?B?'.base64_encode($subject).'?=';
        $msg  = "From: =?UTF-8?B?".base64_encode($fname)."?= <{$from}>\r\n";
        $msg .= "To: {$to}\r\n";
        $msg .= "Reply-To: {$reply_to}\r\n";
        $msg .= "Subject: {$subj_enc}\r\n";
        $msg .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $msg .= chunk_split(base64_encode($body))."\r\n.\r\n";
        fwrite($sock, $msg);
        $r = $read();

        $send("QUIT"); fclose($sock);
        return strpos($r,'250') !== false;

    } catch (Throwable $e) {
        return false;
    }
}

// Envia notificação para a Aligator
$subject = "Novo lead: {$nome} — Aligator";
$body    = "Nome: {$nome}" .
           "\nE-mail: {$email}" .
           "\nTelefone: {$telefone}" .
           ($empresa  ? "\nEmpresa: {$empresa}"   : '') .
           ($servico  ? "\nServiço: {$servico}"   : '') .
           ($mensagem ? "\n\nMensagem:\n{$mensagem}" : '');
send_smtp(SITE_EMAIL, $subject, $body, $email);

// Webhook Make/n8n (se configurado)
$webhook = cfg('webhook_url','');
if ($webhook) {
    $payload = json_encode([
        'nome'=>$nome,'email'=>$email,'telefone'=>$telefone,
        'empresa'=>$empresa,'mensagem'=>$mensagem,'forma'=>$form,
    ]);
    $ch = curl_init($webhook);
    curl_setopt_array($ch,[
        CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>$payload,
        CURLOPT_HTTPHEADER=>['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>5,
    ]);
    @curl_exec($ch); @curl_close($ch);
}

echo json_encode(['ok'=>true,'message'=>'Mensagem enviada!','id'=>$id]);
