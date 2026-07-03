

<?php
session_start();
  include_once("./models/registar_funcionario.php");




include("configuracao/conexao.php");

/* ================= BUSCAR CLIENTE (AJAX) ================= */
if (isset($_GET['buscar_cliente'])) {

    $bi = $_GET['buscar_cliente'];

    $sql = "
        SELECT 
            clientes.id_cliente,
            clientes.nome,
            carros.matricula,
            marcas.nome AS marca,
            modelos.nome AS modelo

        FROM clientes

        LEFT JOIN carros 
            ON carros.id_cliente = clientes.id_cliente

        LEFT JOIN modelos 
            ON carros.id_modelo = modelos.id_modelo

        LEFT JOIN marcas 
            ON modelos.id_marca = marcas.id_marca

        WHERE clientes.numero_bi = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bi);
    $stmt->execute();

    $res = $stmt->get_result();

    $dados = [];
    $carros = [];

    while ($row = $res->fetch_assoc()) {

        $dados['status'] = "sucesso";
        $dados['nome'] = $row['nome'];

        if ($row['matricula']) {
            $carros[] = [
                "placa" => $row['matricula'],
                "marca" => $row['marca'],
                "modelo" => $row['modelo']
            ];
        }
    }

    $dados['carros'] = $carros;

    header("Content-Type: application/json");
    echo json_encode($dados);
    exit;
}

/* ================= CRIAR CONTRATO (só roda se NÃO for o form de atualizar) ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['atualizar'])) {

    // ================= CLIENTE =================
    $bi = $_POST['numero_bi'];

    $sql = "SELECT id_cliente FROM clientes WHERE numero_bi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bi);
    $stmt->execute();
    $res = $stmt->get_result();
    $cliente = $res->fetch_assoc();

    if (!$cliente) {
        die("Cliente não encontrado");
    }

    $id_cliente = $cliente['id_cliente'];

    // ================= TOTAL =================
    $total = 0;

    if (!empty($_POST['preco_viatura'])) {
        foreach ($_POST['preco_viatura'] as $preco) {
            $total += floatval($preco);
        }
    }

    // ================= DADOS CONTRATO =================
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $numero_transacao = $_POST['numero_transacao'];

    // ================= 1. INSERIR CONTRATO =================
    $sql = "INSERT INTO contratos 
        (id_cliente, data_inicio, data_fim, numero_transacao, total_geral, criado_em)
        VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "isssd",
        $id_cliente,
        $data_inicio,
        $data_fim,
        $numero_transacao,
        $total
    );

    if (!$stmt->execute()) {
        die("Erro ao criar contrato: " . $stmt->error);
    }

    $id_contrato = $conn->insert_id;

    // ================= 2. INSERIR CARROS =================
    if (!empty($_POST['placa'])) {

        $placas = $_POST['placa'];
        $precos = $_POST['preco_viatura'];

        foreach ($placas as $i => $placa) {

            $placa = trim($placa);
            if ($placa == "") continue;

            $sql = "SELECT id_carro FROM carros WHERE matricula = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $placa);
            $stmt->execute();

            $res = $stmt->get_result();
            $carro = $res->fetch_assoc();

            if ($carro) {

                $id_carro = $carro['id_carro'];
                $preco = floatval($precos[$i]);

                $sql = "INSERT INTO contrato_carros 
                        (id_contrato, id_carro, preco_viatura)
                        VALUES (?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "iid",
                    $id_contrato,
                    $id_carro,
                    $preco
                );

                if (!$stmt->execute()) {
                    die("Erro ao inserir carro: " . $stmt->error);
                }
            }
        }
    }

    header("Location: fatura_contrato.php?id=" . $id_contrato);
    exit;
}

#####EDIÇÃO##################################################################

// resolve o id tanto se vier por GET (link "editar") quanto por POST (o próprio form de edição)
$id_contrato = isset($_GET['id']) 
    ? intval($_GET['id']) 
    : (isset($_POST['id_contrato']) ? intval($_POST['id_contrato']) : 0);

if ($id_contrato <= 0) {
    die("Contrato inválido");
}

/* ================= PROCESSAR ATUALIZAÇÃO (antes de buscar dados para exibir) ================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar'])) {

    $data_inicio      = $_POST['data_inicio'] ?? '';
    $data_fim         = $_POST['data_fim'] ?? '';
    $numero_transacao = $_POST['numero_transacao'] ?? '';
    $total            = (float) ($_POST['total'] ?? 0);

    $sql = "UPDATE contratos SET
            data_inicio = ?,
            data_fim = ?,
            numero_transacao = ?,
            total_geral = ?
            WHERE id_contrato = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro SQL: " . $conn->error);
    }

    $stmt->bind_param("sssdi", $data_inicio, $data_fim, $numero_transacao, $total, $id_contrato);

    if ($stmt->execute()) {
        header("Location: contratos.php?msg=atualizado");
        exit();
    } else {
        die("Erro ao atualizar contrato: " . $stmt->error);
    }
}

/* ================= CONTRATO (para exibir no formulário) ================= */
$sql = "SELECT c.*, 
               cl.nome, 
               cl.numero_bi
        FROM contratos c
        JOIN clientes cl ON c.id_cliente = cl.id_cliente
        WHERE c.id_contrato = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_contrato);
