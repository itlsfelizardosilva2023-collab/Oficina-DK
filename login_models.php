<?php
session_start();

include_once('Admistrador\configuracao\conexao.php');
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim(strtolower($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    // Buscar usuário — inclui id_funcionario e ativo
    $stmt = $conn->prepare("SELECT id, id_funcionario, nome, senha, nivel, ativo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($senha, $user['senha'])) {

            if ($user['ativo'] == 0) {
                $erro = "⚠️ Conta desativada!";
            } else {

                // Sessões
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['nivel'] = $user['nivel'];
                $_SESSION['id_funcionario'] = $user['id_funcionario'];

                // Redirecionamento por nível
                if ($user['nivel'] === "admin") {
                    header("Location: Admistrador/inicio_admin.php");
                    exit;
                } elseif ($user['nivel'] === "tecnico") {
                    header("Location: Admistrador/servicos_adquiridos.php");
                    exit;
                } else {
                    header("Location: Admistrador/inicio_admin.php");
                    exit;
                }
            }

        } else {
            $erro = "⚠️ Email ou senha incorretos!";
        }

    } else {
        $erro = "⚠️ Email ou senha incorretos!";
    }

    $stmt->close();
    $conn->close();
}
?>