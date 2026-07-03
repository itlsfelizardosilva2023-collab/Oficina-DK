<?php

function inserirRegistro($conn, $tabela, $dados) {

    // Preparar query dinamicamente com os campos recebidos
    $campos       = implode(", ", array_keys($dados));
    $placeholders = implode(", ", array_fill(0, count($dados), "?"));
    $sql          = "INSERT INTO $tabela ($campos) VALUES ($placeholders)";

    if ($stmt = $conn->prepare($sql)) {

        $tipos  = str_repeat("s", count($dados)); // todos como string
        $valores = array_values($dados);

        $stmt->bind_param($tipos, ...$valores);

        if ($stmt->execute()) {
            $stmt->close();
            return true; //  Inserido com sucesso
        } else {
            $stmt->close();
            return false; //  Erro ao executar
        }

    } else {
        return false; //  Erro ao preparar a query
    }
}
?>