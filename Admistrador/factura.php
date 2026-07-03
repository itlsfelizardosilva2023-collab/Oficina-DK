<?php
session_start();
include_once("./configuracao/conexao.php");

$id_servico = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_servico == 0) {
    die("Serviço inválido");
}

/* ================= SERVIÇO ================= */
$stmt = $conn->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->bind_param("i", $id_servico);
$stmt->execute();
$result_servico = $stmt->get_result();

if (!$result_servico || $result_servico->num_rows == 0) {
    die("Serviço não encontrado");
}

$servico = $result_servico->fetch_assoc();


$servico = $result_servico->fetch_assoc();



/* ================= ITENS (PEÇAS) ================= */
$stmt = $conn->prepare("SELECT * FROM servico_itens WHERE id_servico = ?");
$stmt->bind_param("i", $id_servico);
$stmt->execute();
$result_itens = $stmt->get_result();

$itens = [];
$subtotal_pecas = 0;

while ($row = $result_itens->fetch_assoc()) {
    $row['subtotal'] = $row['quantidade'] * $row['preco'];
    $subtotal_pecas += $row['subtotal'];
    $itens[] = $row;
}

/* ================= MÃO DE OBRA ================= */
// Assumi que 'total' em servicos é o valor da mão de obra/serviço prestado,
// SEPARADO do custo das peças. Se 'total' já incluir as peças, ajusta aqui.
$mao_de_obra = isset($servico['total']) ? (float)$servico['total'] : 0;

/* ================= TOTAIS COM IVA ================= */
$subtotal_geral = $subtotal_pecas + $mao_de_obra;
$taxa_iva       = 0.14; // 14% IVA, padrão Angola
$valor_iva      = $subtotal_geral * $taxa_iva;
$total_final    = $subtotal_geral + $valor_iva;

/* ================= STATUS DO SERVIÇO ================= */
$status_textos = [
    'andamento' => 'Em Andamento',
    'concluido' => 'Concluído',
];
$status_servico = $status_textos[$servico['status'] ?? ''] ?? 'Pendente';

/* ================= VERIFICAR FACTURA ================= */
$stmt = $conn->prepare("SELECT * FROM facturas WHERE id_servico = ?");
$stmt->bind_param("i", $id_servico);
$stmt->execute();
$factura_existente = $stmt->get_result()->fetch_assoc();

/* ================= GERAR NÚMERO ================= */
function gerarNumeroFactura($conn) {
    $result  = $conn->query("SELECT id FROM facturas ORDER BY id DESC LIMIT 1");
    $row     = $result->fetch_assoc();
    $novo_id = ($row) ? $row['id'] + 1 : 1;
    return "FT" . date('Y') . "/" . str_pad($novo_id, 4, "0", STR_PAD_LEFT);
}

/* ================= INSERIR OU ATUALIZAR FACTURA ================= */
if ($factura_existente) {

    $id_factura = $factura_existente['id'];
    $numero_factura = $factura_existente['numero_factura'];

    $stmt = $conn->prepare("
        UPDATE facturas SET
            nome_cliente = ?, placa_carro = ?, modelo_carro = ?, endereco = ?, total = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssssdi",
        $servico['nome_cliente'],
        $servico['placa_carro'],
        $servico['modelo_carro'],
        $servico['endereco'],
        $total_final,
        $id_factura
    );
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM factura_itens WHERE id_factura = ?");
    $stmt->bind_param("i", $id_factura);
    $stmt->execute();

} else {

    $numero_factura = gerarNumeroFactura($conn);

    $stmt = $conn->prepare("
        INSERT INTO facturas
        (id_servico, nome_cliente, placa_carro, modelo_carro, endereco, total, numero_factura)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "issssds",
        $id_servico,
        $servico['nome_cliente'],
        $servico['placa_carro'],
        $servico['modelo_carro'],
        $servico['endereco'],
        $total_final,
        $numero_factura
    );
    $stmt->execute();

    $id_factura = $conn->insert_id;
}

/* ================= ITENS FACTURA ================= */
$stmt = $conn->prepare("
    INSERT INTO factura_itens
    (id_factura, codigo_da_peca, peca_materia, quantidade, preco, subtotal)
    VALUES (?, ?, ?, ?, ?, ?)
");

foreach ($itens as $item) {
    $stmt->bind_param(
        "issidd",
        $id_factura,
        $item['codigo_da_peca'],
        $item['peca_materia'],
        $item['quantidade'],
        $item['preco'],
        $item['subtotal']
    );
    $stmt->execute();
}

$data_emissao = date('d/m/Y');
$data_servico = isset($servico['data_registo']) ? date('d/m/Y', strtotime($servico['data_registo'])) : '—';
?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<title>Factura <?= htmlspecialchars($numero_factura) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">


<style>
* { box-sizing: border-box; }
@font-face {
    font-family: 'Inter';
    src: url('Inter-Italic-VariableFont_opsz,wght.ttf');

}

body { 
  font-family: 'Inter', sans-serif;
    background: #eef1f5;
    padding: 30px 15px;
    color: #1c2733;
}  

.fatura {
    background: #fff;
    max-width: 850px;
    margin: 0 auto;
    border-radius: 4px;
    box-shadow: 0 8px 30px rgba(10, 31, 68, 0.12);
    border-top: 6px solid #1e90ff;
    overflow: hidden;
}
.barra-topo {
    max-width: 850px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
}

.btn-voltar {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    color: #0a1f44;
    border: 1px solid #d8dce3;
    padding: 9px 18px;
    border-radius: 6px;
    font-family: 'Jost', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
}

.btn-voltar:hover {
    background: #f3f5f8;
    box-shadow: 0 2px 8px rgba(10, 31, 68, 0.08);
}

.cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 35px 40px 25px;
    border-bottom: 2px solid #141412;
}

