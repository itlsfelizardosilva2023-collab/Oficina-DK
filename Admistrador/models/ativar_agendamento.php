<?php
// /models/ativar_agendamento.php

include_once("../configuracao/conexao.php");
include_once("./agendamento_converter.php");

if (empty($_POST['id_agendamento'])) {
    die("ID de agendamento inválido.");
}

$idAgendamento = (int)$_POST['id_agendamento'];

$resultado = converterAgendamentoEmServico($conn, $idAgendamento);

if (!$resultado['success']) {
    // Mostra o erro mas não trava o sistema - volta para a listagem
    header("Location: ../agendamentos.php?erro=" . urlencode($resultado['message']));
    exit;
}

header("Location: ../agendamentos.php?convertido=1&id_servico=" . $resultado['id_servico']);
exit;
