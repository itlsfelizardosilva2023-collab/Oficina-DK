
<?php
include("./configuracao/conexao.php");

$erro = "";

function inserirRegistro($conn, $tabela, $dados) {

    $campos = implode(", ", array_keys($dados));
    $placeholders = implode(", ", array_fill(0, count($dados), "?"));

    $sql = "INSERT INTO $tabela ($campos) VALUES ($placeholders)";

    if($stmt = $conn->prepare($sql)){

        $tipos = str_repeat("s", count($dados));
        $valores = array_values($dados);

        $stmt->bind_param($tipos, ...$valores);

        $stmt->execute();
        $stmt->close();

        return true;

    }

    return false;
}

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $dados = [
        "tipo" => $_POST['tipo'] ?? '',
        "nome" => $_POST['nome'] ?? '',
        "quantidade" => $_POST['quantidade'] ?? '',
        "marca" => $_POST['marca'] ?? '',
        "preco" => $_POST['preco'] ?? '',
        "data_expiracao" => $_POST['data_expiracao'] ?? '',
        "codigo" => $_POST['codigo'] ?? ''
    ];

    inserirRegistro($conn, "estoque", $dados);

    header("Location: stok.php");
    exit();
}

$sql = "SELECT SUM(quantidade * preco) AS total_stock FROM estoque";
$result = $conn->query($sql);

$total = 0;

if($result && $row = $result->fetch_assoc()){
    $total = $row['total_stock'] ?? 0;
}

$pesquisar = $_POST['pesquisar'] ?? '';

if(!empty($pesquisar)){
    $sql = "SELECT * FROM estoque 
            WHERE nome LIKE '%$pesquisar%' 
            OR marca LIKE '%$pesquisar%' 
            OR tipo LIKE '%$pesquisar%'";
}else{
    $sql = "SELECT * FROM estoque";
}

$result = $conn->query($sql);

?>