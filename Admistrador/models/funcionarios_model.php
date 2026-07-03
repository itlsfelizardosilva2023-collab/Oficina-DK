

<?php
            session_start();
            include_once('./configuracao/config.php');
            include_once('./configuracao/conexao.php');
            require_once "./funcoes/elimina.php";

        ## Tratar exclusão se existir 
                    if(isset($_GET['excluir_id'])){
            $id = $_GET['excluir_id'];
            if(eliminarRegistro($conn, 'funcionarios', 'id_funcionario', $id)){
                $_SESSION['msg'] = "Registro eliminado com sucesso!";
            } else {
                $_SESSION['msg'] = "Erro ao eliminar registro!";
            }
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
                            
            
           

            # Verifica se está logado e é admin
            if(!isset($_SESSION['nivel']) || $_SESSION['nivel'] != "admin"){
                header("Location: login.php");
                exit();
            }

            

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");


$pesquisa = trim($_GET['pesquisar'] ?? '');

$sqlBase = "
    SELECT 
        f.id_funcionario,
        f.nome,
        f.email,
        f.endereco,
        f.telefone,
        f.criado_em,
        c.nome_cargo,
        s.nome_setor
    FROM funcionarios f
    LEFT JOIN cargos c ON c.id_cargo = f.id_cargo
    LEFT JOIN setores s ON s.id_setor = c.id_setor
";

if ($pesquisa === '') {

    // 🔵 SEM pesquisa: últimos 10
    $sql = $sqlBase . " ORDER BY f.id_funcionario DESC LIMIT 10";
    $stmt = $conn->prepare($sql);

} else {

    // 🔴 COM pesquisa: TODOS os resultados que combinarem
    $sql = $sqlBase . "
        WHERE f.nome LIKE ?
           OR f.email LIKE ?
           OR f.telefone LIKE ?
           OR f.endereco LIKE ?
           OR c.nome_cargo LIKE ?
           OR s.nome_setor LIKE ?
           OR f.criado_em LIKE ?
        ORDER BY f.id_funcionario DESC
    ";

    $stmt = $conn->prepare($sql);
    $like = "%{$pesquisa}%";

    $stmt->bind_param(
        "sssssss",
        $like, $like, $like, $like, $like, $like, $like
    );
}

$stmt->execute();
$result = $stmt->get_result();
?>


