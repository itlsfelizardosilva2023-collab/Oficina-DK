
<?php
session_start();
require_once "./configuracao/conexao.php";
    if($_SESSION['nivel'] != "admin"){
    header("Location: login.php");
    exit();
  }

$erro = "";

$func_array = [];
$sqlFunc = "SELECT id_funcionario, nome FROM funcionarios";
$resultFunc = mysqli_query($conn, $sqlFunc);
while($row = mysqli_fetch_assoc($resultFunc)){
    $func_array[$row['id_funcionario']] = $row;
}

## Verifica se o admin está logado
if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== "admin"){
    $erro = "Você precisa estar logado como admin para cadastrar usuários!";
}

## Processa cadastro
if(isset($_POST['cadastrar']) && $erro == ""){
    $idFuncionario = $_POST['id_funcionario'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $nivel = $_POST['nivel'];
    $senhaAdmin = $_POST['senha_admin']; 
    $senhaUsuario = $_POST['senha_usuario']; 

    ## Valida nome
    if(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nome)){
         $erro = "⚠️ O nome deve conter apenas letras e espaços";
    }
    ## Valida email
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erro = "⚠️ Email inválido";
    }
    ## Valida nível
    elseif(!in_array($nivel, ["admin","usuario","tecnico"])){
        $erro = "⚠️ Nível inválido";
    }

    ## Verifica senha do admin logado
    if($erro == ""){
        $admin_id = $_SESSION['usuario_id'];
        $sqlAdmin = "SELECT * FROM usuarios WHERE id='$admin_id'";
        $resultAdmin = mysqli_query($conn, $sqlAdmin);
        $admin = mysqli_fetch_assoc($resultAdmin);

        if(!password_verify($senhaAdmin, $admin['senha'])){
            $erro = "⚠️ Senha do administrador incorreta!";
        }
    }

    ## Verifica se o email já existe
    if($erro == ""){
        $sqlCheck = "SELECT * FROM usuarios WHERE email='$email'";
        $resultCheck = mysqli_query($conn, $sqlCheck);
        if(mysqli_num_rows($resultCheck) > 0){
            $erro = "⚠️ Esse email já está cadastrado!";
        }
    }

  
    if($erro == ""){
        $senhaHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);
        $sqlInsert = "INSERT INTO usuarios (nome, email, nivel, senha, id_funcionario) 
              VALUES ('$nome', '$email', '$nivel', '$senhaHash', '$idFuncionario')";
        mysqli_query($conn, $sqlInsert);

        header("Location: contas.php");
        exit;
    }
}
?>