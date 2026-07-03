

<?php
session_start();
require_once "./configuracao/conexao.php";



// Verifica ID
if(!isset($_GET['id'])){
    die("ID do carro não especificado!");
}
$id_carro = intval($_GET['id']);

// 🔥 Buscar carro + marca (JOIN correto)
$stmt = $conn->prepare("
    SELECT carros.*, modelos.id_marca
    FROM carros
    JOIN modelos ON carros.id_modelo = modelos.id_modelo
    WHERE carros.id_carro = ?
");
$stmt->bind_param("i", $id_carro);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Carro não encontrado!");
}
$carro = $result->fetch_assoc();


// 🔥 PROCESSAR UPDATE
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_marca    = $_POST['id_marca'];
    $nova_marca  = trim($_POST['nova_marca']);
    $id_modelo   = $_POST['id_modelo'];
    $novo_modelo = trim($_POST['novo_modelo']);
    $matricula   = trim($_POST['matricula']);
    $cor         = trim($_POST['cor']);
    $id_cliente  = $_POST['id_cliente'];

    $erros = [];

    // 🔥 VALIDAR MATRÍCULA (não duplicar)
    $stmt = $conn->prepare("
        SELECT id_carro FROM carros 
        WHERE matricula = ? AND id_carro != ?
    ");
    $stmt->bind_param("si", $matricula, $id_carro);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
    $erros['matricula'] = "Matrícula já existe!";

    if(empty($matricula)){
        $erro = "⚠️ Informe a matrícula!";
    }

}elseif(!preg_match("/^[A-Z0-9\-]+$/i", $matricula)){
         $erros['matricula'] = "⚠️ Matrícula inválida!";
    }

if(preg_match('/[0-9]/', $cor)){
    $erros['cor'] = "A cor não pode ter números!";
}

    // 🔥 SE NÃO TIVER ERRO → CONTINUA
    if(empty($erros)){

        // Inserir nova marca
        if($id_marca === "nova" && !empty($nova_marca)){
            $stmt = $conn->prepare("INSERT INTO marcas (nome) VALUES (?)");
            $stmt->bind_param("s", $nova_marca);
            $stmt->execute();
            $id_marca = $conn->insert_id;
        }

        // Inserir novo modelo
        if(!empty($novo_modelo)){
            $stmt = $conn->prepare("INSERT INTO modelos (nome, id_marca) VALUES (?, ?)");
            $stmt->bind_param("si", $novo_modelo, $id_marca);
            $stmt->execute();
            $id_modelo = $conn->insert_id;
        }

        // 🔥 UPDATE
        $stmt = $conn->prepare("
            UPDATE carros 
            SET id_modelo = ?, matricula = ?, cor = ?, id_cliente = ? 
            WHERE id_carro = ?
        ");
        $stmt->bind_param("issii", $id_modelo, $matricula, $cor, $id_cliente, $id_carro);

        if($stmt->execute()){
            header("Location: carros.php?sucesso=2");
            exit;
        } else {
           
        }

    } 
    
}


// 🔥 CLIENTES (últimos 5 + atual)
$res_clientes = $conn->query("SELECT id_cliente, nome FROM clientes ORDER BY id_cliente DESC LIMIT 5");
$clientes = [];

while($c = $res_clientes->fetch_assoc()){
    $clientes[$c['id_cliente']] = $c['nome'];
}

// garantir cliente atual
if(!isset($clientes[$carro['id_cliente']])){
    $stmt = $conn->prepare("SELECT id_cliente, nome FROM clientes WHERE id_cliente = ?");
    $stmt->bind_param("i", $carro['id_cliente']);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        $cliente_atual = $res->fetch_assoc();
        $clientes[$cliente_atual['id_cliente']] = $cliente_atual['nome'];
    }
}


?>