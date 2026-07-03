<?php

include_once("../configuracao/conexao.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Envia o email de "serviço concluído" para o cliente.
 * Retorna true em sucesso, ou uma string com o erro em caso de falha.
 */
function enviarNotificacaoConcluido($cliente, $servico) {
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
        $mail->Subject = 'O seu veiculo esta pronto - Oficina D.K';

        $mail->Body = '
<!DOCTYPE html>
<html lang="pt">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:30px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

      <tr>
        <td style="background-color:#1a1a2e;padding:30px;text-align:center;">
          <span style="color:#ffffff;font-size:22px;font-weight:bold;letter-spacing:1px;">OFICINA D.K</span>
          <p style="color:#aaaaaa;margin:8px 0 0;font-size:13px;letter-spacing:1px;">SERVICO CONCLUIDO</p>
        </td>
      </tr>

      <tr>
        <td style="padding:35px 40px;background-color:#ffffff;">
          <p style="color:#1a1a2e;font-size:18px;font-weight:bold;margin:0 0 15px;">Caro(a) ' . htmlspecialchars($cliente['nome']) . ',</p>
          <p style="color:#555555;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Informamos que o servico referente ao veiculo com matricula
            <strong style="color:#1a1a2e;">' . htmlspecialchars($servico['placa_carro']) . '</strong>
            (' . htmlspecialchars($servico['modelo_carro']) . ') foi concluido com sucesso.
          </p>

          <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;border-radius:4px;margin-bottom:25px;">
            <tr>
              <td style="padding:18px 20px;">
                <p style="color:#1a1a2e;margin:0 0 6px;font-size:14px;"><strong>Descricao:</strong> ' . htmlspecialchars($servico['descricao_servico']) . '</p>
                <p style="color:#1a1a2e;margin:0;font-size:14px;"><strong>Total:</strong> ' . number_format((float)$servico['total'], 2, ',', '.') . ' Kz</p>
              </td>
            </tr>
          </table>

          <p style="color:#555555;font-size:15px;line-height:1.7;margin:0 0 20px;">
            Pode dirigir-se a oficina para retirar o seu veiculo quando lhe for conveniente.
          </p>

          <p style="color:#999999;font-size:13px;margin:0;line-height:1.6;">
            Para mais informacoes, responda a este email ou contacte-nos diretamente.<br>
            Este e um email automatico gerado pelo sistema da Oficina D.K.
          </p>
        </td>
      </tr>

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

        $mail->AltBody = 'Caro(a) ' . $cliente['nome'] . ', o servico do veiculo ' . $servico['placa_carro'] . ' foi concluido. Total: ' . number_format((float)$servico['total'], 2, ',', '.') . ' Kz. Contacte a Oficina D.K para mais informacoes.';

        $mail->send();
        return true;
    } catch (PHPMailerException $e) {
        return $mail->ErrorInfo;
    }
}

$transacao_iniciada = false;

