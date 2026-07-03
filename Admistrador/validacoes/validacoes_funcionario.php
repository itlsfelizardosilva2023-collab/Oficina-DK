
<?php
 
    if($_SESSION['nivel'] != "admin"){
    header("Location: login.php");
    exit();
  }
  $erros = [
    'nome' => '',
    'endereco' => '',
    'telefone' => '',
    'cargo' => '',
    'setor' => '',
    'senha_adm' => ''
];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $cargo = $_POST['cargo'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $senha_adm = $_POST['senha_adm'] ?? '';

    // Nome
    if (!preg_match("/^[a-zA-ZÀ-ÿ ]+$/u", $nome)) {
        $erros['nome'] = "⚠️ O nome só pode conter letras e espaços.";
    }

    // Endereço
    if (empty($endereco)) {
        $erros['endereco'] = "⚠️ O endereço é obrigatório.";
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
        $erros['senha_adm'] = "⚠️  Senha do administrador incorreta!";
    } elseif (!password_verify($senha_adm, $hashSenha)) {
        $erros['senha_adm'] = "⚠️ Senha do administrador incorreta!";
    }
}
?>