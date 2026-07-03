
<?php
include("./configuracao/conexao.php");



$bi = $_GET['bi'] ?? '';

if ($bi == '') {
    echo json_encode(["sucesso" => false, "erro" => "BI vazio"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, nome FROM clientes WHERE numero_bi = ?");

if (!$stmt) {
    echo json_encode(["sucesso" => false, "erro" => $conn->error]);
    exit;
}

$stmt->bind_param("s", $bi);
$stmt->execute();

$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "sucesso" => true,
        "id" => $row['id'],
        "nome" => $row['nome']
    ]);
} else {
    echo json_encode(["sucesso" => false]);
}
?>