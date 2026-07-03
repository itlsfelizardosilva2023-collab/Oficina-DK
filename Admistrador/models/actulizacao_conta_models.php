
<?php
session_start();
require_once "./configuracao/config.php";

$erro = "";



// Verifica se é admin — corta já, não deixa a página continuar
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== "admin") {
    header("Location: login.php");
    exit;
}

// Pega usuário (prepared statement)
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if (!$usuario) {
        $erro = "Usuário não encontrado!";
    }
} else {
    $erro = "Usuário inválido!";
}

// Atualizar
if (isset($_POST['atualizar']) && $erro == "") {

    $id = (int) $_POST['id'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $nivel = $_POST['nivel'];

    $senhaAdmin = $_POST['senha'] ?? '';
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha  = $_POST['nova_senha'] ?? '';

    // Validações
    if (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nome)) {
        $erro = "⚠️ Nome inválido";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "⚠️ Email inválido";
    } elseif (!in_array($nivel, ["admin", "usuario", "tecnico"])) {
        $erro = "⚠️ Nível inválido";
    }

    // Verifica senha do admin logado
    if ($erro == "") {
        if (empty($senhaAdmin)) {
            $erro = "⚠️ Confirma a tua senha de admin";
        } else {
            $admin_id = $_SESSION['usuario_id'];
            $stmtAdmin = $conexao->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmtAdmin->bind_param("i", $admin_id);
            $stmtAdmin->execute();
            $admin = $stmtAdmin->get_result()->fetch_assoc();

            if (!$admin || !password_verify($senhaAdmin, $admin['senha'])) {
                $erro = "⚠️ Senha do admin incorreta!";
            }
        }
    }

    // Verifica senha atual do usuário (só se for trocar a senha dele)
    if ($erro == "" && !empty($novaSenha)) {
        if (empty($senhaAtual) || !password_verify($senhaAtual, $usuario['senha'])) {
            $erro = "⚠️ Senha atual incorreta!";
        }
    }

    // Atualiza
    if ($erro == "") {
        if (!empty($novaSenha)) {
            $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmtUpdate = $conexao->prepare(
                "UPDATE usuarios SET nome=?, email=?, nivel=?, senha=? WHERE id=?"
            );
            $stmtUpdate->bind_param("ssssi", $nome, $email, $nivel, $novaSenhaHash, $id);
        } else {
            $stmtUpdate = $conexao->prepare(
                "UPDATE usuarios SET nome=?, email=?, nivel=? WHERE id=?"
            );
            $stmtUpdate->bind_param("sssi", $nome, $email, $nivel, $id);
        }

        if ($stmtUpdate->execute()) {
            header("Location: contas.php");
            exit;
        } else {
            $erro = "⚠️ Erro ao atualizar, tenta novamente.";
        }
    }
}
?>