$stmt->execute();
$contrato = $stmt->get_result()->fetch_assoc();

if (!$contrato) {
    die("Contrato não encontrado");
}

/* ================= VIATURAS DO CONTRATO ================= */
$sql = "
SELECT 
    cc.id_carro,
    c.matricula,
    m.nome AS marca,
    mo.nome AS modelo,
    cc.preco_viatura
FROM contrato_carros cc
INNER JOIN carros c ON cc.id_carro = c.id_carro
INNER JOIN modelos mo ON c.id_modelo = mo.id_modelo
INNER JOIN marcas m ON mo.id_marca = m.id_marca
WHERE cc.id_contrato = ?
";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id_contrato);
$stmt2->execute();
$viaturas = $stmt2->get_result();


?>
<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
     <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/registar_contratos.css">
  </head>
   <body>
    
    <div class="container">
             <?php include_once('sidebar.php');?>
         

                  <div class="containerc">

<div class="rightc">

<h2>CONTRATO</h2>

<form method="POST" action="contratos.php">

<!-- ID oculto -->
<input type="hidden" name="id_contrato" value="<?= $contrato['id_contrato'] ?>">

<!-- CLIENTE -->
<label>Cliente</label>

<div class="grid2">

<input type="text" name="numero_bi"
       value="<?= htmlspecialchars($contrato['numero_bi']) ?>"
       placeholder="NIF / BI">

<input type="text" name="nome"
       value="<?= htmlspecialchars($contrato['nome']) ?>"
       readonly>


</div>

<!-- VIATURAS -->
<label>Viaturas</label>

<div id="viaturas">

<?php while($v = $viaturas->fetch_assoc()): ?>

<div class="gridViatura linhaViatura">

<input type="text" name="placa[]"
       value="<?= htmlspecialchars($v['matricula']) ?>"
       placeholder="Placa">

<input type="text" name="marca[]"
       value="<?= htmlspecialchars($v['marca']) ?>"
       readonly>

<input type="text" name="modelo[]"
       value="<?= htmlspecialchars($v['modelo']) ?>"
       readonly>

<input type="number" name="preco_viatura[]"
       value="<?= $v['preco_viatura'] ?>"
       oninput="calcularTotal()">

<button type="button" class="remover" onclick="remover(this)">
    X
</button>

</div>

<?php endwhile; ?>

</div>

<!-- Nº TRANSACÇÃO -->
<input type="text" name="numero_transacao"
       value="<?= htmlspecialchars($contrato['numero_transacao']) ?>">

<!-- TOTAL -->
<div class="totalBox">
    <h3>Total</h3>

