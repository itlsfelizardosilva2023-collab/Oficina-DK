<?php
include("./configuracao/conexao.php");
session_start();
if (isset($_POST['atualizar'])) {

    $id_contrato = $_POST['id_contrato'];
    $numero_bi = $_POST['numero_bi'];
    $numero_transacao = $_POST['numero_transacao'];
    $total = $_POST['total'];
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_fim = $_POST['data_fim'] ?? null;

    $sql = "UPDATE contratos SET
                numero_bi = '$numero_bi',
                numero_transacao = '$numero_transacao',
                total_geral = '$total',
                data_inicio = '$data_inicio',
                data_fim = '$data_fim'
            WHERE id_contrato = '$id_contrato'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: contratos.php?sucesso=1");
        exit;
    } else {
        echo "Erro: " . mysqli_error($conn);
    }
}
?>

