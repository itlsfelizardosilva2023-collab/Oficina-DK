
<?php
 include_once('./configuracao/config.php');
function temAcessoPagina($pagina) {
    global $conexao;

    if (!isset($_SESSION['nivel'])) {
        return false;
    }

    if ($_SESSION['nivel'] === 'admin') {
        return true;
    }

    if ($_SESSION['nivel'] === 'usuario') {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }

        $stmt = $conexao->prepare(
            "SELECT id_permissao FROM usuario_permissoes WHERE id_usuario = ? AND pagina = ?"
        );
        $stmt->bind_param("is", $_SESSION['usuario_id'], $pagina);
        $stmt->execute();
        $stmt->store_result();

        $temAcesso = $stmt->num_rows > 0;
        $stmt->close();

        return $temAcesso;
    }

    return false;
}