<input type="hidden" id="total_input" name="total"
       value="<?= isset($contrato['total_geral']) ? $contrato['total_geral'] : 0 ?>">


<h1 id="total">
    <?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Akz
</h1>
</div>

<!-- BOTÕES -->
<div class="botoes">

    <button type="submit" name="atualizar" class="salvar">
    Atualizar Contrato
</button>

    <a class="Voltar" href="contratos.php">Voltar</a>

</div>

</form>
</div>

</div>


     
                       

               <div class="right">
                <div class="top">
                 <button id="menu-btn">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                           <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                        </svg>
                    </span>
                 </button>

                    
                    
                </a>
                    <div class="theme-toggler">
                    <span class="material-icons active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun-fill" viewBox="0 0 16 16">
                         <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
                        </svg>
                     </span>
                        <span class="material-icons ">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon-fill" viewBox="0 0 16 16">
                             <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
                           </svg>
                         </span>
                    </div>

                    
                    <div class="profile">
                        <a href="../logout.php">
                          <h3>Logout</h3>
                       </a>
                     </div>
                        <div class="info">
                            <p>Olá!
                                 <b>
                                    <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                                 </b>
                             </p>
                             <small class="text-muted">admin</small>
                        </div>
                     </div>  
                </div>         

        </div>

    <script src="../views/js/clientes.js"></script>
    <script id="fade-js">
                document.addEventListener("DOMContentLoaded", function() {
                // Fade-in ao carregar a página
                document.body.style.opacity = 0;
                setTimeout(() => {
                    document.body.style.opacity = 1;
                }, 50);

                // Seleciona todos os links internos
                const links = document.querySelectorAll("a[href]");
                
                links.forEach(link => {
                    link.addEventListener("click", function(e) {
                        const href = link.getAttribute("href");

                        // Ignora links externos ou âncoras
                        if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) {
                            return;
                        }

                        e.preventDefault(); // impede navegação imediata
                        document.body.classList.add("fade-out"); // inicia fade-out

                        // Espera a transição antes de mudar de página
                        setTimeout(() => {
                            window.location.href = href;
                        }, 1000); // deve ser igual ao tempo do CSS
                    });
                });
            });
             document.addEventListener("click", function(){

      let erro = document.querySelector(".erro");


      if(erro){
      erro.style.display = "none";
      }


      });



document.getElementById("bi").addEventListener("keyup", function(){

    let bi = this.value;

    fetch("?buscar_cliente=" + bi)

    .then(r => r.json())

    .then(d => {

        console.log(d);

        if(d.status === "sucesso"){

            document.getElementById("nome").value = d.nome;

            document.getElementById("viaturas").innerHTML = "";

            d.carros.forEach(carro => {

                let div = document.createElement("div");

                div.classList.add("gridViatura","linhaViatura");

                div.innerHTML = `

                    <input type="text"
                           class="placa"
                           name="placa[]"
                           value="${carro.placa}">

                    <input type="text"
                           class="marca"
                           name="marca[]"
                           value="${carro.marca}"
                           readonly>

                    <input type="text"
                           class="modelo"
                           name="modelo[]"
                           value="${carro.modelo}"
                           readonly>

                                    <input type="number"
                    class="precoViatura"
                    name="preco_viatura[]"
                    placeholder="kz"
                    oninput="calcularTotal()">
                    
                    <button type="button"
                            class="remover"
                            onclick="remover(this)">
                        X
                    </button>
                `;

                document.getElementById("viaturas").appendChild(div);

            });

        }

    });

});


function remover(botao){

    if(confirm("Deseja eliminar esta viatura?")){

        botao.parentElement.remove();

    }

}

function calcularTotal() {

    let inputs = document.querySelectorAll(".precoViatura");
    let total = 0;

    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    document.getElementById("total").innerText = total + " Akz";
    document.getElementById("total_input").value = total;
}

function remover(btn) {
    btn.parentElement.remove();
    calcularTotal(); 
}

</script>
 </body>
</html>