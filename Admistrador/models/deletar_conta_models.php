
<?php
session_start();
require_once "../configuracao/conexao.php";

## Verifica se o admin está logado
if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== "admin"){
    echo "Você precisa estar logado como admin para deletar usuários!";
    exit;
}

## Pega o ID do usuário a ser deletado
if(!isset($_GET['id'])){
    echo "ID do usuário não fornecido!";
    exit;
}

$id = intval($_GET['id']); // segurança contra SQL injection

## Não permitir que o admin se delete
if($id == $_SESSION['usuario_id']){
    echo "<script>
            alert('Você não pode deletar sua própria conta!');
            window.location.href = '../contas.php';
          </script>";
    exit;
}

## Executa delete
$sql = "DELETE FROM usuarios WHERE id='$id'";
if(mysqli_query($conn, $sql)){
    header("Location: ../contas.php");
    exit;
} else {
    echo "Erro ao deletar usuário: " . mysqli_error($conn);
}
?>