
<?php
function atualizarRegistro($conn, $tabela, $dados, $id, $campo_id){

    $campos = [];
    $valores = [];
    $tipos = "";

    foreach($dados as $coluna => $valor){
        $campos[] = "$coluna = ?";
        $valores[] = $valor;
        $tipos .= is_numeric($valor) ? "i" : "s";
    }

    // adiciona o ID
    $tipos .= "i";
    $valores[] = $id;

    // usa campo dinâmico
    $sql = "UPDATE $tabela SET " . implode(", ", $campos) . " WHERE $campo_id = ?";
    $stmt = $conn->prepare($sql);

    if(!$stmt){
        return "Erro SQL: " . $conn->error;
    }

    $stmt->bind_param($tipos, ...$valores);

    return $stmt->execute() ? true : "Erro ao atualizar!";
}
?>