<?php

include_once("../configuracao/conexao.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // ================= VALIDAÇÕES BÁSICAS =================

    $required = [
        'placa_carro',
        'nome_cliente',
        'modelo_carro_geral',
        'id_mecanico',
        'nome_servico_geral'
    ];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Campos obrigatórios em falta.");
        }
    }

    // ================= DADOS =================

    $placa       = strtoupper(trim($_POST['placa_carro']));
    $cliente     = trim($_POST['nome_cliente']);
    $modelo      = trim($_POST['modelo_carro_geral']);
    $obs         = trim($_POST['obs'] ?? '');

    $local       = $_POST['local'] ?? 'Oficina';
    $endereco    = trim($_POST['endereco_final'] ?? 'Oficina');

    $descricao   = trim($_POST['nome_servico_geral']);
    $status      = $_POST['status'] ?? 'Pendente';

    $id_mecanico = (int) $_POST['id_mecanico'];
    $id_cliente  = (int) ($_POST['id_cliente'] ?? 0);

    $taxa_desloc = (float) ($_POST['taxa_deslocacao'] ?? 0);
    $taxa_alim   = (float) ($_POST['taxa_alimentacao'] ?? 0);

    // ================= VALIDAÇÕES =================

    if ($id_mecanico <= 0) {
        throw new Exception("Mecânico inválido (ID incorreto).");
    }

    if (strlen($descricao) < 3) {
        throw new Exception("Descrição do serviço inválida.");
    }

    if ($local === 'Fora' && empty($endereco)) {
        throw new Exception("Endereço obrigatório para serviços externos.");
    }

    // ================= VERIFICAR MECÂNICO =================

    $stmt = $conn->prepare("SELECT id_funcionario FROM funcionarios WHERE id_funcionario = ? LIMIT 1");
    $stmt->bind_param("i", $id_mecanico);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception("Mecânico não existe.");
    }

    // ================= VERIFICAR DISPONIBILIDADE =================

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM servicos
        WHERE id_mecanico = ?
        AND status NOT IN ('Concluido', 'Cancelado')
    ");
    $stmt->bind_param("i", $id_mecanico);
    $stmt->execute();

    $busy = $stmt->get_result()->fetch_assoc();

    if ($busy['total'] > 0) {
        throw new Exception("Mecânico já está atribuído a um serviço ativo.");
    }

    // ================= ITENS (NÃO OBRIGATÓRIOS) =================

    $codigos     = $_POST['codigo_da_peca'] ?? [];
    $pecas       = $_POST['peca_materia'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    $precos      = $_POST['preco'] ?? [];

    // REMOVIDO: A validação que bloqueava o código se count($pecas) fosse 0

    // ================= TOTAL =================

    $total = 0;

    // Calcula o valor dos itens apenas se existirem peças enviadas
    for ($i = 0; $i < count($pecas); $i++) {

        $peca = trim($pecas[$i]);

        // Se a linha estiver em branco, ignora e passa para a próxima
        if ($peca === '') {
            continue;
        }

        $qtd   = (float) ($quantidades[$i] ?? 0);
        $preco = (float) ($precos[$i] ?? 0);

        if ($qtd <= 0) {
            throw new Exception("Quantidade inválida em {$peca}");
        }

        if ($preco < 0) {
            throw new Exception("Preço inválido em {$peca}");
        }

        $total += $qtd * $preco;
    }

    // Soma as taxas (incluindo o valor que veio para a Cobrança)
    $total += $taxa_desloc + $taxa_alim;

    // ================= TRANSAÇÃO =================

    $conn->begin_transaction();

    // ================= INSERIR SERVIÇO =================

    if ($id_cliente > 0) {

        // Cliente registado — guarda o id_cliente
        $stmt = $conn->prepare("
            INSERT INTO servicos (
                id_cliente,
                placa_carro,
                nome_cliente,
                modelo_carro,
                endereco,
                obs,
                total,
                id_mecanico,
                status,
                deslocacao,
                cobranca,
                descricao_servico
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssdisdds",
            $id_cliente,
            $placa,
            $cliente,
            $modelo,
            $endereco,
            $obs,
            $total,
            $id_mecanico,
            $status,
            $taxa_desloc,
            $taxa_alim,
            $descricao
        );

    } else {

        // Cliente não registado — id_cliente fica NULL
        $stmt = $conn->prepare("
            INSERT INTO servicos (
                id_cliente,
                placa_carro,
                nome_cliente,
                modelo_carro,
                endereco,
                obs,
                total,
                id_mecanico,
                status,
                deslocacao,
                cobranca,
                descricao_servico
            ) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssdsdds",
            $placa,
            $cliente,
            $modelo,
            $endereco,
            $obs,
            $total,
            $id_mecanico,
            $status,
            $taxa_desloc,
            $taxa_alim,
            $descricao
        );
    }

    $stmt->execute();

    $id_servico = $conn->insert_id;

    // ================= PREPARES PARA ITENS E STOCK =================

    $stmtItem = $conn->prepare("
        INSERT INTO servico_itens (
            id_servico,
            codigo_da_peca,
            peca_materia,
            quantidade,
            preco
        ) VALUES (?, ?, ?, ?, ?)
    ");

    $stmtStock = $conn->prepare("
        SELECT quantidade FROM estoque WHERE codigo = ?
    ");

    $stmtUpdate = $conn->prepare("
        UPDATE estoque SET quantidade = quantidade - ? WHERE codigo = ?
    ");

    // ================= LOOP ITENS =================

    for ($i = 0; $i < count($pecas); $i++) {

        $peca = trim($pecas[$i]);

        // Ignora linhas vazias
        if ($peca === '') {
            continue;
        }

        $codigo = trim($codigos[$i] ?? '');
        $qtd    = (float) ($quantidades[$i] ?? 0);
        $preco  = (float) ($precos[$i] ?? 0);

        $stmtItem->bind_param(
            "issdd",
            $id_servico,
            $codigo,
            $peca,
            $qtd,
            $preco
        );

        $stmtItem->execute();

        // ================= GESTÃO DE STOCK =================

        // Se não houver código de peça associado (ex: serviço manual), não mexe no stock
        if ($codigo === '') {
            continue;
        }

        $stmtStock->bind_param("s", $codigo);
        $stmtStock->execute();

        $res = $stmtStock->get_result();

        if ($res->num_rows === 0) {
            throw new Exception("Código não encontrado no estoque: $codigo");
        }

        $stock = $res->fetch_assoc();

        if ($stock['quantidade'] < $qtd) {
            throw new Exception("Stock insuficiente em {$peca}");
        }

        $stmtUpdate->bind_param("ds", $qtd, $codigo);
        $stmtUpdate->execute();
    }

    // ================= COMMIT =================

    $conn->commit();

    header("Location: ../servicos.php?sucesso=1");
    exit;

} catch (Exception $e) {

    if (isset($conn) && $conn->connect_errno === 0 && $conn->ping()) {
        $conn->rollback();
    }

    die("Erro: " . $e->getMessage());
}