<?php
require __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') define('BASE_URL', '/sitenovo');

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $empresa  = trim($_POST['empresa']  ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');
    $servico  = trim($_POST['servico']  ?? '');

    if (!$nome)                     $errors[] = 'Nome é obrigatório.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
    if (!$mensagem)                 $errors[] = 'Mensagem é obrigatória.';

    if (empty($errors)) {
        $id = save_lead([
            'form'     => 'contato_principal',
            'nome'     => $nome,
            'email'    => $email,
            'fone'     => $telefone,
            'empresa'  => $empresa,
            'mensagem' => "Serviço: $servico\n\n$mensagem",
        ]);
        if ($id) {
            // E-mail via SMTP (lê credenciais do admin → Configurações)
            $smtp_host  = cfg('ES_SMTP_HOST',   'smtp.zoho.com');
            $smtp_port  = (int)cfg('ES_SMTP_PORT',   '587');
            $smtp_user  = cfg('ES_SMTP_USER',   cfg('EMAIL_SENDER', SITE_EMAIL));
            $smtp_pass  = cfg('ES_SMTP_PASS',   '');
            $smtp_from  = cfg('EMAIL_SENDER',   SITE_EMAIL);
            $smtp_fname = cfg('EMAIL_SENDER_NAME', 'Aligator');
            $smtp_to    = SITE_EMAIL;
            $smtp_subj  = "Novo lead: {$nome} — Aligator";
            $smtp_body  = "Nome: {$nome}\nE-mail: {$email}\nTelefone: {$telefone}\nEmpresa: {$empresa}\nServiço: {$servico}\n\n{$mensagem}";

            $sent = false;
            if ($smtp_host && $smtp_user && $smtp_pass) {
                try {
                    $ctx  = stream_context_create(['ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
                    $sec  = strtolower(cfg('ES_SMTP_SECURE','tls'));
                    $sock = ($sec === 'ssl')
                        ? @stream_socket_client("ssl://{$smtp_host}:{$smtp_port}", $en, $es, 15, STREAM_CLIENT_CONNECT, $ctx)
                        : @stream_socket_client("tcp://{$smtp_host}:{$smtp_port}", $en, $es, 15);
                    if ($sock) {
                        $rd = fn() => fgets($sock, 512);
                        $wr = fn($l) => fwrite($sock, $l."\r\n");
                        $rd();
                        $wr("EHLO ".($_SERVER['HTTP_HOST']??'localhost'));
                        while(true){$r=$rd();if($r[3]===' ')break;}
                        if ($sec === 'tls') {
                            $wr("STARTTLS"); $rd();
                            stream_socket_enable_crypto($sock,true,STREAM_CRYPTO_METHOD_TLS_CLIENT);
                            $wr("EHLO ".($_SERVER['HTTP_HOST']??'localhost'));
                            while(true){$r=$rd();if($r[3]===' ')break;}
                        }
                        $wr("AUTH LOGIN"); $rd();
                        $wr(base64_encode($smtp_user)); $rd();
                        $wr(base64_encode($smtp_pass)); $r=$rd();
                        if (strpos($r,'235')!==false) {
                            $wr("MAIL FROM:<{$smtp_from}>"); $rd();
                            $wr("RCPT TO:<{$smtp_to}>");   $rd();
                            $wr("DATA"); $rd();
                            $msg  = "From: =?UTF-8?B?".base64_encode($smtp_fname)."?= <{$smtp_from}>\r\n";
                            $msg .= "To: {$smtp_to}\r\n";
                            $msg .= "Reply-To: {$email}\r\n";
                            $msg .= "Subject: =?UTF-8?B?".base64_encode($smtp_subj)."?=\r\n";
                            $msg .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
                            $msg .= chunk_split(base64_encode($smtp_body))."\r\n.\r\n";
                            fwrite($sock, $msg); $r=$rd();
                            $sent = strpos($r,'250')!==false;
                        }
                        $wr("QUIT"); fclose($sock);
                    }
                } catch (Throwable $e) { $sent = false; }
            }
            // Fallback: mail() nativo
            if (!$sent) {
                $h = "From: {$smtp_fname} <{$smtp_from}>\r\nReply-To: {$email}\r\nContent-Type: text/plain; charset=UTF-8";
                @mail($smtp_to, '=?UTF-8?B?'.base64_encode($smtp_subj).'?=', $smtp_body, $h);
            }
            
            // Webhook Make/n8n (configure na variável abaixo)
            $webhook_url = cfg('webhook_url', '');
            if ($webhook_url) {
                $payload = json_encode([
                    'nome' => $nome, 'email' => $email, 'telefone' => $telefone,
                    'empresa' => $empresa, 'servico' => $servico, 'mensagem' => $mensagem,
                    'origem' => 'site_aligator',
                ]);
                $ch = curl_init($webhook_url);
                curl_setopt_array($ch, [
                    CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5,
                ]);
                @curl_exec($ch); @curl_close($ch);
            }
            // Redirecionar para página de obrigado
            redirect(BASE_URL . '/obrigado');
        } else {
            $errors[] = 'Erro ao enviar. Tente novamente.';
        }
    }
}

$seo_title = 'Fale com a Aligator — Contato';
$seo_desc  = 'Agende uma reunião estratégica gratuita ou envie uma mensagem. Estamos prontos para ajudar seu negócio a crescer.';
$calendly  = cfg('calendly_url', 'https://calendly.com/agenciaaligator');

register_pageview('contato', 'Contato');
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="page-header-bg"></div>
  <div class="container page-header-content">
    <div class="label">Contato</div>
    <h1 class="display-2" style="margin-bottom:12px">
      Vamos construir algo<br>
      <span class="text-green">extraordinário?</span>
    </h1>
    <p>Agende uma reunião estratégica gratuita ou envie sua mensagem.<br>
       Respondemos em até 1 dia útil.</p>
  </div>
</div>

<section>
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:64px;align-items:flex-start" class="two-col">

      <!-- ── Info lateral ── -->
      <div>
        <div class="contact-info" data-reveal>
          <div class="contact-item">
            <div class="contact-item-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <div class="contact-item-text">
              <div class="label-s">E-mail</div>
              <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-item-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></div>
            <div class="contact-item-text">
              <div class="label-s">WhatsApp</div>
              <a href="https://wa.me/<?= SITE_WHATS ?>" target="_blank" rel="noopener">
                <?= SITE_PHONE ?>
              </a>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-item-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div class="contact-item-text">
              <div class="label-s">Localização</div>
              <span style="font-weight:500">São Bernardo do Campo, SP — Brasil</span>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-item-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div class="contact-item-text">
              <div class="label-s">Atendimento</div>
              <span style="font-weight:500">Seg–Sex, 9h às 18h</span>
            </div>
          </div>
        </div>

        <!-- Calendly embed -->
        <div style="margin-top:40px" data-reveal data-reveal-delay="2">
          <div class="label" style="margin-bottom:16px">Prefere agendar direto?</div>
          <h3 style="font-size:1.2rem;margin-bottom:12px">Agende sua reunião estratégica</h3>
          <p style="font-size:.9rem;color:var(--text-2);margin-bottom:20px">
            Escolha o melhor horário para uma conversa de 30 minutos.
            Sem compromisso, sem pressão.
          </p>
          <?php if ($calendly): ?>
          <a href="<?= h($calendly) ?>" target="_blank" rel="noopener"
             class="btn btn-primary" style="width:100%;justify-content:center">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Ver Agenda e Agendar
          </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Formulário ── -->
      <div data-reveal data-reveal-delay="1">
        <div class="card" style="padding:40px">
          <h2 style="font-size:1.4rem;margin-bottom:8px">Enviar mensagem</h2>
          <p style="font-size:.88rem;color:var(--text-2);margin-bottom:32px">
            Conte sobre seu projeto e entraremos em contato em breve.
          </p>

          <?php if ($success): ?>
          <div class="alert alert-success">
            ✓ Mensagem enviada! Entraremos em contato em breve. Obrigado, <?= h($_POST['nome'] ?? '') ?>!
          </div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <?= implode('<br>', array_map('h', $errors)) ?>
          </div>
          <?php endif; ?>

          <?php if (!$success): ?>
          <form method="post" action="<?= BASE_URL ?>/contato">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div class="form-group">
                <label for="nome">Nome *</label>
                <input type="text" id="nome" name="nome" class="form-control"
                       placeholder="Seu nome" required
                       value="<?= h($_POST['nome'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="seu@email.com" required
                       value="<?= h($_POST['email'] ?? '') ?>">
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div class="form-group">
                <label for="telefone">WhatsApp</label>
                <input type="tel" id="telefone" name="telefone" class="form-control"
                       placeholder="(11) 9 9999-9999"
                       value="<?= h($_POST['telefone'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="empresa">Empresa</label>
                <input type="text" id="empresa" name="empresa" class="form-control"
                       placeholder="Nome da empresa"
                       value="<?= h($_POST['empresa'] ?? '') ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="servico">Qual solução te interessa?</label>
              <select id="servico" name="servico" class="form-control">
                <option value="">Selecione...</option>
                <option value="sistemas">Sistemas Personalizados / SaaS</option>
                <option value="automacao">Automação & IA</option>
                <option value="growth">Growth Performance / Tráfego Pago</option>
                <option value="seo">SEO Técnico</option>
                <option value="eventos">Soluções para Eventos</option>
                <option value="mentoria">Mentoria</option>
                <option value="outro">Outro</option>
              </select>
            </div>
            <div class="form-group">
              <label for="mensagem">Mensagem *</label>
              <textarea id="mensagem" name="mensagem" class="form-control"
                        placeholder="Conte sobre seu projeto, desafio ou dúvida..."
                        rows="5" required><?= h($_POST['mensagem'] ?? '') ?></textarea>
            </div>
            <!-- UTM hidden fields (preenchidos via JS) -->
            <input type="hidden" name="utm_source"   id="utms">
            <input type="hidden" name="utm_medium"   id="utmm">
            <input type="hidden" name="utm_campaign" id="utmc">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:16px">
              Enviar Mensagem
            </button>
            <p style="font-size:.75rem;color:var(--text-3);text-align:center;margin-top:12px">
              Ao enviar, você concorda com nossa
              <a href="<?= BASE_URL ?>/politica-de-privacidade" style="color:var(--text-2);text-decoration:underline">Política de Privacidade</a>.
            </p>
          </form>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
// Pre-fill UTM hidden fields
document.addEventListener('DOMContentLoaded', function() {
  const ss = sessionStorage;
  const f = document.getElementById('utms');
  const m = document.getElementById('utmm');
  const c = document.getElementById('utmc');
  if (f) f.value = ss.getItem('utm_source') || '';
  if (m) m.value = ss.getItem('utm_medium') || '';
  if (c) c.value = ss.getItem('utm_campaign') || '';
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
