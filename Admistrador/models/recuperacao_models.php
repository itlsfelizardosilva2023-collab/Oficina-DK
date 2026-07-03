
<?php

session_start();
require_once "./configuracao/conexao.php";

$erro = "";
$msg = "";

### só admin entra
if(!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'admin'){
    exit("Acesso negado!");
}

###  GERAR CÓDIGO
if(isset($_GET['gerar'])){

    $id = $_GET['gerar'];

    $codigo = rand(100000,999999);
    $expira = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    $sql = "UPDATE recuperacao_conta 
            SET codigo='$codigo', expiracao='$expira', status='aprovado'
            WHERE id='$id'";

    if(mysqli_query($conn, $sql)){
        $msg = "✅ Código gerado: <b>$codigo</b> (válido por 30 min)";
    }else{
        $erro = "⚠️ Erro ao gerar código!";
    }
}

##  LISTAR PEDIDOS
$sqlPedidos = "
SELECT rc.id, rc.status, u.email 
FROM recuperacao_conta rc
JOIN usuarios u ON rc.usuario_id = u.id
WHERE rc.status='pendente'
";

$result = mysqli_query($conn, $sqlPedidos);
?>
