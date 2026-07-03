<?php
// ============================================================
// ficha_servico.php
// Ficha de Serviço — busca completa de dados via JOIN
// ============================================================
  include_once('./configuracao/conexao.php');

// --------------------------------------------------------
// 1. VALIDAÇÃO DO ID DO SERVIÇO
// --------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Serviço não especificado.");
}
$id_servico = (int) $_GET['id'];

// --------------------------------------------------------
// 2. BUSCAR DADOS DO SERVIÇO + CLIENTE + VIATURA + MECÂNICO
//    JOIN: servicos -> carros (via matrícula) -> modelos -> marcas
//          servicos -> clientes (via id_cliente do carro)
//          servicos -> funcionarios (via id_mecanico)
// --------------------------------------------------------
$sql_servico = "
    SELECT
        s.id,
        s.placa_carro,
        s.nome_cliente   AS nome_cliente_texto,
        s.modelo_carro   AS modelo_carro_texto,
        s.endereco       AS endereco_texto,
        s.obs,
        s.total,
        s.data_registo,
        s.data,
        s.status,
        s.deslocacao,
        s.cobranca,
        s.id_mecanico,

        c.id_carro,
        c.cor,
        c.id_cliente,

        mo.nome   AS modelo_nome,
        ma.nome   AS marca_nome,

        cl.nome      AS cliente_nome,
        cl.telefone  AS cliente_telefone,
        cl.numero_bi AS cliente_bi,
        cl.endereco  AS cliente_endereco,

        f.nome   AS mecanico_nome,
        f.cargo  AS mecanico_cargo

    FROM servicos s
    LEFT JOIN carros      c  ON c.matricula  = s.placa_carro
    LEFT JOIN modelos     mo ON mo.id_modelo = c.id_modelo
    LEFT JOIN marcas      ma ON ma.id_marca  = mo.id_marca
    LEFT JOIN clientes    cl ON cl.id_cliente = c.id_cliente
    LEFT JOIN funcionarios f ON f.id_funcionario = s.id_mecanico
    WHERE s.id = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql_servico);
$stmt->bind_param("i", $id_servico);
$stmt->execute();
$resultado = $stmt->get_result();
$servico = $resultado->fetch_assoc();
$stmt->close();

if (!$servico) {
    die("Serviço não encontrado.");
}

// --------------------------------------------------------
// 3. FALLBACK: se não houver carro cadastrado com essa matrícula,
//    usar os campos de texto que já estavam salvos em `servicos`
// --------------------------------------------------------
$cliente_nome_final = $servico['cliente_nome'] ?? $servico['nome_cliente_texto'];
$marca_modelo_final = trim(
    ($servico['marca_nome'] ?? '') . ' ' . ($servico['modelo_nome'] ?? $servico['modelo_carro_texto'])
);
$endereco_final = $servico['cliente_endereco'] ?? $servico['endereco_texto'];

// --------------------------------------------------------
// 4. BUSCAR ITENS DO SERVIÇO (peças + mão de obra, lista única)
//    Tabela servico_itens não distingue tipo — tudo numa lista
// --------------------------------------------------------
$sql_itens = "
    SELECT
        id,
        codigo_da_peca,
        peca_materia AS descricao,
        quantidade,
        preco
    FROM servico_itens
    WHERE id_servico = ?
    ORDER BY id ASC
";

$stmt2 = $conexao->prepare($sql_itens);
$stmt2->bind_param("i", $id_servico);
$stmt2->execute();
$itens = $stmt2->get_result();
$stmt2->close();

// --------------------------------------------------------
// 5. CALCULAR TOTAL (soma de quantidade x preço de cada item)
//    + deslocação, se houver
// --------------------------------------------------------
$total = 0;
$linhas_itens = []; // guardamos em array pra poder iterar 2x (cálculo + render) sem reabrir o result set

while ($row = $itens->fetch_assoc()) {
    $subtotal = $row['quantidade'] * $row['preco'];
    $total += $subtotal;
    $row['subtotal'] = $subtotal;
    $linhas_itens[] = $row;
}