.empresa h1 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 28px;
    color: #0a1f44;
    margin: 0 0 4px;
    letter-spacing: 0.5px;
}

.empresa p {
    margin: 2px 0;
    font-size: 13px;
    color: #5a6473;
}

.fatura-meta {
    text-align: right;
}

.fatura-meta .titulo {
    font-family: 'Cormorant Garamond', serif;
    font-size: 22px;
    color: #000000;
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 6px;
}

.fatura-meta p {
    margin: 2px 0;
    font-size: 13px;
    color: #5a6473;
}

.fatura-meta b { color: #0a1f44; }

.corpo {
    padding: 30px 40px;
}

.secao-titulo {
    font-family: 'Cormorant Garamond', serif;
    font-size: 15px;
    font-weight: 700;
    color: #0a1f44;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0 0 12px;
    padding-bottom: 6px;
    border-bottom: 1px solid #e4e7ec;
}

.grid-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 30px;
    margin-bottom: 28px;
}

.grid-info p {
    margin: 0;
    font-size: 14px;
}

.grid-info b {
    color: #0a1f44;
    font-weight: 600;
}

.status-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    background: rgba(25, 135, 84, 0.12);
    color: #198754;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th {
    background: #1e90ff;
    color: #fff;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 10px;
    text-align: left;
}

th.num, td.num { text-align: right; }

td {
    padding: 11px 10px;
    font-size: 13.5px;
    border-bottom: 1px solid #edeff2;
}

tr:last-child td { border-bottom: none; }

.linha-obra td {
    background: #f7f8fa;
    font-style: italic;
}

.totais {
    margin-left: auto;
    width: 280px;
    margin-top: 10px;
}

.totais div {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-size: 14px;
}

.totais .final {
    border-top: 2px solid #c9a227;
    margin-top: 6px;
    padding-top: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #0a1f44;
}

.rodape {
    padding: 20px 40px 35px;
    border-top: 1px solid #e4e7ec;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rodape p {
    font-size: 12px;
    color: #8a93a3;
    margin: 0;
}

.btn-imprimir {
    padding: 10px 24px;
    background: #1eff90;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    letter-spacing: 0.5px;
}

.btn-imprimir:hover { background: #132a5c; }

@media print {
    body { background: #fff; padding: 0; }
    .fatura { box-shadow: none; border-radius: 0; }
    .btn-imprimir { display: none; }
    .barra-topo{ display: none; }
}
</style>
</head>

<body>
<div class="barra-topo">
    <a href="servicos.php" class="btn-voltar">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Voltar
    </a>
</div>
<div class="fatura">

    <div class="cabecalho">
        <div class="empresa">
            <h1><img src="../views/img/WhatsApp_Image_2026-02-27_at_12.49.27.1.png" width="80px"></h1>
            <p>Capalanga Vila, Luanda — Angola</p>
            <p>NIF: 5417045870</p>
        </div>
        <div class="fatura-meta">
            <div class="titulo">FACTURA</div>
            <p><b>Nº:</b> <?= htmlspecialchars($numero_factura) ?></p>
            <p><b>Emissão:</b> <?= $data_emissao ?></p>
            <p><b>Data do Serviço:</b> <?= $data_servico ?></p>
        </div>
    </div>

    <div class="corpo">

        <div class="secao-titulo">Dados do Cliente e Veículo</div>
        <div class="grid-info">
            <p><b>Cliente:</b> <?= htmlspecialchars($servico['nome_cliente']) ?></p>
            <p><b>Modelo do Carro:</b> <?= htmlspecialchars($servico['modelo_carro']) ?></p>
            <p><b>Placa:</b> <?= htmlspecialchars($servico['placa_carro']) ?></p>
            <p><b>Endereço:</b> <?= htmlspecialchars($servico['endereco']) ?></p>
            <p><b>Status do Serviço:</b> <span class="status-badge"><?= $status_servico ?></span></p>
        </div>

        <div class="secao-titulo">Peças e Materiais</div>
        <table>
            <tr>
                <th>Código</th>
                <th>Peça / Material</th>
                <th class="num">Qtd</th>
                <th class="num">Preço (Kz)</th>
                <th class="num">Subtotal (Kz)</th>
            </tr>

            <?php if (count($itens) > 0): ?>
                <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['codigo_da_peca']) ?></td>
                    <td><?= htmlspecialchars($item['peca_materia']) ?></td>
                    <td class="num"><?= $item['quantidade'] ?></td>
                    <td class="num"><?= number_format($item['preco'], 2, ',', '.') ?></td>
                    <td class="num"><?= number_format($item['subtotal'], 2, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; color:#8a93a3;">Nenhuma peça registada</td></tr>
            <?php endif; ?>

            <tr class="linha-obra">
                <td colspan="4">Mão de Obra / Serviço Prestado</td>
                <td class="num"><?= number_format($mao_de_obra, 2, ',', '.') ?></td>
            </tr>
        </table>

        <div class="totais">
            <div><span>Subtotal</span><span><?= number_format($subtotal_geral, 2, ',', '.') ?> Kz</span></div>
            <div><span>IVA (14%)</span><span><?= number_format($valor_iva, 2, ',', '.') ?> Kz</span></div>
            <div class="final"><span>TOTAL</span><span><?= number_format($total_final, 2, ',', '.') ?> Kz</span></div>
        </div>

    </div>

    <div class="rodape">
        <p>Obrigado pela confiança na Oficina Capapelo.</p>
        <button class="btn-imprimir" onclick="window.print()">Imprimir Factura</button>
    </div>

</div>

</body>
</html>