try {

    if (
        empty($_POST['id']) ||
        empty($_POST['placa_carro']) ||
        empty($_POST['nome_cliente']) ||
        empty($_POST['modelo_carro_geral']) ||
        empty($_POST['id_mecanico'])
    ) {
        throw new Exception("Existem campos obrigatórios por preencher.");
    }

    $id = (int)$_POST['id'];

    $placa   = trim($_POST['placa_carro']);
    $cliente = trim($_POST['nome_cliente']);
    $modelo  = trim($_POST['modelo_carro_geral']);
    $obs     = trim($_POST['obs'] ?? '');
    $status  = trim($_POST['status'] ?? 'Pendente');

    // ================= TRATAMENTO DA DESCRIÇÃO DINÂMICA =================
    $select_servico = $_POST['select_servico_geral'] ?? '';
    if ($select_servico === "Outro") {
        $descricao_servico = trim($_POST['nome_servico_geral_outro'] ?? '');
    } else {
        $descricao_servico = trim($select_servico);
    }

    if (empty($descricao_servico)) {
        throw new Exception("A descrição do serviço não pode estar vazia.");
    }

    // ================= TRATAMENTO DO ENDEREÇO FINAL =================
    $endereco = trim($_POST['endereco_final'] ?? 'Oficina');
    if (empty($endereco)) {
        $endereco = 'Oficina';
    }

    $id_mecanico = (int)$_POST['id_mecanico'];

    $taxa_deslocacao = (float)($_POST['taxa_deslocacao'] ?? 0);
    $taxa_alimentacao = (float)($_POST['taxa_alimentacao'] ?? 0);

    // Validação de regras de negócio do mecânico responsável
    $stmtMecanico = $conn->prepare("
        SELECT COUNT(*) total
        FROM servicos
        WHERE id_mecanico = ?
        AND status NOT IN ('Concluido','Cancelado')
        AND id != ?
    ");

    $stmtMecanico->bind_param("ii", $id_mecanico, $id);
    $stmtMecanico->execute();

    $res = $stmtMecanico->get_result()->fetch_assoc();

    if ($res['total'] > 0 && $status != 'Concluido' && $status != 'Cancelado') {
        throw new Exception("Este mecânico já possui um serviço ativo.");
    }

    $codigos     = $_POST['codigo_da_peca'] ?? [];
    $pecas       = $_POST['peca_materia'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    $precos      = $_POST['preco'] ?? [];

    $totalGeral = 0;

    for ($i = 0; $i < count($pecas); $i++) {

        if (empty(trim($pecas[$i]))) {
            continue;
        }

        $qtd = (float)$quantidades[$i];
        $preco = (float)$precos[$i];

        $totalGeral += ($qtd * $preco);
    }

    $totalGeral += $taxa_deslocacao + $taxa_alimentacao;

    $conn->begin_transaction();
    $transacao_iniciada = true;

    // Captura o status ANTES desta atualização para evitar loops e spam de emails
    $stmtStatusAnterior = $conn->prepare("SELECT status FROM servicos WHERE id = ?");
    $stmtStatusAnterior->bind_param("i", $id);
    $stmtStatusAnterior->execute();
    $statusAnteriorRow = $stmtStatusAnterior->get_result()->fetch_assoc();
    $statusAnterior = $statusAnteriorRow['status'] ?? null;

    $stmtUpdate = $conn->prepare("
        UPDATE servicos SET
            placa_carro = ?,
            nome_cliente = ?,
            modelo_carro = ?,
            endereco = ?,
            obs = ?,
            total = ?,
            id_mecanico = ?,
            status = ?,
            deslocacao = ?,
            cobranca = ?,
            descricao_servico = ?
        WHERE id = ?
    ");

    $stmtUpdate->bind_param(
        "sssssdisddsi",
        $placa,
        $cliente,
        $modelo,
        $endereco,
        $obs,
        $totalGeral,
        $id_mecanico,
        $status,
        $taxa_deslocacao,
        $taxa_alimentacao,
        $descricao_servico,
        $id
    );

    $stmtUpdate->execute();

    // Saneamento e atualização relacional de itens
    $stmtDelete = $conn->prepare("
        DELETE FROM servico_itens
        WHERE id_servico = ?
    ");

    $stmtDelete->bind_param("i", $id);
    $stmtDelete->execute();

    $stmtItem = $conn->prepare("
        INSERT INTO servico_itens (
            id_servico,
            codigo_da_peca,
            peca_materia,
            quantidade,
            preco
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < count($pecas); $i++) {

        $codigo = trim($codigos[$i] ?? '');
        $peca   = trim($pecas[$i] ?? '');

        if ($peca == '') {
            continue;
        }

        $qtd   = (float)$quantidades[$i];
        $preco = (float)$precos[$i];

        $stmtItem->bind_param(
            "issdd",
            $id,
            $codigo,
            $peca,
            $qtd,
            $preco
        );

        $stmtItem->execute();
    }

    $conn->commit();
    $transacao_iniciada = false;

    // ───── Notificação por email ao cliente ─────
    $mudouParaConcluido = ($statusAnterior !== 'Concluido') && ($status === 'Concluido');

    if ($mudouParaConcluido) {
        $stmtCliente = $conn->prepare("
            SELECT cl.nome, cl.email
            FROM carros ca
            INNER JOIN clientes cl ON cl.id_cliente = ca.id_cliente
            WHERE ca.matricula = ?
            LIMIT 1
        ");
        $stmtCliente->bind_param("s", $placa);
        $stmtCliente->execute();
        $clienteEncontrado = $stmtCliente->get_result()->fetch_assoc();

        if ($clienteEncontrado && !empty($clienteEncontrado['email'])) {
            $servicoParaEmail = [
                'placa_carro'        => $placa,
                'modelo_carro'       => $modelo,
                'descricao_servico'  => $descricao_servico,
                'total'              => $totalGeral,
            ];

            $resultadoEnvio = enviarNotificacaoConcluido($clienteEncontrado, $servicoParaEmail);
            if ($resultadoEnvio !== true) {
                error_log("Falha ao enviar email de servico concluido (servico #$id): " . $resultadoEnvio);
            }
        } else {
            error_log("Servico #$id concluido, mas nao foi possivel localizar email do cliente (placa: $placa).");
        }
    }

    header("Location: ../servicos.php?update=1");
    exit;

} catch (Exception $e) {

    if ($transacao_iniciada) {
        $conn->rollback();
    }

    die("Erro: " . $e->getMessage());
}