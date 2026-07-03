<?php

require_once "configuracao/conexao.php";
require_once "./funcoes/elimina.php";

/* =======================
   PROTEÇÃO ADMIN
======================= */


/* =======================
   ELIMINAR CARRO
======================= */
if (isset($_GET['excluir_id'])) {
    $id = intval($_GET['excluir_id']);

    if (eliminarRegistro($conn, 'carros', 'id_carro', $id)) {
        $_SESSION['msg'] = "Registro eliminado com sucesso!";
    } else {
        $_SESSION['msg'] = "Erro ao eliminar registro!";
    }

    header("Location: carros.php");
    exit;
}

/* =======================
   PESQUISA
======================= */
$pesquisa = trim($_GET['pesquisar'] ?? '');
$termo = "%$pesquisa%";

/* =======================
   QUERY PRINCIPAL
======================= */
$sql = "
SELECT 
    carros.id_carro,
    carros.matricula,
    carros.cor,
    carros.criado_em,
    marcas.nome AS marca,
    modelos.nome AS modelo,
    clientes.nome AS cliente,
    s.status AS status_servico

FROM carros
INNER JOIN modelos ON carros.id_modelo = modelos.id_modelo
INNER JOIN marcas ON modelos.id_marca = marcas.id_marca
INNER JOIN clientes ON carros.id_cliente = clientes.id_cliente

LEFT JOIN servicos s 
    ON s.id = (
        SELECT s2.id
        FROM servicos s2
        WHERE s2.placa_carro = carros.matricula
        ORDER BY s2.id DESC
        LIMIT 1
    )

WHERE 
(
    marcas.nome LIKE ?
    OR modelos.nome LIKE ?
    OR carros.matricula LIKE ?
    OR carros.cor LIKE ?
    OR clientes.nome LIKE ?
    OR carros.criado_em LIKE ?
    OR s.status LIKE ?
)

ORDER BY carros.id_carro DESC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro na query: " . $conn->error);
}

$stmt->bind_param(
    "sssssss",
    $termo,
    $termo,
    $termo,
    $termo,
    $termo,
    $termo,
    $termo
);

$stmt->execute();
$result = $stmt->get_result();
?>