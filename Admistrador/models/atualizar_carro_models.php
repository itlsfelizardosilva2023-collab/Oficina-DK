

<?php
session_start();
require_once "./configuracao/conexao.php";
require_once "./funcoes/funcao_atualizar.php";

$erro = "";

##PROCESSAR FORM
if(isset($_POST['atualizar'])){

    $dados = [
        
        "id_marca" => $_POST['id_marca'];
        "nova_marca"=> $_POST['nova_marca'];
        "id_modelo" => $_POST['id_modelo'];
        "novo_modelo"=> $_POST['novo_modelo'];
        "matricula" => $_POST['matricula'];
        "cor" => $_POST['cor'];
        "id_cliente" =>$_POST['id_cliente']

    ];

    $id = $_POST['id'] ?? 0;
    $senha = $_POST['senha'] ?? '';

   


   

    
    ##ATUALIZAR
    if($erro == ""){
        $resultado = atualizarRegistro( $conn, "carros", $dados, $id, "id_ccarro");

        if($resultado === true){
            header("Location:carro.php?sucesso=1");
            exit;
        } else {
            $erro = $resultado;
        }
    }
}

## BUSCAR FUNCIONÁRIO
if(!isset($_GET['id'])){
    die("ID não recebido!");
}

$id = $_GET['id'];

$sql = "SELECT * FROM carros WHERE id_carro = ?";
$stmt = $conn->prepare($sql);

if(!$stmt){
    die("Erro SQL: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$carros = $result->fetch_assoc();

if(!$clientes){
    die("Carros não encontrado!");
}
?>
