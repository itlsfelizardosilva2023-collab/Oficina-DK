
<?php
function listarRegistros($conn, $tabela, $colunas, $where = "", $param = "", $imprimir = true){

    $sql = "SELECT " . implode(", ", $colunas) . " FROM $tabela $where";
    $stmt = $conn->prepare($sql);

    if(!$stmt){
        die("Erro: " . $conn->error);
    }

    if($param){
        $stmt->bind_param("s", $param);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if($imprimir){
        while($row = $result->fetch_assoc()){
            echo "<tr>";

            foreach($colunas as $col){
                echo "<td>" . htmlspecialchars($row[$col]) . "</td>";
            }

            echo "<td>
                    <a href='editar_funcionario.php?id={$row['id']}'>Editar</a> | 
                    <a href='deletar_funcionario.php?id={$row['id']}' onclick='return confirm(\"Deseja deletar?\")'>Deletar</a>
                  </td>";

            echo "</tr>";
        }
    } else {
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();
}
?>