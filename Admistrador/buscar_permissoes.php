<?php
ob_start();
session_start();
 include_once('./configuracao/config.php');
header('Content-Type: application/json');

if (!isset($_GET['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID de usuário não fornecido.']);
    exit;
}

$id_usuario = (int) $_GET['id_usuario'];

$stmt = $conexao->prepare("SELECT pagina FROM usuario_permissoes WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$permissoes = [];
while ($row = $resultado->fetch_assoc()) {
    $permissoes[] = $row['pagina'];
}
$stmt->close();

ob_end_clean();
echo json_encode(['sucesso' => true, 'permissoes' => $permissoes]);