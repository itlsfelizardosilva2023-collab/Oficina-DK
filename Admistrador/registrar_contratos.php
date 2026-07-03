
<?php
session_start();
  include_once("./models/servico_model.php");




include("configuracao/conexao.php");


// ================= VARIÁVEL DE ERRO (TOPO DA PÁGINA) =================
$erros = [];
/* ================= BUSCAR CLIENTE (AJAX) ================= */
if (isset($_GET['buscar_cliente'])) {

    $bi = trim($_GET['buscar_cliente']);

    if ($bi === "") {
        header("Content-Type: application/json");
        echo json_encode(["status" => "erro", "mensagem" => "Número de BI não informado"]);
        exit;
    }

    $sql = "
        SELECT 
            clientes.id_cliente,
            clientes.nome,
            carros.matricula,
            marcas.nome AS marca,
            modelos.nome AS modelo
        FROM clientes
        LEFT JOIN carros ON carros.id_cliente = clientes.id_cliente
        LEFT JOIN modelos ON carros.id_modelo = modelos.id_modelo
        LEFT JOIN marcas ON modelos.id_marca = marcas.id_marca
        WHERE clientes.numero_bi = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        header("Content-Type: application/json");
        echo json_encode(["status" => "erro", "mensagem" => "Erro na consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $bi);
    $stmt->execute();
    $res = $stmt->get_result();

    $dados = [];
    $carros = [];
    $encontrou = false;

    while ($row = $res->fetch_assoc()) {
        $encontrou = true;
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

    if (!$encontrou) {
        header("Content-Type: application/json");
        echo json_encode(["status" => "erro", "mensagem" => "Cliente não encontrado"]);
        exit;
    }

    $dados['carros'] = $carros;
    header("Content-Type: application/json");
    echo json_encode($dados);
    exit;
}

/* ================= PROCESSAR CONTRATO (POST) ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {

        // ===== CAMPOS OBRIGATÓRIOS =====
        $bi = trim($_POST['numero_bi'] ?? '');
        $data_inicio = trim($_POST['data_inicio'] ?? '');
        $data_fim = trim($_POST['data_fim'] ?? '');
        $numero_transacao = trim($_POST['numero_transacao'] ?? '');

        if ($bi === "") throw new Exception("Número de BI do cliente é obrigatório");
        if ($data_inicio === "") throw new Exception("Data de início é obrigatória");
        if ($data_fim === "") throw new Exception("Data de fim é obrigatória");
        if ($numero_transacao === "") throw new Exception("Número de transação é obrigatório");

        // ===== DATAS =====
        $d1 = DateTime::createFromFormat('Y-m-d', $data_inicio);
        $d2 = DateTime::createFromFormat('Y-m-d', $data_fim);

        if (!$d1) throw new Exception("Data de início inválida");
        if (!$d2) throw new Exception("Data de fim inválida");

        $hoje = new DateTime();
        $hoje->setTime(0, 0, 0);

        if ($d1 < $hoje) throw new Exception("A data de início não pode ser anterior à data de hoje");

        if ($d2 <= $d1) throw new Exception("A data de fim deve ser posterior à data de início");

        // ===== VIATURAS =====
        if (empty($_POST['placa']) || !is_array($_POST['placa'])) {
            throw new Exception("É necessário selecionar pelo menos uma viatura");
        }

        $placasValidas = array_filter(array_map('trim', $_POST['placa']), fn($p) => $p !== "");

        if (count($placasValidas) === 0) {
            throw new Exception("É necessário selecionar pelo menos uma viatura");
        }

        if (count($placasValidas) !== count(array_unique($placasValidas))) {
            throw new Exception("Existem viaturas duplicadas no contrato");
        }

        // ===== PREÇOS =====
        $VALOR_MINIMO_VIATURA = 20000;

        if (!empty($_POST['preco_viatura'])) {
            foreach ($_POST['preco_viatura'] as $i => $preco) {
                if (!is_numeric($preco) || floatval($preco) <= 0) {
                    throw new Exception("Preço inválido na viatura " . ($i + 1));
                }
                if (floatval($preco) < $VALOR_MINIMO_VIATURA) {
                    throw new Exception("O valor do contrato por viatura deve ser no mínimo " . number_format($VALOR_MINIMO_VIATURA, 0, ',', '.') . " Kz (viatura " . ($i + 1) . ")");
                }
            }
        }
        

        // ===== CLIENTE =====
        $sql = "SELECT id_cliente FROM clientes WHERE numero_bi = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Erro na consulta de cliente: " . $conn->error);

        $stmt->bind_param("s", $bi);
        $stmt->execute();
        $cliente = $stmt->get_result()->fetch_assoc();

        if (!$cliente) throw new Exception("Cliente não encontrado");

        $id_cliente = $cliente['id_cliente'];

        // ===== TOTAL =====
        $total = 0;
        foreach ($_POST['preco_viatura'] as $preco) {
            $total += floatval($preco);
        }

        if ($total <= 0) throw new Exception("O valor total do contrato não pode ser zero");

        // ===== INSERIR CONTRATO =====
        $sql = "INSERT INTO contratos 
                (id_cliente, data_inicio, data_fim, numero_transacao, total_geral, criado_em)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Erro ao preparar inserção do contrato: " . $conn->error);

        $stmt->bind_param("isssd", $id_cliente, $data_inicio, $data_fim, $numero_transacao, $total);

        if (!$stmt->execute()) throw new Exception("Erro ao criar contrato: " . $stmt->error);

        $id_contrato = $conn->insert_id;

        // ===== INSERIR CARROS =====
        $placas = $_POST['placa'];
        $precos = $_POST['preco_viatura'];
        $pelo_menos_um_carro_inserido = false;

        foreach ($placas as $i => $placa) {
            $placa = trim($placa);
            if ($placa == "") continue;

            $sql = "SELECT id_carro FROM carros WHERE matricula = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new Exception("Erro ao preparar busca da viatura: " . $conn->error);

            $stmt->bind_param("s", $placa);
            $stmt->execute();
            $carro = $stmt->get_result()->fetch_assoc();

            if (!$carro) throw new Exception("Viatura com matrícula '$placa' não foi encontrada no sistema");

            $id_carro = $carro['id_carro'];
            $preco = floatval($precos[array_search($placa, array_map('trim', $placas))]);

            $sql = "INSERT INTO contrato_carros (id_contrato, id_carro, preco_viatura) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new Exception("Erro ao preparar inserção da viatura: " . $conn->error);

            $stmt->bind_param("iid", $id_contrato, $id_carro, $preco);

            if (!$stmt->execute()) throw new Exception("Erro ao inserir carro: " . $stmt->error);

            $pelo_menos_um_carro_inserido = true;
        }

        if (!$pelo_menos_um_carro_inserido) throw new Exception("Nenhuma viatura válida foi associada ao contrato");

        // ===== SUCESSO =====
        header("Location: fatura_contrato.php?id=" . $id_contrato);
        exit;

    } catch (Exception $e) {
        $erros[] = $e->getMessage();
    }
}

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
<?php if (!empty($erros)): ?>
<div class="alerta-erro">
    <div class="alerta-erro-titulo">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Corrige os seguintes pontos:
    </div>
    <ul>
        <?php foreach ($erros as $erro): ?>
            <li><?= htmlspecialchars($erro) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>


<?php
// Garante pelo menos 1 linha de viatura para exibir, mesmo sem dados antigos
$placas_antigas  = $_POST['placa'] ?? [''];
$marcas_antigas  = $_POST['marca'] ?? [''];
$modelos_antigos = $_POST['modelo'] ?? [''];
$precos_antigos  = $_POST['preco_viatura'] ?? [''];
?>

<form method="POST">

<!-- CLIENTE -->
<label>Cliente</label>

<div class="grid2">

<input type="text" id="bi" name="numero_bi" placeholder="NIF / BI"
       value="<?= htmlspecialchars($_POST['numero_bi'] ?? '') ?>">

<input type="text" id="nome" name="nome" placeholder="Nome Cliente" readonly
       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">

<input type="date" name="data_inicio"
       value="<?= htmlspecialchars($_POST['data_inicio'] ?? '') ?>">

<input type="date" name="data_fim"
       value="<?= htmlspecialchars($_POST['data_fim'] ?? '') ?>">

</div>

<!-- VIATURAS -->
<label>Viaturas</label>

<div id="viaturas">

<?php foreach ($placas_antigas as $i => $placaAntiga): ?>
<div class="gridViatura linhaViatura">

<input type="text" class="placa" name="placa[]" placeholder="Placa"
       value="<?= htmlspecialchars($placaAntiga) ?>">

<input type="text" class="marca" name="marca[]" placeholder="Marca" readonly
       value="<?= htmlspecialchars($marcas_antigas[$i] ?? '') ?>">

<input type="text" class="modelo" name="modelo[]" placeholder="Modelo" readonly
       value="<?= htmlspecialchars($modelos_antigos[$i] ?? '') ?>">

<input type="number"
       class="precoViatura"
       name="preco_viatura[]"
       placeholder="kz"
       value="<?= htmlspecialchars($precos_antigos[$i] ?? '') ?>"
       oninput="calcularTotal()">

<button type="button" class="remover" onclick="remover(this)">
    X
</button>

</div>
<?php endforeach; ?>

</div>

<input type="text" name="numero_transacao" placeholder="Nº Transação"
       value="<?= htmlspecialchars($_POST['numero_transacao'] ?? '') ?>">

<!-- TOTAL -->
<div class="totalBox">
    <h3>Total</h3>
    <input type="hidden" id="total_input" name="total">
    <h1 id="total">0 Akz</h1>
</div>

<!-- BOTÕES -->
<div class="botoes">
   <button type="submit" class="salvar">
    GERAR FATURA
</button>
<a class="Voltar" href="contratos.php">Voltar</a>
</div>

</form>

<script>
// Recalcula o total assim que a página carrega, já que os preços vêm pré-preenchidos
window.addEventListener("DOMContentLoaded", calcularTotal);
</script>
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