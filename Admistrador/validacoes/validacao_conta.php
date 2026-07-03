

<?php
 session_start();
  require_once "./configuracao/conexao.php";
    if($_SESSION['nivel'] != "admin"){
    header("Location: login.php");
    exit();
  }


  

  $erros = [
    'nome' => '',
    'email' => 'email',             
    'nivel' => 'nivel',
    'senha_admin' => 'senha_admin',
    'senha_usuario' => 'senha_usuario'
];

 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $nivel = trim($_POST['nivel'] ?? '');
    $senha_admin = $_POST['senha_admin'] ?? '';
    $senha_usuario = $_POST['senha_usuario'] ?? '';


    // Nome
    if (!preg_match("/^[a-zA-ZÀ-ÿ ]+$/u", $nome)) {
        $erros['nome'] = "⚠️ O nome só pode conter letras e espaços.";
    }

    // Endereço
    if (empty($endereco)) {
        $erros['email'] = "⚠️ O endereço é obrigatório.";
    }

    // Telefone
    if (!preg_match("/^\d{9}$/", $telefone)) {
        $erros['telefone'] = "⚠️ Telefone deve ter 9 dígitos e apenas números.";
    }

    // Cargo
    if (empty($cargo)) {
        $erros['cargo'] = "⚠️ Seleciona o cargo.";
    }

    // Setor
    if (empty($setor)) {
        $erros['setor'] = "⚠️ Seleciona o setor.";
    }

    // Senha do admin
    if (empty($senha_adm)) {
        $erros['senha_adm'] = "⚠️ A senha do administrador é obrigatória.";
    } elseif (!password_verify($senha_adm, $hashSenha)) {
        $erros['senha_adm'] = "⚠️ Senha do administrador incorreta!";
    }
}

session_start();
require_once "./configuracao/conexao.php";
    if($_SESSION['nivel'] != "admin"){
    header("Location: login.php");
    exit();
  }



## Verifica se o admin está logado
if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== "admin"){
    $erro = "Você precisa estar logado como admin para cadastrar usuários!";
}

## Processa cadastro
if(isset($_POST['cadastrar']) && $erro == ""){

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $nivel = $_POST['nivel'];
    $senhaAdmin = $_POST['senha_admin']; 
    $senhaUsuario = $_POST['senha_usuario']; 

    ## Valida nome
    if(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nome)){
         $erros['nome'] = "⚠️ O nome deve conter apenas letras e espaços";
    }
    ## Valida email
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erro = "⚠️ Email inválido";
    }
    ## Valida nível
    elseif(!in_array($nivel, ["admin","usuario"])){
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
        $sqlInsert = "INSERT INTO usuarios (nome, email, nivel, senha) 
                      VALUES ('$nome', '$email', '$nivel', '$senhaHash')";
        mysqli_query($conn, $sqlInsert);

        header("Location: contas.php");
        exit;
    }
}
?>