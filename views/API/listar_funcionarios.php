<?php
include("../configuracao/conexao.php");

header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id_funcionario, nome, cargo, telefone 
        FROM funcionarios 
        ORDER BY id_funcionario DESC 
        LIMIT 10";

$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>