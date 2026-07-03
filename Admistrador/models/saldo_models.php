<?php

if ($_SESSION['nivel'] != "admin") {
    header("Location: login.php");
    exit();
}
 
include("./configuracao/conexao.php");
 
// ================= REGISTAR NOVO FLUXO (SAÍDA / AJUSTE MANUAL) =================
if (isset($_POST['registar_fluxo'])) {

    // Verifica sessão ativa
    if (!isset($_SESSION['id_funcionario'])) {
        header("Location: login.php");
        exit();
    }

    $descricao = trim($_POST['descricao'] ?? '');
    $valor     = floatval($_POST['valor'] ?? 0);
    $fluxo     = $_POST['fluxo'] ?? '';

    $erro = '';

    if ($descricao === '') {
        $erro = "Descrição não pode estar vazia.";
    } elseif ($fluxo !== "entrada" && $fluxo !== "saida") {
        $erro = "Fluxo inválido.";
    } elseif ($valor <= 0) {
        $erro = "Valor inválido.";
    }

    // Só valida saldo disponível se for uma saída
    if ($erro === '' && $fluxo === 'saida') {
        $sql_saldo = "
            SELECT 
                COALESCE(SUM(CASE WHEN fluxo = 'entrada' THEN valor ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN fluxo = 'saida' THEN valor ELSE 0 END), 0) AS saldo_atual
            FROM capital_empresa
        ";
        $result_saldo = mysqli_query($conn, $sql_saldo);
        $saldo_atual  = mysqli_fetch_assoc($result_saldo)['saldo_atual'] ?? 0;

        if ($valor > $saldo_atual) {
            $erro = "Saldo insuficiente. Saldo atual: " . number_format($saldo_atual, 2, ',', '.') . " Kz.";
        }
    }

    if ($erro !== '') {
        header("Location: saldo.php?erro=" . urlencode($erro));
        exit();
    }

    $sql_insert = "
        INSERT INTO capital_empresa (descricao, fluxo, valor, data_registro)
        VALUES (?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param('ssd', $descricao, $fluxo, $valor);

    if (!$stmt->execute()) {
        error_log("Erro ao registar movimento: " . $stmt->error);
        header("Location: saldo.php?erro=" . urlencode("Erro ao registar movimento. Tente novamente."));
        exit();
    }

    $stmt->close();

    header("Location: saldo.php?sucesso=1");
    exit();
}
// ================= CAPITAL TOTAL REAL =================
$sql_capital = "
SELECT 
(
    -- SOMA DOS SERVIÇOS CONCLUÍDOS
    (
        SELECT COALESCE(SUM(total),0) 
        FROM servicos
        WHERE status = 'Concluido'
    )
 
    +
 
    -- SOMA DOS PAGAMENTOS DE CONTRATO (mensais, já efectivamente pagos)
    (
        SELECT COALESCE(SUM(valor),0) 
        FROM pagamentos_contrato
        WHERE status = 'pago'
    )
 
    +
 
    -- ENTRADAS MANUAIS REGISTADAS DIRECTAMENTE EM CAPITAL_EMPRESA
    (
        SELECT COALESCE(SUM(valor),0)
        FROM capital_empresa
        WHERE fluxo = 'entrada'
    )
 
    -
 
    -- SAÍDAS DA EMPRESA
    (
        SELECT COALESCE(SUM(valor),0) 
        FROM capital_empresa 
        WHERE fluxo = 'saida'
    )
 
) AS capital_total
";
 
$res_capital = mysqli_query($conn, $sql_capital);
 
if (!$res_capital) {
    die("Erro SQL capital: " . mysqli_error($conn));
}
 
$dados_capital = mysqli_fetch_assoc($res_capital);
$capital_total = $dados_capital['capital_total'] ?? 0;
 
// ================= TOTAIS SEPARADOS (ENTRADAS / SAÍDAS) PARA OS CARTÕES =================
$sql_totais = "
SELECT
    (
        (SELECT COALESCE(SUM(total),0) FROM servicos WHERE status = 'Concluido')
        +
        (SELECT COALESCE(SUM(valor),0) FROM pagamentos_contrato WHERE status = 'pago')
        +
        (SELECT COALESCE(SUM(valor),0) FROM capital_empresa WHERE fluxo = 'entrada')
    ) AS total_entradas,
    (
        SELECT COALESCE(SUM(valor),0) FROM capital_empresa WHERE fluxo = 'saida'
    ) AS total_saidas
";
$res_totais = mysqli_query($conn, $sql_totais);
if (!$res_totais) {
    die("Erro SQL totais: " . mysqli_error($conn));
}
$dados_totais   = mysqli_fetch_assoc($res_totais);
$total_entradas = $dados_totais['total_entradas'] ?? 0;
$total_saidas   = $dados_totais['total_saidas'] ?? 0;
 
// ================= BUSCAR MOVIMENTAÇÕES =================
$sql = "
SELECT 
    'serviço' AS tipo,
    CONCAT('Serviço - ', s.nome_cliente) AS descricao,
    s.total AS valor,
    'entrada' AS fluxo,
    s.data_registo AS data_movimento
FROM servicos s
WHERE s.status = 'Concluido'
 
UNION ALL
 
SELECT 
    'contrato' AS tipo,
    CONCAT('Contrato #', pc.contrato_id, ' - ', cl.nome, ' (', 
        ELT(pc.mes,'Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'), 
        '/', pc.ano, ')') AS descricao,
    pc.valor AS valor,
    'entrada' AS fluxo,
    pc.data_pagamento AS data_movimento
FROM pagamentos_contrato pc
INNER JOIN contratos c ON c.id_contrato = pc.contrato_id
INNER JOIN clientes cl ON cl.id_cliente = c.id_cliente
WHERE pc.status = 'pago'
 
UNION ALL
 
SELECT 
    'manual' AS tipo,
    CONCAT('Entrada - ', ce.descricao) AS descricao,
    ce.valor AS valor,
    'entrada' AS fluxo,
    ce.data_registro AS data_movimento
FROM capital_empresa ce
WHERE ce.fluxo = 'entrada'
 
UNION ALL
 
SELECT 
    'saída' AS tipo,
    CONCAT('Saída - ', ce.descricao) AS descricao,
    ce.valor AS valor,
    'saida' AS fluxo,
    ce.data_registro AS data_movimento
FROM capital_empresa ce
WHERE ce.fluxo = 'saida'
 
ORDER BY data_movimento DESC
";
 
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('<b>Erro SQL:</b> ' . mysqli_error($conn));
}
?>