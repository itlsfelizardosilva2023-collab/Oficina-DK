<?php
session_start();
 include_once('./configuracao/config.php');

// Protege a página - ajusta conforme o teu sistema de permissões
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Veículo não especificado.");
}

$id_carro = (int) $_GET['id'];

// --- Busca dados do carro (marca, modelo, matrícula, cliente) ---
$sqlCarro = "SELECT c.id_carro, c.matricula, c.cor,
                    mo.nome AS nome_modelo,
                    ma.nome AS nome_marca,
                    cl.id_cliente, cl.nome AS nome_cliente, cl.telefone
             FROM carros c
             INNER JOIN modelos mo ON c.id_modelo = mo.id_modelo
             INNER JOIN marcas ma ON mo.id_marca = ma.id_marca
             INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente
             WHERE c.id_carro = ?";

$stmt = $conexao->prepare($sqlCarro);
$stmt->bind_param("i", $id_carro);
$stmt->execute();
$resultCarro = $stmt->get_result();

if ($resultCarro->num_rows === 0) {
    die("Veículo não encontrado.");
}
$carro = $resultCarro->fetch_assoc();
$stmt->close();

// --- Filtros (opcionais, vêm da própria página via GET) ---
// Intervalo de datas customizado tem prioridade sobre mês/ano
$dataInicioFiltro = isset($_GET['data_inicio']) && $_GET['data_inicio'] !== '' ? $_GET['data_inicio'] : null;
$dataFimFiltro    = isset($_GET['data_fim']) && $_GET['data_fim'] !== '' ? $_GET['data_fim'] : null;
$usaIntervalo = $dataInicioFiltro || $dataFimFiltro;

$mesFiltro = (!$usaIntervalo && isset($_GET['mes']) && $_GET['mes'] !== '') ? (int) $_GET['mes'] : null;
$anoFiltro = (!$usaIntervalo && isset($_GET['ano']) && $_GET['ano'] !== '') ? (int) $_GET['ano'] : null;

// --- Busca o histórico de serviços deste carro pela matrícula ---
// (a tabela servicos guarda placa_carro como texto, não id_carro)
$sqlServicos = "SELECT s.id, s.descricao_servico, s.obs, s.total, s.cobranca,
                       s.data, s.status,
                       f.nome AS nome_mecanico
                FROM servicos s
                LEFT JOIN funcionarios f ON s.id_mecanico = f.id_funcionario
                WHERE s.placa_carro = ?";

$params = [$carro['matricula']];
$types = "s";

if ($usaIntervalo) {
    if ($dataInicioFiltro) {
        $sqlServicos .= " AND DATE(s.data) >= ?";
        $params[] = $dataInicioFiltro;
        $types .= "s";
    }
    if ($dataFimFiltro) {
        $sqlServicos .= " AND DATE(s.data) <= ?";
        $params[] = $dataFimFiltro;
        $types .= "s";
    }
} else {
    if ($mesFiltro) {
        $sqlServicos .= " AND MONTH(s.data) = ?";
        $params[] = $mesFiltro;
        $types .= "i";
    }
    if ($anoFiltro) {
        $sqlServicos .= " AND YEAR(s.data) = ?";
        $params[] = $anoFiltro;
        $types .= "i";
    }
}

$sqlServicos .= " ORDER BY s.data DESC";

$stmt = $conexao->prepare($sqlServicos);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultServicos = $stmt->get_result();

$servicos = [];
$totalGeral = 0;
while ($row = $resultServicos->fetch_assoc()) {
    $row['pecas'] = [];
    $servicos[] = $row;
    $totalGeral += (float) ($row['cobranca'] ?? $row['total']);
}
$stmt->close();

// --- Busca as peças/materiais usados em cada serviço encontrado ---
if (!empty($servicos)) {
    $idsServicos = array_column($servicos, 'id');
    $placeholders = implode(',', array_fill(0, count($idsServicos), '?'));
    $typesItens = str_repeat('i', count($idsServicos));

    $sqlItens = "SELECT id_servico, codigo_da_peca, peca_materia, quantidade, preco
                 FROM servico_itens
                 WHERE id_servico IN ($placeholders)";

    $stmt = $conexao->prepare($sqlItens);
    $stmt->bind_param($typesItens, ...$idsServicos);
    $stmt->execute();
    $resultItens = $stmt->get_result();

    $itensPorServico = [];
    while ($item = $resultItens->fetch_assoc()) {
        $itensPorServico[$item['id_servico']][] = $item;
    }
    $stmt->close();

    foreach ($servicos as &$s) {
        $s['pecas'] = $itensPorServico[$s['id']] ?? [];
    }
    unset($s);
}

function formatarKz($valor) {
    return number_format((float) $valor, 2, ',', '.') . ' Kz';
}

$mesesNomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Histórico do Veículo - <?= htmlspecialchars($carro['matricula']) ?></title>
<style>
    :root {
        --bg-dark: #0d1117;
        --bg-panel: #161b22;
        --border-color: #30363d;
        --text-main: #e6edf3;
        --text-muted: #8b949e;
        --accent: #58a6ff;
        --accent-gold: #d4af37;
    }
    * { box-sizing: border-box; }
    html { -webkit-text-size-adjust: 100%; }
    body {
        background: var(--bg-dark);
        color: var(--text-main);
        font-family: 'Segoe UI', Arial, sans-serif;
        margin: 0;
        padding: 30px;
    }
    .container { max-width: 1000px; margin: 0 auto; }

    .voltar {
        display: inline-block;
        margin-bottom: 20px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
    }
    .voltar:hover { color: var(--accent); }

    .cabecalho {
        background: var(--bg-panel);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 20px 25px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .cabecalho h1 {
        margin: 0 0 5px 0;
        font-size: 22px;
    }
    .cabecalho p {
        margin: 2px 0;
        color: var(--text-muted);
        font-size: 14px;
        word-break: break-word;
    }
    .cabecalho .destaque { color: var(--accent-gold); font-weight: 600; }

    .barra-acoes {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .filtros {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .filtros select,
    .filtros input[type="date"] {
        background: var(--bg-panel);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        max-width: 100%;
    }
    .filtros select:disabled { opacity: 0.4; }
    .label-data {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--text-muted);
    }
    .separador-filtro { color: var(--text-muted); font-size: 13px; }

    .btn-pecas {
        background: transparent;
        color: var(--accent);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-pecas:hover { background: rgba(88, 166, 255, 0.1); }

    .linha-pecas td {
        background: #10151c;
        padding: 0;
    }
    .linha-pecas .conteudo-pecas {
        padding: 14px 20px;
    }
    .linha-pecas table {
        width: 100%;
        border: none;
        border-radius: 0;
        background: transparent;
        min-width: 0;
    }
    .linha-pecas th, .linha-pecas td {
        background: transparent;
        font-size: 13px;
        padding: 6px 10px;
        border-bottom: 1px solid #21262d;
        white-space: normal;
    }
    .linha-pecas .sem-pecas {
        color: var(--text-muted);
        font-size: 13px;
        padding: 6px 10px;
        font-style: italic;
    }
    .btn {
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn:hover { opacity: 0.9; }
    .btn-limpar {
        background: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }
    .btn-imprimir { background: var(--accent-gold); color: #111; }

    /* Wrapper com rolagem horizontal para a tabela em telas pequenas */
    .tabela-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    table {
        width: 100%;
        min-width: 640px;
        border-collapse: collapse;
        background: var(--bg-panel);
        border: none;
        border-radius: 10px;
    }
    thead { background: #1c232c; }
    th, td {
        padding: 12px 14px;
        text-align: left;
        font-size: 14px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    td:nth-child(2) { white-space: normal; }
    th { color: var(--text-muted); text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: rgba(88, 166, 255, 0.06); }

    .status-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-concluido { background: rgba(46, 160, 67, 0.15); color: #3fb950; }
    .status-pendente { background: rgba(210, 153, 34, 0.15); color: #d29922; }
    .status-andamento { background: rgba(88, 166, 255, 0.15); color: #58a6ff; }
    .status-cancelado { background: rgba(248, 81, 73, 0.15); color: #f85149; }

    .rodape-total {
        text-align: right;
        margin-top: 15px;
        font-size: 16px;
    }
    .rodape-total span { color: var(--accent-gold); font-weight: 700; }

    .vazio {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
    }

    /* --- Responsividade --- */
    @media (max-width: 768px) {
        body { padding: 16px; }
        .cabecalho { padding: 16px 18px; }
        .cabecalho h1 { font-size: 19px; }

        .barra-acoes {
            flex-direction: column;
            align-items: stretch;
        }
        .filtros {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }
        .filtros select,
        .filtros input[type="date"] {
            width: 100%;
        }
        .label-data {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .label-data input { flex: 1; }
        .separador-filtro {
            text-align: center;
            margin: -4px 0;
        }
        .filtros .btn,
        .filtros .btn-limpar {
            width: 100%;
        }
        .btn-imprimir { width: 100%; }

        .rodape-total { text-align: center; font-size: 15px; }
    }

    @media (max-width: 480px) {
        .cabecalho h1 { font-size: 17px; }
        .cabecalho p { font-size: 13px; }
        table { min-width: 560px; }
        th, td { padding: 10px 10px; font-size: 13px; }
    }

    /* --- Impressão --- */
    @media print {
        body { background: #fff; color: #000; padding: 0; }
        .voltar, .barra-acoes, .no-print { display: none !important; }
        .tabela-wrapper { overflow: visible; border: none; }
        table { min-width: 0; }
        .cabecalho, table {
            background: #fff;
            border: 1px solid #999;
            color: #000;
        }
        .cabecalho p, th { color: #333; }
        th { background: #eee; }
        tbody tr:hover { background: none; }
        .linha-pecas { display: table-row !important; }
        .linha-pecas td, .conteudo-pecas { background: #fff !important; }
        .sem-pecas { color: #666; }
    }
</style>
</head>
<body>
<div class="container">

    <a href="carros.php" class="voltar no-print">&larr; Voltar para veículos</a>

    <div class="cabecalho">
        <div>
            <h1>Histórico de Serviços</h1>
            <p><strong>Cliente:</strong> <span class="destaque"><?= htmlspecialchars($carro['nome_cliente']) ?></span></p>
            <p><strong>Veículo:</strong> <?= htmlspecialchars($carro['nome_marca']) ?> <?= htmlspecialchars($carro['nome_modelo']) ?> — <?= htmlspecialchars($carro['cor']) ?></p>
            <p><strong>Matrícula:</strong> <?= htmlspecialchars($carro['matricula']) ?></p>
        </div>
    </div>

    <div class="barra-acoes">
        <form method="GET" class="filtros no-print">
            <input type="hidden" name="id" value="<?= $id_carro ?>">

            <select name="mes" <?= $usaIntervalo ? 'disabled' : '' ?>>
                <option value="">Todos os meses</option>
                <?php foreach ($mesesNomes as $num => $nome): ?>
                    <option value="<?= $num ?>" <?= $mesFiltro === $num ? 'selected' : '' ?>><?= $nome ?></option>
                <?php endforeach; ?>
            </select>
            <select name="ano" <?= $usaIntervalo ? 'disabled' : '' ?>>
                <option value="">Todos os anos</option>
                <?php for ($a = date('Y'); $a >= date('Y') - 3; $a--): ?>
                    <option value="<?= $a ?>" <?= $anoFiltro === $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endfor; ?>
            </select>

            <span class="separador-filtro">ou</span>

            <label class="label-data">De
                <input type="date" name="data_inicio" value="<?= htmlspecialchars($dataInicioFiltro ?? '') ?>">
            </label>
            <label class="label-data">Até
                <input type="date" name="data_fim" value="<?= htmlspecialchars($dataFimFiltro ?? '') ?>">
            </label>

            <button type="submit" class="btn">Filtrar</button>
            <?php if ($mesFiltro || $anoFiltro || $usaIntervalo): ?>
                <a href="historico_veiculo.php?id=<?= $id_carro ?>" class="btn btn-limpar">Limpar</a>
            <?php endif; ?>
        </form>

        <button onclick="window.print()" class="btn btn-imprimir no-print">🖨️ Imprimir</button>
    </div>

    <?php if (empty($servicos)): ?>
        <div class="vazio">Nenhum serviço encontrado para este veículo<?= ($mesFiltro || $anoFiltro) ? ' no período selecionado' : '' ?>.</div>
    <?php else: ?>
        <div class="tabela-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Serviço</th>
                    <th>Mecânico</th>
                    <th>Status</th>
                    <th>Preço Cobrado</th>
                    <th class="no-print">Peças</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servicos as $s): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($s['data'])) ?></td>
                        <td><?= htmlspecialchars($s['descricao_servico'] ?: '—') ?></td>
                        <td><?= htmlspecialchars($s['nome_mecanico'] ?? 'Não atribuído') ?></td>
                        <td><span class="status-badge status-<?= $s['status'] ?>"><?= ucfirst($s['status']) ?></span></td>
                        <td><?= formatarKz($s['cobranca'] ?? $s['total']) ?></td>
                        <td class="no-print">
                            <button type="button" class="btn-pecas" onclick="togglePecas(<?= $s['id'] ?>)">
                                <?= count($s['pecas']) ?> item(ns) ▾
                            </button>
                        </td>
                    </tr>
                    <tr class="linha-pecas" id="pecas-<?= $s['id'] ?>" style="display:none;">
                        <td colspan="6">
                            <div class="conteudo-pecas">
                                <?php if (empty($s['pecas'])): ?>
                                    <div class="sem-pecas">Nenhuma peça ou material registado para este serviço.</div>
                                <?php else: ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Peça / Material</th>
                                                <th>Qtd.</th>
                                                <th>Preço Unit.</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($s['pecas'] as $p): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($p['codigo_da_peca'] ?? '—') ?></td>
                                                    <td><?= htmlspecialchars($p['peca_materia'] ?? '—') ?></td>
                                                    <td><?= (int) $p['quantidade'] ?></td>
                                                    <td><?= formatarKz($p['preco']) ?></td>
                                                    <td><?= formatarKz($p['quantidade'] * $p['preco']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <div class="rodape-total">
            Total do período: <span><?= formatarKz($totalGeral) ?></span>
        </div>
    <?php endif; ?>

</div>

<script>
function togglePecas(idServico) {
    const linha = document.getElementById('pecas-' + idServico);
    if (!linha) return;
    linha.style.display = (linha.style.display === 'none' || linha.style.display === '') ? 'table-row' : 'none';
}
</script>
</body>
</html>