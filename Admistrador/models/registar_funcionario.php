
<?php
session_start();
require_once "./configuracao/conexao.php";
require_once "./funcoes/funcao_registo.php";

$erro = "";
$sucesso = "";

// Verifica se admin está logado
if(!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== "admin"){
    die("Você precisa estar logado como admin para cadastrar usuários!");
}



// Mostrar mensagem de sucesso via GET (PRG)
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    $sucesso = "✅ Funcionário registrado com sucesso!";
}

// Processa formulário somente se houver POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {

    $senhaAtualAdm = $_POST['senha_adm'] ?? '';
    $idAdmin = $_SESSION['usuario_id'] ?? 0;

    if (empty($senhaAtualAdm) || !$idAdmin) {
        $erro = "Erro: dados do administrador não fornecidos.";
    } else {
        // Busca hash da senha do admin logado
        $sqlAdmin = "SELECT senha FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sqlAdmin);
        if (!$stmt) {
            die("Erro na preparação da query: " . $conn->error);
        }
        $stmt->bind_param("i", $idAdmin);
        $stmt->execute();
        $stmt->bind_result($hashSenha);
        $stmt->fetch();
        $stmt->close();

        // Verifica se a senha do admin está correta
        if (!password_verify($senhaAtualAdm, $hashSenha)) {
            $erro = "⚠️ Senha do administrador incorreta!";
        } else {
            // Captura dados do formulário
            $nome     = trim($_POST['nome'] ?? '');
            $endereco = trim($_POST['endereco'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email    = trim($_POST['email'] ?? '');

            $id_setor_post = $_POST['id_setor'] ?? '';
            $novo_setor    = trim($_POST['novo_setor'] ?? '');
            $id_cargo_post = $_POST['id_cargo'] ?? '';
            $novo_cargo    = trim($_POST['novo_cargo'] ?? '');

            $id_setor = null;
            $id_cargo = null;

            // Validações básicas primeiro
            if (!preg_match("/^[a-zA-ZÀ-ÿ ]+$/u", $nome)) {
                $erro = "⚠️ o nome só pode conter letras e espaços.";
            } elseif (!preg_match("/^[0-9]{9}$/", $telefone)) {
                $erro = "⚠️ telefone deve ter 9 dígitos, e só pode ser formado por números";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erro = "⚠️ Email inválido";
            } elseif (empty($endereco)) {
                $erro = "⚠️ Endereço é obrigatório.";
            }

            // ===== Resolve o SETOR =====
            if ($erro == "") {
                if ($id_setor_post === 'novo') {

                    if ($novo_setor === '' || mb_strlen($novo_setor) > 100) {
                        $erro = "⚠️ Digite um nome de setor válido.";
                    } else {
                        $stmtS = $conn->prepare("SELECT id_setor FROM setores WHERE nome_setor = ?");
                        $stmtS->bind_param("s", $novo_setor);
                        $stmtS->execute();
                        $existenteSetor = $stmtS->get_result()->fetch_assoc();

                        if ($existenteSetor) {
                            $id_setor = $existenteSetor['id_setor'];
                        } else {
                            $stmtInsS = $conn->prepare("INSERT INTO setores (nome_setor) VALUES (?)");
                            $stmtInsS->bind_param("s", $novo_setor);
                            $stmtInsS->execute();
                            $id_setor = $conn->insert_id;
                        }
                    }

                } elseif (ctype_digit((string)$id_setor_post)) {

                    $stmtS = $conn->prepare("SELECT id_setor FROM setores WHERE id_setor = ?");
                    $stmtS->bind_param("i", $id_setor_post);
                    $stmtS->execute();
                    $existenteSetor = $stmtS->get_result()->fetch_assoc();

                    if ($existenteSetor) {
                        $id_setor = (int) $id_setor_post;
                    } else {
                        $erro = "⚠️ Setor inválido.";
                    }

                } else {
                    $erro = "⚠️ Selecione um setor.";
                }
            }

            // ===== Resolve o CARGO (só roda se o setor já foi resolvido sem erro) =====
            if ($erro == "") {
                if ($id_cargo_post === 'novo') {

                    if ($novo_cargo === '' || mb_strlen($novo_cargo) > 100) {
                        $erro = "⚠️ Digite um nome de cargo válido.";
                    } else {
                        $stmtC = $conn->prepare("SELECT id_cargo FROM cargos WHERE nome_cargo = ? AND id_setor = ?");
                        $stmtC->bind_param("si", $novo_cargo, $id_setor);
                        $stmtC->execute();
                        $existenteCargo = $stmtC->get_result()->fetch_assoc();

                        if ($existenteCargo) {
                            $id_cargo = $existenteCargo['id_cargo'];
                        } else {
                            $stmtInsC = $conn->prepare("INSERT INTO cargos (nome_cargo, id_setor) VALUES (?, ?)");
                            $stmtInsC->bind_param("si", $novo_cargo, $id_setor);
                            $stmtInsC->execute();
                            $id_cargo = $conn->insert_id;
                        }
                    }

                } elseif (ctype_digit((string)$id_cargo_post)) {

                    // confirma que o cargo existe E pertence ao setor escolhido
                    $stmtC = $conn->prepare("SELECT id_cargo FROM cargos WHERE id_cargo = ? AND id_setor = ?");
                    $stmtC->bind_param("ii", $id_cargo_post, $id_setor);
                    $stmtC->execute();
                    $existenteCargo = $stmtC->get_result()->fetch_assoc();

                    if ($existenteCargo) {
                        $id_cargo = (int) $id_cargo_post;
                    } else {
                        $erro = "⚠️ Cargo inválido para o setor selecionado.";
                    }

                } else {
                    $erro = "⚠️ Selecione um cargo.";
                }
            }

            // Se não houver erro, insere registro e redireciona (PRG)
            if ($erro == "") {
                $dadosFuncionario = [
                    'nome'     => $nome,
                    'endereco' => $endereco,
                    'telefone' => $telefone,
                    'email'    => $email,
                    'id_cargo' => $id_cargo
                ];

                if (inserirRegistro($conn, 'funcionarios', $dadosFuncionario)) {
                    header("Location: funcionarios.php?sucesso=1");
                    exit;
                } else {
                    $erro = "⚠️ Erro ao registrar funcionário. Tenta novamente.";
                }
            }
        }
    }
}

?>