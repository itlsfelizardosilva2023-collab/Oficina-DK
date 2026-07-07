<?php

require_once "./configuracao/conexao.php";
require_once "./funcoes/funcao_atualizar.php";

$erro = "";

// ============================================================
// PROCESSAR FORMULÁRIO
// ============================================================
if(isset($_POST['atualizar'])){

    $dados = [
        "nome"      => trim($_POST['nome']      ?? ''),
        "endereco"  => trim($_POST['endereco']  ?? ''),
        "telefone"  => trim($_POST['telefone']  ?? ''),
        "numero_bi" => trim($_POST['numero_bi'] ?? ''),
        "email"     => trim($_POST['email']     ?? '')
    ];

    $id    = $_POST['id'] ?? 0;
    $senha = $_POST['senha'] ?? '';

    // ============================================================
    // VALIDAÇÕES
    // ============================================================
    if(empty($dados['nome']) || empty($dados['telefone'])){
        $erro = "⚠️ Preencha os campos obrigatórios!";

    } elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $dados['nome'])){
        $erro = "⚠️ Nome inválido. Só pode conter letras e espaços.";

    } elseif(!preg_match("/^[0-9]{9}$/", $dados['telefone'])){
        $erro = "⚠️ Telefone inválido. Deve ter 9 dígitos numéricos.";

    } elseif(!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)){
        $erro = "⚠️ O email introduzido não é válido.";

    } elseif($id <= 0){
        $erro = "⚠️ ID inválido.";
    }

    // ============================================================
    // VERIFICAR ADMIN
    // ============================================================
    if($erro == ""){
        if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== "admin"){
            $erro = "⚠️ Acesso negado!";
        } else {

            $admin_id = $_SESSION['usuario_id'];

            $sqlAdmin  = "SELECT senha FROM usuarios WHERE id = ?";
            $stmtAdmin = $conn->prepare($sqlAdmin);

            if($stmtAdmin){
                $stmtAdmin->bind_param("i", $admin_id);
                $stmtAdmin->execute();
                $admin = $stmtAdmin->get_result()->fetch_assoc();
                $stmtAdmin->close();
            } else {
                $erro = "⚠️ Erro SQL: " . $conn->error;
            }
        }
    }

    // ============================================================
    // ATUALIZAR
    // ============================================================
    if($erro == ""){
        $resultado = atualizarRegistro($conn, "clientes", $dados, $id, "id_cliente");

        if($resultado === true){
            header("Location: clientes.php?sucesso=1");
            exit;
        } else {
            $erro = $resultado;
        }
    }
}

// ============================================================
// BUSCAR CLIENTE
// ============================================================
if(!isset($_GET['id'])){
    die("ID não recebido!");
}

$id  = $_GET['id'];
$sql = "SELECT * FROM clientes WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);

if(!$stmt){
    die("Erro SQL: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result   = $stmt->get_result();
$clientes = $result->fetch_assoc();
$stmt->close();

if(!$clientes){
    die("Cliente não encontrado!");
}
?>