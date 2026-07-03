<?php
// /models/criar_agendamento.php

include_once("../configuracao/conexao.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if (
        empty($_POST['nome_cliente']) ||
        empty($_POST['placa_carro']) ||
        empty($_POST['modelo_carro']) ||
        empty($_POST['descricao_servico']) ||
        empty($_POST['data_agendada']) ||
        empty($_POST['hora_agendada'])
    ) {
        throw new Exception("Existem campos obrigatórios por preencher.");
    }

    $nome_cliente       = trim($_POST['nome_cliente']);
    $telefone_cliente   = trim($_POST['telefone_cliente'] ?? '');
    $placa_carro        = strtoupper(trim($_POST['placa_carro']));
    $modelo_carro       = trim($_POST['modelo_carro']);
    $descricao_servico  = trim($_POST['descricao_servico']);
    $obs                = trim($_POST['obs'] ?? '');
    $endereco           = trim($_POST['endereco'] ?? 'Oficina');
    $data_agendada      = $_POST['data_agendada'];
    $hora_agendada      = $_POST['hora_agendada'];

    // Validação simples de data: não permitir agendar no passado
    $dataHoraAgendada = DateTime::createFromFormat('Y-m-d H:i', "$data_agendada $hora_agendada");
    if (!$dataHoraAgendada || $dataHoraAgendada < new DateTime()) {
        throw new Exception("A data e hora do agendamento devem estar no futuro.");
    }

    $criado_por = $_SESSION['usuario_id'] ?? null;

    $stmt = $conn->prepare("
        INSERT INTO agendamentos (
            nome_cliente, telefone_cliente, placa_carro, modelo_carro,
            descricao_servico, obs, endereco,
            data_agendada, hora_agendada, criado_por
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // 9 parâmetros "s" (texto) + 1 "i" (criado_por, inteiro ou NULL) = 10 no total
    $stmt->bind_param(
        "sssssssssi",
        $nome_cliente,
        $telefone_cliente,
        $placa_carro,
        $modelo_carro,
        $descricao_servico,
        $obs,
        $endereco,
        $data_agendada,
        $hora_agendada,
        $criado_por
    );

    $stmt->execute();

    header("Location: ../agendamentos.php?criado=1");
    exit;

} catch (Exception $e) {
    die("Erro ao criar agendamento: " . $e->getMessage());
}
