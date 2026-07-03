<?php
// /models/cancelar_agendamento.php

include_once("../configuracao/conexao.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (empty($_POST['id_agendamento'])) {
    die("ID de agendamento inválido.");
}

$idAgendamento = (int)$_POST['id_agendamento'];

try {
    $stmt = $conn->prepare("
        UPDATE agendamentos
        SET status_agendamento = 'Cancelado'
        WHERE id_agendamento = ? AND status_agendamento = 'Agendado'
    ");
    $stmt->bind_param("i", $idAgendamento);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Agendamento não encontrado ou já processado.");
    }

    header("Location: ../agendamentos.php?cancelado=1");
    exit;

} catch (Exception $e) {
    die("Erro ao cancelar agendamento: " . $e->getMessage());
}
