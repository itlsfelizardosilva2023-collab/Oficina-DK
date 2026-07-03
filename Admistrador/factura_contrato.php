<?php
session_start();
include_once("./configuracao/conexao.php");

$id_servico = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_servico == 0) {
    die("Serviço inválido");
}

/* ================= SERVIÇO ================= */
$sql_servico = "SELECT * FROM servicos WHERE id = $id_servico";
$result_servico = mysqli_query($conn, $sql_servico);

if (!$result_servico || mysqli_num_rows($result_servico) == 0) {
    die("Serviço não encontrado");
}

$servico = mysqli_fetch_assoc($result_servico);

/* ================= ITENS ================= */
$sql_itens = "SELECT * FROM servico_itens WHERE id_servico = $id_servico";
$result_itens = mysqli_query($conn, $sql_itens);

$itens = [];
$total_calculado = 0;

while ($row = mysqli_fetch_assoc($result_itens)) {
    $row['subtotal'] = $row['quantidade'] * $row['preco'];
    $total_calculado += $row['subtotal'];
    $itens[] = $row;
}

/* ================= VERIFICAR FACTURA ================= */
$check = mysqli_query($conn,
    "SELECT * FROM facturas WHERE id_servico = $id_servico"
);

$factura_existente = mysqli_fetch_assoc($check);

/* ================= GERAR NÚMERO ================= */
function gerarNumeroFactura($conn) {

    $result = mysqli_query($conn,
        "SELECT id FROM facturas ORDER BY id DESC LIMIT 1"
    );

    $row = mysqli_fetch_assoc($result);

    $novo_id = ($row) ? $row['id'] + 1 : 1;

    return "FT" . str_pad($novo_id, 4, "0", STR_PAD_LEFT);
}

/* ================= INSERIR OU ATUALIZAR FACTURA ================= */
if ($factura_existente) {

    $id_factura = $factura_existente['id'];

    mysqli_query($conn, "
        UPDATE facturas SET
            nome_cliente = '{$servico['nome_cliente']}',
            placa_carro = '{$servico['placa_carro']}',
            modelo_carro = '{$servico['modelo_carro']}',
            endereco = '{$servico['endereco']}',
            total = $total_calculado
        WHERE id = $id_factura
    ");

    mysqli_query($conn,
        "DELETE FROM factura_itens WHERE id_factura = $id_factura"
    );

} else {

    $numero_factura = gerarNumeroFactura($conn);

    mysqli_query($conn, "
        INSERT INTO facturas
        (id_servico, nome_cliente, placa_carro, modelo_carro, endereco, total, numero_factura)
        VALUES (
            $id_servico,
            '{$servico['nome_cliente']}',
            '{$servico['placa_carro']}',
            '{$servico['modelo_carro']}',
            '{$servico['endereco']}',
            $total_calculado,
            '$numero_factura'
        )
    ");

    $id_factura = mysqli_insert_id($conn);
}

/* ================= ITENS FACTURA ================= */
foreach ($itens as $item) {

    $subtotal = $item['quantidade'] * $item['preco'];

    mysqli_query($conn, "
        INSERT INTO factura_itens
        (id_factura, codigo_da_peca, peca_materia, quantidade, preco, subtotal)
        VALUES (
            $id_factura,
            '{$item['codigo_da_peca']}',
            '{$item['peca_materia']}',
            {$item['quantidade']},
            {$item['preco']},
            $subtotal
        )
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Factura</title>

<style>
@font-face {
    font-family: 'Inter';
    src: url('Inter-Italic-VariableFont_opsz,wght.ttf');

}

body{
    font-family: Arial;
    background: #f5f6fa;
    padding: 20px;
    font-family: 'Inter', sans-serif;
}

.card{
    background: white;
    padding: 20px;
    border-radius: 12px;
    max-width: 900px;
    margin: auto;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header{
    text-align:center;
}

.header h2{
    margin:0;
}

.info{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:10px;
    margin-top:20px;
}

table{
    width:100%;
    border-collapse: collapse;
    margin-top:20px;
}

th{
    background:#2c3e50;
    color:white;
    padding:10px;
}

td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

.total{
    text-align:right;
    margin-top:20px;
    font-size:20px;
    font-weight:bold;
}

.btn{
    margin-top:20px;
    padding:10px;
    background:#2c3e50;
    color:white;
    border:none;
    cursor:pointer;
}
</style>

</head>

<body>

<div class="card">

<div class="header">
    <h2>FACTURA DE SERVIÇO</h2>
    <p>
        <b>Nº:</b>
        <?= $factura_existente['numero_factura'] ?? $numero_factura ?>
    </p>
</div>

<div class="info">
    <p><b>Cliente:</b> <?= $servico['nome_cliente'] ?></p>
    <p><b>Carro:</b> <?= $servico['modelo_carro'] ?></p>
    <p><b>Placa:</b> <?= $servico['placa_carro'] ?></p>
    <p><b>Endereço:</b> <?= $servico['endereco'] ?></p>
</div>

<table>

<tr>
    <th>Peça</th>
    <th>Qtd</th>
    <th>Preço</th>
    <th>Subtotal</th>
</tr>

<?php foreach ($itens as $item): ?>
<tr>
    <td><?= $item['peca_materia'] ?></td>
    <td><?= $item['quantidade'] ?></td>
    <td><?= number_format($item['preco'],2,',','.') ?></td>
    <td><?= number_format($item['subtotal'],2,',','.') ?></td>
</tr>
<?php endforeach; ?>

</table>

<div class="total">
TOTAL: <?= number_format($total_calculado,2,',','.') ?> KZ
</div>

<button class="btn" onclick="window.print()">Imprimir Factura</button>

</div>

</body>
</html>