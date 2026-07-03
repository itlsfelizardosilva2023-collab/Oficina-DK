<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

include_once("./configuracao/conexao.php");

define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'kleitonlucas126@gmail.com');
define('SMTP_PASS',     'nniz uccg bloz gcfr');
define('SMTP_FROM',     'kleitonlucas126@gmail.com');
define('SMTP_FROMNAME', 'Oficina D.K');

$sql = "
SELECT
    ct.id_contrato,
    c.nome,
    c.email,
    ct.data_inicio,
    ct.tipo,
    DATEDIFF(CURDATE(), ct.data_inicio) AS dias
FROM contratos ct
INNER JOIN clientes c ON c.id_cliente = ct.id_cliente
LEFT JOIN lembretes_enviados le ON le.id_contrato = ct.id_contrato
WHERE
    c.email IS NOT NULL
    AND c.email != ''
    AND ct.status = 'pago'
    AND DATEDIFF(CURDATE(), ct.data_inicio) >= 30
    AND le.id_contrato IS NULL
";

$resultado = mysqli_query($conn, $sql);
if (!$resultado) die("Erro na consulta: " . mysqli_error($conn));

$total    = mysqli_num_rows($resultado);
$enviados = 0;
$falhados = 0;

function enviarLembrete($cliente) {
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
        $mail->addAddress($cliente['email'], $cliente['nome']);
        $mail->isHTML(true);
        $mail->Subject = 'Lembrete de Manutencao Preventiva - Oficina D.K';
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
          <p style="color:#aaaaaa;margin:8px 0 0;font-size:13px;letter-spacing:1px;">SISTEMA DE MANUTENCAO PREVENTIVA</p>
        </td>
      </tr>

      <!-- CORPO -->
      <tr>
        <td style="padding:35px 40px;background-color:#ffffff;">

          <!-- Saudação -->
          <p style="color:#1a1a2e;font-size:18px;font-weight:bold;margin:0 0 15px;">Caro(a) ' . htmlspecialchars($cliente['nome']) . ',</p>
          <p style="color:#555555;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Informamos que o seu contrato <strong style="color:#1a1a2e;">#' . $cliente['id_contrato'] . '</strong>
            completou <strong style="color:#e94560;">' . $cliente['dias'] . ' dias</strong>
            desde o inicio em <strong>' . date('d/m/Y', strtotime($cliente['data_inicio'])) . '</strong>.
            Esta na hora de realizar a sua manutencao preventiva.
          </p>

          <!-- Caixa de aviso -->
          <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff8f0;border-left:4px solid #e94560;border-radius:4px;margin-bottom:25px;">
            <tr>
              <td style="padding:18px 20px;">
                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td style="padding-right:10px;vertical-align:top;padding-top:2px;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="#e94560" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="12" y1="9" x2="12" y2="13" stroke="#e94560" stroke-width="2" stroke-linecap="round"/>
                        <line x1="12" y1="17" x2="12.01" y2="17" stroke="#e94560" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </td>
                    <td>
                      <p style="color:#1a1a2e;font-weight:bold;margin:0 0 8px;font-size:14px;">Por que realizar a manutencao agora?</p>
                      <ul style="color:#555555;margin:0;padding-left:18px;font-size:14px;line-height:2;">
                        <li>Prevenir avarias inesperadas</li>
                        <li>Prolongar a vida util da viatura</li>
                        <li>Garantir seguranca na estrada</li>
                        <li>Manter a garantia do contrato</li>
                      </ul>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Botão -->
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center" style="padding:10px 0 25px;">
                <a href="mailto:' . SMTP_FROM . '?subject=Agendamento%20Manutencao%20Contrato%20' . $cliente['id_contrato'] . '"
                   style="display:inline-block;background-color:#e94560;color:#ffffff;
                          text-decoration:none;padding:14px 35px;border-radius:6px;
                          font-size:15px;font-weight:bold;letter-spacing:0.5px;">
                  Agendar Manutencao
                </a>
              </td>
            </tr>
          </table>

          <p style="color:#999999;font-size:13px;margin:0;line-height:1.6;">
            Para mais informacoes, responda a este email ou contacte-nos diretamente.<br>
            Este e um email automatico gerado pelo sistema da Oficina D.K.
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
        $mail->AltBody = 'Caro(a) ' . $cliente['nome'] .  ' completou ' . $cliente['dias'] . ' dias. Esta na hora da manutencao preventiva. Contacte a Oficina D.K para agendar.';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

$cli = (php_sapi_name() === 'cli');
function log_msg($msg) {
    global $cli;
    if ($cli) echo strip_tags($msg) . PHP_EOL;
}

$clientes = [];
while ($row = mysqli_fetch_assoc($resultado)) $clientes[] = $row;

$clientes_resultado = [];
foreach ($clientes as $cliente) {
    $resultado_envio = enviarLembrete($cliente);
    if ($resultado_envio === true) {
        $id   = (int)$cliente['id_contrato'];
        $hoje = date('Y-m-d');
        mysqli_query($conn, "INSERT IGNORE INTO lembretes_enviados (id_contrato, data_envio) VALUES ($id, '$hoje')");
        $enviados++;
        log_msg("[OK] Email enviado: " . $cliente['nome'] . " | Contrato #$id | " . $cliente['dias'] . " dias");
    } else {
        $falhados++;
        log_msg("[ERRO] Falhou: " . $cliente['nome'] . " | " . $resultado_envio);
    }
    $clientes_resultado[] = [
        'cliente' => $cliente,
        'sucesso' => ($resultado_envio === true),
        'erro'    => ($resultado_envio !== true) ? $resultado_envio : ''
    ];
}

log_msg("Total: $total | Enviados: $enviados | Falharam: $falhados");

// Detecta se este ficheiro foi aberto diretamente no browser (standalone)
// ou se foi puxado via include_once() por outra página (ex: painel de notificações).
// get_included_files() inclui sempre o próprio script na posição 0; se houver
// mais do que esse, é porque outro ficheiro fez o include.
$eh_standalone = (count(get_included_files()) <= 1) || (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__));

