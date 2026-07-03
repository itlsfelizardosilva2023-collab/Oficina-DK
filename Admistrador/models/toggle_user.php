

<?php
header('Content-Type: application/json');

require_once "../configuracao/conexao.php";

// Receber JSON
$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$status = $data['status'] ?? null;

if ($id === null || $status === null) {
    echo json_encode(["success" => false]);
    exit;
}

// Atualizar
$sql = "UPDATE usuarios SET ativo = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $status, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}