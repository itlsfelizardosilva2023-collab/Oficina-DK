<?php

include_once("../configuracao/conexao.php");

$id = intval($_GET['id']);

if ($id <= 0) {
    die("ID inválido");
}

/* ==============================
   1. ELIMINAR ITENS DO SERVIÇO
============================== */
mysqli_query($conn, "
DELETE FROM servico_itens
WHERE id_servico = $id
");

/* ==============================
   2. ELIMINAR SERVIÇO
============================== */
$result = mysqli_query($conn, "
DELETE FROM servicos
WHERE id = $id
");

if (!$result) {
    die("Erro ao eliminar serviço: " . mysqli_error($conn));
}

/* ==============================
   3. REDIRECIONAR
============================== */
header("Location: ../servicos.php");
exit;

?>