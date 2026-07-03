<?php

include_once("./configuracao/conexao.php");

$placa = strtoupper(trim($_GET['placa'] ?? ''));

$sql = mysqli_query($conn, "
SELECT
    clientes.id_cliente,
    clientes.nome AS nome_cliente,
    clientes.endereco,
    modelos.nome AS modelo_carro
FROM carros
INNER JOIN clientes
    ON carros.id_cliente = clientes.id_cliente
INNER JOIN modelos
    ON carros.id_modelo = modelos.id_modelo
WHERE carros.matricula = '$placa'
LIMIT 1
");

if (mysqli_num_rows($sql) > 0) {

    $dados = mysqli_fetch_assoc($sql);

    $id_cliente = $dados['id_cliente'];

    $sqlContrato = mysqli_query($conn, "
        SELECT id_contrato
        FROM contratos
        WHERE id_cliente = '$id_cliente'
          AND CURDATE() BETWEEN data_inicio AND data_fim
        LIMIT 1
    ");

    $temContrato = mysqli_num_rows($sqlContrato) > 0;

    echo json_encode([
        "id_cliente"    => $id_cliente,
        "nome_cliente"  => $dados['nome_cliente'],
        "modelo_carro"  => $dados['modelo_carro'],
        "endereco"      => $dados['endereco'],
        "tem_contrato"  => $temContrato
    ]);

} else {

    echo json_encode([]);

}