if (!$cli):
    if ($eh_standalone):
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembretes de Manutencao - Oficina D.K</title>
    <style>
        body { margin: 0; padding: 24px 16px; background-color: #f4f4f4; }
    </style>
</head>
<body>
<?php endif; ?>

<style>
/* Estilos escopados ao componente .lembretes-wrap, para não vazar
   nem ser afetado por estilos da página onde for incluído. */
.lembretes-wrap {
    box-sizing: border-box;
    width: 100%;
    max-width: 100%;
    font-family: Arial, sans-serif;
    overflow-wrap: break-word;
}
.lembretes-wrap * { box-sizing: border-box; max-width: 100%; }

.lembretes-wrap.standalone {
    max-width: 600px;
    margin: 0 auto;
}

.lembretes-wrap h2 {
    font-size: 16px;
    font-weight: bold;
    color: #1a1a2e;
    margin: 0 0 14px;
}

.lembretes-wrap .aviso {
    background: #fff8f0;
    border-left: 4px solid #e94560;
    border-radius: 6px;
    padding: 14px 16px;
    color: #555;
    font-size: 13px;
    line-height: 1.6;
}

/* resumo (badges) — usa wrap em vez de flex rígido, para caber em painéis estreitos */
.lembretes-wrap .resumo {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.lembretes-wrap .badge {
    flex: 1 1 calc(33.333% - 6px);
    min-width: 80px;
    border-radius: 8px;
    padding: 12px 6px;
    text-align: center;
    color: #fff;
    font-size: 20px;
    font-weight: bold;
    line-height: 1.2;
}

.lembretes-wrap .badge small {
    display: block;
    font-size: 10px;
    font-weight: normal;
    margin-top: 4px;
    opacity: 0.85;
    white-space: normal;
}

.lembretes-wrap .badge.azul  { background-color: #1a1a2e; }
.lembretes-wrap .badge.verde { background-color: #1d9e75; }
.lembretes-wrap .badge.verm  { background-color: #e94560; }

/* cards de cliente */
.lembretes-wrap .card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-left: 4px solid #ccc;
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 10px;
    font-size: 12.5px;
    color: #555;
    line-height: 1.6;
}

.lembretes-wrap .card.sucesso { border-left-color: #1d9e75; }
.lembretes-wrap .card.erro    { border-left-color: #e94560; }

.lembretes-wrap .card .label {
    font-weight: bold;
    color: #1a1a2e;
}

/* Em painéis muito estreitos (sidebar de notificações), badges em 1 coluna */
@container (max-width: 260px) {
    .lembretes-wrap .badge { flex: 1 1 100%; }
}

/* Fallback para browsers sem container queries: usa largura do próprio wrap via classe */
.lembretes-wrap.compacto .badge { flex: 1 1 100%; }
.lembretes-wrap.compacto .resumo { flex-direction: column; }
</style>

<div class="lembretes-wrap<?= $eh_standalone ? ' standalone' : ' compacto' ?>">
    <h2>Envio de Lembretes de Manutencao</h2>
    <?php if ($total === 0): ?>
        <div class="aviso">Nenhum cliente pendente. Todos os clientes com 30 ou mais dias ja receberam o lembrete, ou ainda nao ha contratos com 30 dias.</div>
    <?php else: ?>
        <div class="resumo">
            <div class="badge azul"><?= $total ?><small>Encontrados</small></div>
            <div class="badge verde"><?= $enviados ?><small>Enviados</small></div>
            <div class="badge verm"><?= $falhados ?><small>Falharam</small></div>
        </div>
        <?php foreach ($clientes_resultado as $item): $c = $item['cliente']; ?>
        <div class="card <?= $item['sucesso'] ? 'sucesso' : 'erro' ?>">
            <span class="label">Contrato #<?= htmlspecialchars($c['id_contrato']) ?></span> &mdash; <?= $item['sucesso'] ? 'Enviado com sucesso' : 'Falhou' ?><br>
            <span class="label">Nome:</span> <?= htmlspecialchars($c['nome']) ?><br>
            <span class="label">Email:</span> <?= htmlspecialchars($c['email']) ?><br>
            <span class="label">Inicio:</span> <?= date('d/m/Y', strtotime($c['data_inicio'])) ?><br>
            <span class="label">Dias de contrato:</span> <?= $c['dias'] ?> dias<br>
            <?php if (!$item['sucesso']): ?>
                <span class="label" style="color:#dc3545;">Erro:</span> <span style="color:#dc3545;"><?= htmlspecialchars($item['erro']) ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($eh_standalone): ?>
</body>
</html>
<?php
    endif;
endif;
?>