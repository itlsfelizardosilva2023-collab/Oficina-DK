<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include_once('./configuracao/config.php');
include_once('./configuracao/conexao.php');
require_once "./funcoes/elimina.php";


// ================= ELIMINAR =================
if(isset($_GET['id'])){

    $id = $_GET['id'];

    if(eliminarRegistro($conn, 'servicos', 'id', $id)){

        $_SESSION['msg'] = "Registro eliminado com sucesso!";

    }else{

        $_SESSION['msg'] = "Erro ao eliminar registro!";

    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit;

}


// ================= VERIFICAR LOGIN =================
if(!isset($_SESSION['nivel']) || $_SESSION['nivel'] != "admin"){

    header("Location: login.php");
    exit();

}


// ================= PESQUISA =================
$pesquisa = isset($_GET['pesquisar'])
? trim($_GET['pesquisar'])
: '';


// ================= SQL =================
if($pesquisa == ''){

    $sql = "SELECT * FROM servicos";

    $stmt = $conn->prepare($sql);

    if(!$stmt){

        die("Erro SQL: " . $conn->error);

    }

}else{

    $sql = "SELECT * FROM servicos

            WHERE nome_cliente LIKE ?
            OR placa_carro LIKE ?
            OR preco LIKE ?
            OR endereco LIKE ?";

    $stmt = $conn->prepare($sql);

    if(!$stmt){

        die("Erro SQL: " . $conn->error);

    }

    $pesquisa = "%".$pesquisa."%";

    $stmt->bind_param(

        "ssss",

        $pesquisa,
        $pesquisa,
        $pesquisa,
        $pesquisa

    );

}


// ================= EXECUTAR =================
$stmt->execute();

$result = $stmt->get_result();

?>

