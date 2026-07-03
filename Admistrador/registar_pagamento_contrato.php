<?php
// registar_pagamento_contrato.php

ob_start(); // captura qualquer warning/notice antes de sair, para não estragar o JSON
error_reporting(E_ALL);
ini_set('display_errors', 0); // erros não vão para a saída, só para o log

header('Content-Type: application/json; charset=utf-8');

require_once "configuracao/conexao.php";

// conexao.php só define $conn — criar alias para $conexao usado no resto do ficheiro
if (!isset($conexao) && isset($conn) && $conn instanceof mysqli) {
    $conexao = $conn;
}

function responder($sucesso, $extra = []) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode(array_merge(['sucesso' => $sucesso], $extra));
    exit;
}

set_exception_handler(function ($e) {
    responder(false, ['erro' => 'Erro interno: ' . $e->getMessage()]);
});

if (!isset($conexao) || !($conexao instanceof mysqli)) {
    responder(false, ['erro' => 'Falha na ligação à base de dados.']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, ['erro' => 'Método inválido.']);
}

$contrato_id   = intval($_POST['contrato_id'] ?? 0);
$mes           = intval($_POST['mes'] ?? 0);
$ano           = intval($_POST['ano'] ?? 0);
$num_transacao = trim($_POST['num_transacao'] ?? '');
$observacao    = trim($_POST['observacao'] ?? '');
$data_pag      = $_POST['data_pagamento'] ?? date('Y-m-d');
$valor         = floatval($_POST['valor'] ?? 0);

if (!$contrato_id || !$mes || !$ano || $num_transacao === '') {
    responder(false, ['erro' => 'Dados incompletos. Verifique o número de transação.']);
}
if ($mes < 1 || $mes > 12) {
    responder(false, ['erro' => 'Mês inválido.']);
}

$d = DateTime::createFromFormat('Y-m-d', $data_pag);
if (!$d) { $data_pag = date('Y-m-d'); }

// Já existe pagamento para este mês/ano?
$chk = $conexao->prepare("SELECT id FROM pagamentos_contrato WHERE contrato_id = ? AND mes = ? AND ano = ?");
if (!$chk) { responder(false, ['erro' => 'Erro SQL (verificação): ' . $conexao->error]); }
$chk->bind_param("iii", $contrato_id, $mes, $ano);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    responder(false, ['erro' => 'Este mês já tem pagamento registado.']);
}

// Confirmar que o contrato existe
$stmtC = $conexao->prepare("SELECT id_contrato FROM contratos WHERE id_contrato = ?");
$stmtC->bind_param("i", $contrato_id);
$stmtC->execute();
if (!$stmtC->get_result()->fetch_assoc()) {
    responder(false, ['erro' => 'Contrato não encontrado.']);
}

$conexao->begin_transaction();

try {
    $stmt = $conexao->prepare("
        INSERT INTO pagamentos_contrato
        (contrato_id, mes, ano, num_transacao, observacao, data_pagamento, valor, status, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pago', NOW())
    ");
    if (!$stmt) { throw new Exception('Erro ao preparar inserção: ' . $conexao->error); }
    $stmt->bind_param("iiisssd", $contrato_id, $mes, $ano, $num_transacao, $observacao, $data_pag, $valor);
    if (!$stmt->execute()) { throw new Exception('Erro ao registar pagamento: ' . $stmt->error); }

    $meses_nomes = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                    7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
    $descricao = "Pagamento mensal contrato #{$contrato_id} — {$meses_nomes[$mes]} {$ano}";

    $stmtCap = $conexao->prepare("
        INSERT INTO capital_empresa (descricao, id_servico, id_contrato, fluxo, valor, data_registro)
        VALUES (?, NULL, ?, 'entrada', ?, NOW())
    ");
    if (!$stmtCap) { throw new Exception('Erro ao preparar lançamento financeiro: ' . $conexao->error); }
    $stmtCap->bind_param("sid", $descricao, $contrato_id, $valor);
    if (!$stmtCap->execute()) { throw new Exception('Erro ao lançar entrada financeira: ' . $stmtCap->error); }

    $conexao->commit();
    responder(true, ['mensagem' => 'Pagamento registado com sucesso.']);

} catch (Exception $e) {
    $conexao->rollback();
    responder(false, ['erro' => $e->getMessage()]);
}