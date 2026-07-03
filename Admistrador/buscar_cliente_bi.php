<?php
include_once('./configuracao/config.php');
include_once('./configuracao/conexao.php');

header('Content-Type: application/json');

$bi = isset($_GET['bi']) ? trim($_GET['bi']) : '';

if(empty($bi)){
    echo json_encode(['encontrado' => false]);
    exit;
}

$bi = $conn->real_escape_string($bi);

$sql = "SELECT id_cliente, nome FROM clientes WHERE numero_bi = '$bi' LIMIT 1";
$res = $conn->query($sql);

if($res && $res->num_rows > 0){
    $cliente = $res->fetch_assoc();
    echo json_encode([
        'encontrado' => true,
        'id'   => $cliente['id_cliente'],
        'nome' => $cliente['nome']
    ]);
} else {
    echo json_encode(['encontrado' => false]);
}