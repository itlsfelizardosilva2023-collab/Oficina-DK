<?php
// /models/agendamento_converter.php
//
// Função central reutilizável: transforma um agendamento (status 'Agendado')
// num registo real na tabela `servicos`.
//
// Usada em dois lugares:
//   1) Botão manual "Ativar agora" na listagem de agendamentos
//   2) Cron job automático que roda a cada X minutos e converte os
//      agendamentos cuja data/hora já chegou

/**
 * Converte um agendamento específico em serviço.
 *
 * @param mysqli $conn
 * @param int    $idAgendamento
 * @return array{success: bool, message: string, id_servico?: int}
 */
function converterAgendamentoEmServico(mysqli $conn, int $idAgendamento): array
{
    $transacao_iniciada = false;

    try {
        // 1. Busca o agendamento e garante que ainda está "Agendado"
        //    (evita conversão duplicada se dois processos tentarem ao mesmo tempo)
        $stmt = $conn->prepare("
            SELECT * FROM agendamentos
            WHERE id_agendamento = ? AND status_agendamento = 'Agendado'
            FOR UPDATE
        ");
        $stmt->bind_param("i", $idAgendamento);

        $conn->begin_transaction();
        $transacao_iniciada = true;

        $stmt->execute();
        $agendamento = $stmt->get_result()->fetch_assoc();

        if (!$agendamento) {
            // Já foi convertido por outro processo, ou foi cancelado, ou não existe
            $conn->rollback();
            $transacao_iniciada = false;
            return ['success' => false, 'message' => 'Agendamento não encontrado ou já processado.'];
        }

        // 2. Cria o registo em `servicos`
        //    Status inicial = 'Pendente', SEM mecânico atribuído (conforme definido)
        $stmtInsert = $conn->prepare("
            INSERT INTO servicos (
                nome_cliente, placa_carro, modelo_carro,
                descricao_servico, obs, endereco,
                status, id_mecanico, deslocacao, cobranca, total
            ) VALUES (?, ?, ?, ?, ?, ?, 'Pendente', NULL, 0, 0, 0)
        ");

        $stmtInsert->bind_param(
            "ssssss",
            $agendamento['nome_cliente'],
            $agendamento['placa_carro'],
            $agendamento['modelo_carro'],
            $agendamento['descricao_servico'],
            $agendamento['obs'],
            $agendamento['endereco']
        );
        $stmtInsert->execute();

        $idServicoGerado = $stmtInsert->insert_id;

        // 3. Marca o agendamento como Convertido e liga ao serviço criado
        $stmtUpdateAgendamento = $conn->prepare("
            UPDATE agendamentos
            SET status_agendamento = 'Convertido', id_servico_gerado = ?
            WHERE id_agendamento = ?
        ");
        $stmtUpdateAgendamento->bind_param("ii", $idServicoGerado, $idAgendamento);
        $stmtUpdateAgendamento->execute();

        $conn->commit();
        $transacao_iniciada = false;

        return [
            'success' => true,
            'message' => 'Agendamento convertido em serviço com sucesso.',
            'id_servico' => $idServicoGerado,
        ];

    } catch (Exception $e) {
        if ($transacao_iniciada) {
            $conn->rollback();
        }
        error_log("Erro ao converter agendamento #$idAgendamento: " . $e->getMessage());
        return ['success' => false, 'message' => 'Erro ao converter agendamento: ' . $e->getMessage()];
    }
}

/**
 * Busca e converte TODOS os agendamentos cuja data/hora já chegou.
 * Pensado para ser chamado por um cron job a cada poucos minutos.
 *
 * @param mysqli $conn
 * @return array Lista de resultados de cada conversão tentada
 */
function processarAgendamentosVencidos(mysqli $conn): array
{
    $resultados = [];

    $stmt = $conn->prepare("
        SELECT id_agendamento
        FROM agendamentos
        WHERE status_agendamento = 'Agendado'
        AND TIMESTAMP(data_agendada, hora_agendada) <= NOW()
    ");
    $stmt->execute();
    $pendentes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($pendentes as $row) {
        $resultados[] = converterAgendamentoEmServico($conn, (int)$row['id_agendamento']);
    }

    return $resultados;
}
