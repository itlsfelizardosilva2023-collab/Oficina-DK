
<?php

require_once "./configuracao/conexao.php";

$msg = "";

## ENVIAR PEDIDO AO ADMIN
if(isset($_POST['enviar_codigo'])){

    $email = $_POST['email'];

    $sql = "SELECT id FROM usuarios WHERE email='$email'";
    $res = mysqli_query($conn, $sql);

    if(mysqli_num_rows($res) > 0){

        $user = mysqli_fetch_assoc($res);
        $user_id = $user['id'];

        mysqli_query($conn, "INSERT INTO recuperacao_conta (usuario_id) VALUES ('$user_id')");

        $msg = "✅ Pedido enviado! Contacte o administrador.";
    }else{
        $msg = "⚠️ Email não encontrado!";
    }
}

## CONFIRMAR CÓDIGO E ALTERAR SENHA
if(isset($_POST['confirmar'])){

    $email = $_POST['email'];
    $codigo = $_POST['codigo'];
    $novaSenha = $_POST['nova_senha'];

    $sql = "SELECT rc.*, u.id as user_id 
            FROM recuperacao_conta rc
            JOIN usuarios u ON rc.usuario_id = u.id
            WHERE u.email='$email'
            AND rc.codigo='$codigo'
            AND rc.status='aprovado'
            AND rc.expiracao >= NOW()";

    $res = mysqli_query($conn, $sql);

    if(mysqli_num_rows($res) > 0){

        $row = mysqli_fetch_assoc($res);
        $user_id = $row['user_id'];

        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        mysqli_query($conn, "UPDATE usuarios SET senha='$novaSenhaHash' WHERE id='$user_id'");

        mysqli_query($conn, "UPDATE recuperacao_conta SET status='usado' WHERE id='{$row['id']}'");

        $msg = "✅ Senha alterada com sucesso!";
    }else{
        $msg = " ⚠️ Código inválido ou expirado!";
    }
}
?>