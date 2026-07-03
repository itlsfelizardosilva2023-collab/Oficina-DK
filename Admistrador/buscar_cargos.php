<?php


ob_start();
session_start();


header('Content-Type: application/json');
require_once "./configuracao/conexao.php";
if (!isset($_SESSION['usuario_id'])) {
    ob_end_clean();
    echo json_encode(["erro" => "Não autorizado"]);
    exit;
}

$id_setor = $_GET['id_setor'] ?? null;

if (!$id_setor || !ctype_digit((string)$id_setor)) {
    ob_end_clean();
    echo json_encode(["erro" => "Setor inválido"]);
    exit;
}

$stmt = $conn->prepare("SELECT id_cargo, nome_cargo FROM cargos WHERE id_setor = ? ORDER BY nome_cargo");
$stmt->bind_param("i", $id_setor);
$stmt->execute();
$result = $stmt->get_result();

$cargos = [];
while ($row = $result->fetch_assoc()) {
    $cargos[] = $row;
}

ob_end_clean();
echo json_encode($cargos);
exit;