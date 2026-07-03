
<?php
require_once "./configuracao/conexao.php";
$erro = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $id_marca = $_POST['id_marca'];
    $nova_marca = trim($_POST['nova_marca']);
    $id_modelo = $_POST['id_modelo'];    
    $novo_modelo = trim($_POST['novo_modelo']);

    $matricula = trim($_POST['matricula']);
    $cor = trim($_POST['cor']);
    $id_cliente = $_POST['id_cliente'];

    

  // 🔥 VALIDAR MATRÍCULA (não duplicar)
    $stmt = $conn->prepare("
        SELECT id_carro FROM carros 
        WHERE matricula = ? AND id_carro != ?
    ");
    $stmt->bind_param("si", $matricula, $id_carro);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
    $erro = "Matrícula já existe!";
    
    if(empty($matricula)){
        $erro = "⚠️ Informe a matrícula!";
    }

  }elseif(!preg_match("/^[A-Z0-9\-]+$/i", $matricula)){
         $erro = "⚠️ Matrícula inválida!";
    }
    // ✅ VALIDAR COR (não pode ter números)
    if(empty($cor)){
        $erro = "⚠️ Informe a cor!";
    } elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/", $cor)){
        $erro = "⚠️ A cor não pode conter números!";
    }

    // 🔥 SE NÃO HOUVER ERRO
    if($erro == ""){

        // MARCA
        if($id_marca == "nova" && !empty($nova_marca)){
            $sql = "INSERT INTO marcas (nome) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $nova_marca);
            $stmt->execute();
            $id_marca = $stmt->insert_id;
        }

        // MODELO
        if(!empty($novo_modelo)){
            $sql = "INSERT INTO modelos (nome, id_marca) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $novo_modelo, $id_marca);
            $stmt->execute();
            $id_modelo = $stmt->insert_id;
        }

        // CARRO
        $sql = "INSERT INTO carros (id_modelo, matricula, cor, id_cliente)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $id_modelo, $matricula, $cor, $id_cliente);

        if($stmt->execute()){
            header("Location: carros.php?sucesso=1");
            exit;
        } 
    }
}
?>