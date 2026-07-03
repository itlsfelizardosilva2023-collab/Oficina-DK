

<?php
require_once "./configuracao/conexao.php";

$id_marca = $_GET['id_marca'] ?? null;

$sql = "SELECT * FROM modelos WHERE id_marca = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_marca);
$stmt->execute();
$result = $stmt->get_result();

echo "<option value=''>Selecione</option>";

while($row = $result->fetch_assoc()){
    echo "<option value='{$row['id_modelo']}'>{$row['nome']}</option>";
}
?>