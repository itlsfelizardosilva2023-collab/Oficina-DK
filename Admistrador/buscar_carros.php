<?php
// buscar_carro.php
// Recebe uma placa via GET e devolve os dados do cliente + carro em JSON

include_once("./configuracao/conexao.php");

header('Content-Type: application/json');

$placa = trim($_GET['placa'] ?? '');

if (empty($placa)) {
    echo json_encode([
        'id_cliente'   => null,
        'nome_cliente' => '',
        'modelo_carro' => '',
        'endereco'     => ''
    ]);
    exit;
}

// Procura o carro e o respectivo cliente pela placa
// Ajusta os nomes das tabelas/colunas conforme a tua base de dados
$stmt = $conn->prepare("
    SELECT
        c.id_cliente,
        c.nome        AS nome_cliente,
        c.endereco    AS endereco,
        car.modelo    AS modelo_carro
    FROM carros car
    LEFT JOIN clientes c ON c.id_cliente = car.id_cliente
    WHERE car.placa = ?
    LIMIT 1
");

$stmt->bind_param("s", $placa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Placa não encontrada — devolve vazio mas sem erro
    echo json_encode([
        'id_cliente'   => null,
        'nome_cliente' => '',
        'modelo_carro' => '',
        'endereco'     => ''
    ]);
    exit;
}

$row = $result->fetch_assoc();

echo json_encode([
    'id_cliente'   => (int) $row['id_cliente'],   // IMPORTANTE: int, nunca string
    'nome_cliente' => $row['nome_cliente'] ?? '',
    'modelo_carro' => $row['modelo_carro'] ?? '',
    'endereco'     => $row['endereco']     ?? ''
]);