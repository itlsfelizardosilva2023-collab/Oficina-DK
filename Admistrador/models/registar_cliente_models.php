<?php
session_start();
require_once "./configuracao/conexao.php";
require_once "./funcoes/funcao_registo.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';

define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'kleitonlucas126@gmail.com');
define('SMTP_PASS',     'nniz uccg bloz gcfr');
define('SMTP_FROM',     'kleitonlucas126@gmail.com');
define('SMTP_FROMNAME', 'Oficina D.K');



$erro    = "";
$sucesso = "";

function enviarBoasVindas($nome, $email) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_FROM, SMTP_FROMNAME);
        $mail->addAddress($email, $nome);
        $mail->isHTML(true);
        $mail->Subject = 'Bem-vindo(a) a Oficina D.K';
        $mail->Body = '
<!DOCTYPE html>
<html lang="pt">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:30px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

      <!-- CABEÇALHO -->
      <tr>
        <td style="background-color:#1a1a2e;padding:30px;text-align:center;">
          <table cellpadding="0" cellspacing="0" align="center">
            <tr>
              <td style="padding-right:10px;vertical-align:middle;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z" stroke="#e94560" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </td>
              <td style="vertical-align:middle;">
                <span style="color:#ffffff;font-size:22px;font-weight:bold;letter-spacing:1px;">OFICINA D.K</span>
              </td>
            </tr>
          </table>
          <p style="color:#aaaaaa;margin:8px 0 0;font-size:13px;letter-spacing:1px;">A SUA OFICINA DE CONFIANCA EM LUANDA</p>
        </td>
      </tr>

      <!-- CORPO -->
      <tr>
        <td style="padding:35px 40px;background-color:#ffffff;">

          <p style="color:#1a1a2e;font-size:18px;font-weight:bold;margin:0 0 15px;">Caro(a) ' . htmlspecialchars($nome) . ',</p>
          <p style="color:#555555;font-size:15px;line-height:1.7;margin:0 0 25px;">
            E com muito prazer que damos as <strong>boas-vindas</strong> a familia <strong style="color:#1a1a2e;">Oficina D.K</strong>.
            O seu cadastro foi realizado com sucesso e ja pode usufruir dos nossos servicos.
          </p>

          <!-- Servicos -->
          <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9f9f9;border:1px solid #eeeeee;border-radius:8px;margin-bottom:25px;">
            <tr>
              <td style="padding:20px 25px;">
                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td style="padding-right:10px;vertical-align:top;padding-top:2px;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="#e94560" stroke-width="2"/>
                        <path d="M12 8v4l3 3" stroke="#e94560" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </td>
                    <td>
                      <p style="color:#1a1a2e;font-weight:bold;margin:0 0 12px;font-size:15px;">Os nossos servicos</p>
                      <table cellpadding="0" cellspacing="0">
                        <tr>
                          <td style="padding:4px 0;">
                            <table cellpadding="0" cellspacing="0">
                              <tr>
                                <td style="padding-right:8px;vertical-align:middle;">
                                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <polyline points="20 6 9 17 4 12" stroke="#28a745" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                  </svg>
                                </td>
                                <td style="color:#555555;font-size:14px;">Manutencao preventiva e corretiva</td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding:4px 0;">
                            <table cellpadding="0" cellspacing="0"><tr>
                              <td style="padding-right:8px;vertical-align:middle;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="#28a745" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></td>
                              <td style="color:#555555;font-size:14px;">Diagnostico eletronico</td>
                            </tr></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding:4px 0;">
                            <table cellpadding="0" cellspacing="0"><tr>
                              <td style="padding-right:8px;vertical-align:middle;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="#28a745" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></td>
                              <td style="color:#555555;font-size:14px;">Substituicao de pecas</td>
                            </tr></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding:4px 0;">
                            <table cellpadding="0" cellspacing="0"><tr>
                              <td style="padding-right:8px;vertical-align:middle;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="#28a745" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></td>
                              <td style="color:#555555;font-size:14px;">Revisao geral da viatura</td>
                            </tr></table>
                          </td>
                        </tr>
                        <tr>
                          <td style="padding:4px 0;">
                            <table cellpadding="0" cellspacing="0"><tr>
                              <td style="padding-right:8px;vertical-align:middle;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="#28a745" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></td>
                              <td style="color:#555555;font-size:14px;">Contratos de manutencao periodica</td>
                            </tr></table>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <p style="color:#555555;font-size:15px;line-height:1.7;margin:0 0 25px;">
            Sempre que precisar de agendar um servico ou tiver alguma duvida,
            nao hesite em contactar-nos. Estamos sempre disponiveis para si.
          </p>

          <!-- Botão -->
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center" style="padding:5px 0 25px;">
                <a href="mailto:' . SMTP_FROM . '?subject=Agendamento%20de%20Servico"
                   style="display:inline-block;background-color:#e94560;color:#ffffff;
                          text-decoration:none;padding:14px 35px;border-radius:6px;
                          font-size:15px;font-weight:bold;letter-spacing:0.5px;">
                  Agendar um Servico
                </a>
              </td>
            </tr>
          </table>

          <p style="color:#999999;font-size:13px;margin:0;line-height:1.6;">
            Obrigado por escolher a <strong>Oficina D.K</strong>.<br>
            Este e um email automatico gerado pelo sistema. Por favor nao responda diretamente.
          </p>
        </td>
      </tr>

      <!-- RODAPE -->
      <tr>
        <td style="background-color:#1a1a2e;padding:20px 40px;text-align:center;">
          <p style="color:#aaaaaa;font-size:12px;margin:0;line-height:1.8;">
            &copy; ' . date('Y') . ' Oficina D.K &mdash; Todos os direitos reservados<br>
            Luanda, Angola
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
        ';
        $mail->AltBody = 'Caro(a) ' . $nome . ', bem-vindo(a) a Oficina D.K! O seu cadastro foi realizado com sucesso. Para agendar um servico, contacte-nos em ' . SMTP_FROM;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dadoscliente = [
        'nome'      => trim($_POST['nome']      ?? ''),
        'endereco'  => trim($_POST['endereco']  ?? ''),
        'telefone'  => trim($_POST['telefone']  ?? ''),
        'numero_bi' => trim($_POST['numero_bi'] ?? ''),
        'email'     => trim($_POST['email']     ?? '')
    ];

    if(!preg_match("/^[a-zA-ZÀ-ÿ ]+$/u", $dadoscliente['nome'])){
        $erro = "O nome so pode conter letras e espacos.";
    } elseif(!preg_match("/^[0-9]{9}$/", $dadoscliente['telefone'])){
        $erro = "O telefone deve ter 9 digitos e conter apenas numeros.";
    } elseif(!empty($dadoscliente['email']) && !filter_var($dadoscliente['email'], FILTER_VALIDATE_EMAIL)){
        $erro = "O email introduzido nao e valido.";
    }

    if($erro == ""){
        if(inserirRegistro($conn, 'clientes', $dadoscliente)){
            if(!empty($dadoscliente['email'])){
                enviarBoasVindas($dadoscliente['nome'], $dadoscliente['email']);
            }
            header("Location: clientes.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar o cliente. Tente novamente.";
        }
    }
}
?>