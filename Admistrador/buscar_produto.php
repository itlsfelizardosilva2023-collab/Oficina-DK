<?php

include_once("./configuracao/conexao.php");

$codigo = trim($_GET['codigo'] ?? '');

$sql = mysqli_query($conn, "

SELECT
    nome,
    preco

FROM estoque

WHERE codigo = '$codigo'

LIMIT 1

");

if(mysqli_num_rows($sql) > 0){

    $dados = mysqli_fetch_assoc($sql);

    echo json_encode([

        "nome" => $dados['nome'],
        "preco" => $dados['preco']

    ]);

}else{

    echo json_encode([]);
}
?>