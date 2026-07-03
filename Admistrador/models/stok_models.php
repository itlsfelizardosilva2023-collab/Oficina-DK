
<?php

  include_once(".\models\stok_add_models.php");
include("./configuracao/conexao.php");

$pesquisar = $_POST['pesquisar'] ?? '';

if(!empty($pesquisar)){

    $pesquisar = $conn->real_escape_string($pesquisar);

    $sql = "SELECT * FROM estoque 
            WHERE nome LIKE '%$pesquisar%' 
            OR marca LIKE '%$pesquisar%' 
            OR tipo LIKE '%$pesquisar%'";

}else{

    $sql = "SELECT * FROM estoque";
}

$result = $conn->query($sql);

##alerta#################
$sql_alerta = "
SELECT nome, quantidade
FROM estoque
WHERE quantidade <= 5
ORDER BY quantidade ASC
LIMIT 3
";

$result_alerta = mysqli_query($conn, $sql_alerta);

if(!$result_alerta){
    die("Erro SQL alerta: " . mysqli_error($conn));
}

// guarda todos os produtos com stock baixo
$alertas = [];

while($row = mysqli_fetch_assoc($result_alerta)){
    $alertas[] = $row;
}

// ═══════════════════════════════════════════
//   ELIMINAR PRODUTO DO STOCK
// ═══════════════════════════════════════════
if (isset($_GET['excluir_id'])) {

    $id_estoque = intval($_GET['excluir_id']);

    if ($id_estoque > 0) {

        $stmt = $conn->prepare("DELETE FROM estoque WHERE id_estoque = ?");
        $stmt->bind_param("i", $id_estoque);

        if ($stmt->execute()) {
            header("Location: stok.php?msg=eliminado");
            exit;
        } else {
            header("Location: stok.php?erro=" . urlencode($conn->error));
            exit;
        }
    }
}
?>
