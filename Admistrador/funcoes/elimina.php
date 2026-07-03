
<?php
function eliminarRegistro($conn, $tabela, $coluna_id, $id) {
    ## Garante que o ID seja inteiro
    $id = intval($id);

    ##Prepara a query dinâmica
    $sql = "DELETE FROM $tabela WHERE $coluna_id = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt){
        return false; 
    }

    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        $stmt->close();
        return true; 
    } else {
        $stmt->close();
        return false; 
    }
}
?>