if (!empty($servico['deslocacao'])) {
    $total += (float) $servico['deslocacao'];
}

?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<title>Ficha de Serviço Nº <?= $servico['id'] ?></title>
<link rel="stylesheet" href="../views/css/fatura_contrato.css?v=10">
</head>

<body>

<div class="toolbar">
    <button class="btn btn-print" onclick="window.print()">Imprimir</button>
    <a href="servicos.php"><button class="btn btn-pdf">Voltar</button></a>
</div>

<div class="paper">
    <div class="watermark">DOMINGOS KAPAPELO</div>

    <div class="content">

        <!-- HEADER -->
        <div class="header">
            <div class="logo-row">
                <img src="../views/img/logo.png" width="90">
                <div>
                    <div class="company-name">Domingos Kapapelo</div>
                    <div class="company-sub">Ficha de Serviço</div>
                </div>
            </div>

            <div class="meta-block">
                <div class="meta-num">Serviço Nº <?= str_pad($servico['id'], 4, '0', STR_PAD_LEFT) ?></div>
                <div class="meta-date">
                    <?= date("d/m/Y", strtotime($servico['data'] ?? $servico['data_registo'])) ?>
                </div>
            </div>
        </div>

        <!-- CLIENTE + VIATURA -->
        <div class="contract-body">
            <p>
                Cliente: <strong><?= htmlspecialchars($cliente_nome_final) ?></strong><br>
                Endereço: <strong><?= htmlspecialchars($endereco_final) ?></strong><br>
                Viatura: <strong><?= htmlspecialchars($marca_modelo_final) ?></strong><br>
                Cor: <strong><?= htmlspecialchars($servico['cor'] ?? '—') ?></strong><br>
                Matrícula: <strong><?= htmlspecialchars($servico['placa_carro']) ?></strong><br>
                Mecânico responsável: <strong><?= htmlspecialchars($servico['mecanico_nome'] ?? 'Não atribuído') ?></strong><br>
                Status:
                <strong>
                    <?php
                        $status_labels = [
                            'concluido' => 'Concluído',
                            'pendente'  => 'Pendente',
                            'andamento' => 'Em Andamento',
                            'cancelado' => 'Cancelado',
                        ];
                        echo $status_labels[$servico['status']] ?? htmlspecialchars($servico['status']);
                    ?>
                </strong>
            </p>
            <?php if (!empty($servico['obs'])): ?>
            <p>Observações: <em><?= htmlspecialchars($servico['obs']) ?></em></p>
            <?php endif; ?>
        </div>

        <!-- ITENS DO SERVIÇO -->
        <div class="sec-header"><span>Itens do Serviço</span></div>

        <table class="vehicle-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Qtd</th>
                    <th>Preço Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($linhas_itens)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Nenhum item registado para este serviço.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($linhas_itens as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['codigo_da_peca'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($item['descricao']) ?></td>
                        <td><?= (int) $item['quantidade'] ?></td>
                        <td><?= number_format($item['preco'], 2, ',', '.') ?> Akz</td>
                        <td><?= number_format($item['subtotal'], 2, ',', '.') ?> Akz</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($servico['deslocacao'])): ?>
                <tr>
                    <td>—</td>
                    <td>Taxa de deslocação</td>
                    <td>1</td>
                    <td><?= number_format($servico['deslocacao'], 2, ',', '.') ?> Akz</td>
                    <td><?= number_format($servico['deslocacao'], 2, ',', '.') ?> Akz</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TOTAL -->
        <div class="total-row">
            <div class="total-box">
                <span class="total-label">Total Geral</span>
                <span class="total-amount">
                    <?= number_format($total, 2, ',', '.') ?> Akz
                </span>
            </div>
        </div>

        <!-- ASSINATURAS -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-space"></div>
                <div class="sig-name">Oficina Domingos Kapapelo</div>
            </div>

            <div class="sig-block">
                <div class="sig-space"></div>
                <div class="sig-name"><?= htmlspecialchars($cliente_nome_final) ?></div>
                <div class="sig-role">Cliente</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>