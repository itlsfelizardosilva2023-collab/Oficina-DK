<?php
ob_start();
session_start();
 include_once('./configuracao/config.php');
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID de usuário não fornecido.']);
    exit;
}

$id_usuario = (int) $dados['id_usuario'];
$paginas = $dados['paginas'] ?? [];

$conexao->begin_transaction();

try {
    $stmtDelete = $conexao->prepare("DELETE FROM usuario_permissoes WHERE id_usuario = ?");
    $stmtDelete->bind_param("i", $id_usuario);
    $stmtDelete->execute();
    $stmtDelete->close();

    if (!empty($paginas)) {
        $stmtInsert = $conexao->prepare("INSERT INTO usuario_permissoes (id_usuario, pagina) VALUES (?, ?)");
        foreach ($paginas as $pagina) {
            $stmtInsert->bind_param("is", $id_usuario, $pagina);
            $stmtInsert->execute();
        }
        $stmtInsert->close();
    }

    $conexao->commit();
    ob_end_clean();
    echo json_encode(['sucesso' => true]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_end_clean();
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}