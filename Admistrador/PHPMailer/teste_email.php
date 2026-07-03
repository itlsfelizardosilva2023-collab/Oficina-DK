<?php
// ============================================================
// IMPORTAÇÃO DAS CLASSES (deve vir ANTES dos requires)
// ============================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// ============================================================
// CARREGAMENTO DOS FICHEIROS DO PHPMAILER
// ============================================================
require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

// ============================================================
// CONFIGURAÇÕES (edita apenas aqui)
// ============================================================
define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'kleitonlucas126@gmail.com');
define('SMTP_PASS',     'nniz uccg bloz gcfr'); // Senha de App do Google
define('SMTP_FROM',     'kleitonlucas126@gmail.com');
define('SMTP_FROMNAME', 'Oficina D.K');
define('SMTP_TO',       'kleitonlucas126@gmail.com');

// ============================================================
// ENVIO DO EMAIL
// ============================================================
$mail = new PHPMailer(true);

try {
    // --- Servidor SMTP ---
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8'; // Suporte a caracteres especiais (ã, ç, etc.)

    // --- Remetente e Destinatário ---
    $mail->setFrom(SMTP_FROM, SMTP_FROMNAME);
    $mail->addAddress(SMTP_TO);

    // --- Conteúdo ---
    $mail->isHTML(true);
    $mail->Subject = 'Teste de Envio - Oficina D.K';
    $mail->Body    = '
        <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 500px;">
            <h2 style="color: #2c3e50;">✅ Teste Concluído</h2>
            <p style="color: #555;">O envio de email da <strong>Oficina D.K</strong> está a funcionar corretamente.</p>
            <hr style="border: 1px solid #eee;">
            <small style="color: #aaa;">Email enviado automaticamente pelo sistema.</small>
        </div>
    ';
    // Versão sem HTML (para clientes que não suportam)
    $mail->AltBody = 'Teste concluído. O envio de email da Oficina D.K está a funcionar.';

    // --- Enviar ---
    $mail->send();
    echo '✅ Email enviado com sucesso para ' . SMTP_TO;

} catch (Exception $e) {
    echo '❌ Erro ao enviar email: ' . $mail->ErrorInfo;
} catch (\Exception $e) {
    echo '❌ Erro geral: ' . $e->getMessage();
}