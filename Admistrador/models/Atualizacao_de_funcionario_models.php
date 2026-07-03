
<?php
session_start();
require_once "./configuracao/conexao.php";
require_once "./funcoes/funcao_atualizar.php";

$erro = "";

##PROCESSAR FORM
if (isset($_POST['atualizar'])) {

    $nome     = trim($_POST['nome'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    $id_setor_post = $_POST['id_setor'] ?? '';
    $novo_setor    = trim($_POST['novo_setor'] ?? '');
    $id_cargo_post = $_POST['id_cargo'] ?? '';
    $novo_cargo    = trim($_POST['novo_cargo'] ?? '');

    $id_setor = null;
    $id_cargo = null;

    $id = $_POST['id'] ?? 0;
    $senha = $_POST['senha'] ?? '';

    ## VALIDAÇÕES
    if (empty($nome) || empty($telefone)) {
        $erro = "Preencha os campos obrigatórios!";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $nome)) {
        $erro = "Nome inválido";
    } elseif (!preg_match("/^[0-9]{9}$/", $telefone)) {
        $erro = "Telefone inválido (9 dígitos)";
    } elseif ($id <= 0) {
        $erro = "ID inválido";
    } else {
        $erro = "";
    }

    ## VERIFICAR ADMIN
    if ($erro == "") {
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['nivel']) || $_SESSION['nivel'] !== "admin") {
            $erro = "Acesso negado!";
        } else {

            $admin_id = $_SESSION['usuario_id'];

            $sqlAdmin = "SELECT senha FROM usuarios WHERE id = ?";
            $stmtAdmin = $conn->prepare($sqlAdmin);

            if ($stmtAdmin) {
                $stmtAdmin->bind_param("i", $admin_id);
                $stmtAdmin->execute();
                $admin = $stmtAdmin->get_result()->fetch_assoc();

                if (!$admin) {
                    $erro = "Admin não encontrado!";
                } elseif (!password_verify($senha, $admin['senha'])) {
                    $erro = "Senha do admin incorreta!";
                }
            } else {
                $erro = "Erro SQL: " . $conn->error;
            }
        }
    }

    ## RESOLVER SETOR
    if ($erro == "") {
        if ($id_setor_post === 'novo') {

            if ($novo_setor === '' || mb_strlen($novo_setor) > 100) {
                $erro = "Digite um nome de setor válido.";
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
                $erro = "Setor inválido.";
            }

        } else {
            $erro = "Selecione um setor.";
        }
    }

    ## RESOLVER CARGO
    if ($erro == "") {
        if ($id_cargo_post === 'novo') {

            if ($novo_cargo === '' || mb_strlen($novo_cargo) > 100) {
                $erro = "Digite um nome de cargo válido.";
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

            $stmtC = $conn->prepare("SELECT id_cargo FROM cargos WHERE id_cargo = ? AND id_setor = ?");
            $stmtC->bind_param("ii", $id_cargo_post, $id_setor);
            $stmtC->execute();
            $existenteCargo = $stmtC->get_result()->fetch_assoc();

            if ($existenteCargo) {
                $id_cargo = (int) $id_cargo_post;
            } else {
                $erro = "Cargo inválido para o setor selecionado.";
            }

        } else {
            $erro = "Selecione um cargo.";
        }
    }

    ## ATUALIZAR
    if ($erro == "") {
        $dados = [
            "nome"     => $nome,
            "endereco" => $endereco,
            "telefone" => $telefone,
            "id_cargo" => $id_cargo
        ];

        $resultado = atualizarRegistro($conn, "funcionarios", $dados, $id, "id_funcionario");

        if ($resultado === true) {
            header("Location: funcionarios.php?sucesso=1");
            exit;
        } else {
            $erro = $resultado;
        }
    }
}

## BUSCAR FUNCIONÁRIO
if (!isset($_GET['id'])) {
    die("ID não recebido!");
}

$id = $_GET['id'];

$sql = "
    SELECT f.*, c.id_setor, c.nome_cargo, s.nome_setor
    FROM funcionarios f
    LEFT JOIN cargos c ON c.id_cargo = f.id_cargo
    LEFT JOIN setores s ON s.id_setor = c.id_setor
    WHERE f.id_funcionario = ?
";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro SQL: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$funcionarios = $result->fetch_assoc();

if (!$funcionarios) {
    die("Funcionário não encontrado!");
}
?>
