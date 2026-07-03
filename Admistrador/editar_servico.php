<?php

include_once("./configuracao/conexao.php");
include_once("./models/contas_models.php");

$id = intval($_GET['id']);

/* ================= SERVIÇO ================= */
$sql_servico = "SELECT * FROM servicos WHERE id = $id";
$result_servico = mysqli_query($conn, $sql_servico);

if (!$result_servico) {
    die("Erro SQL Serviço: " . mysqli_error($conn));
}

$servico = mysqli_fetch_assoc($result_servico);

if (!$servico) {
    die("Serviço não encontrado.");
}

/* ================= ITENS ================= */
$sql_itens = "SELECT * FROM servico_itens WHERE id_servico = $id";
$result_itens = mysqli_query($conn, $sql_itens);

if (!$result_itens) {
    die("Erro SQL Itens: " . mysqli_error($conn));
}

/* ================= MECÂNICOS ================= */
$sql_mecanicos = "
SELECT *
FROM funcionarios
WHERE status = 'Activo'
AND cargo = 'Mecanico'
";

$result_mecanicos = mysqli_query($conn, $sql_mecanicos);

// Lista de serviços padrão para validação do Select
$servicos_padrao = [
    "Troca de Óleo e Filtro" => 15000,
    "Revisão Geral Preventiva" => 45000,
    "Alinhamento e Balanceamento" => 20000,
    "Manutenção do Sistema de Travões" => 25000,
    "Reparação de Suspensão" => 35000,
    "Diagnóstico Eletrónico via Scanner" => 18000,
    "Reparação do Sistema de Injeção" => 40000,
    "Manutenção de Ar Condicionado" => 30000,
    "Troca de Embraiagem" => 60000,
    "Reparação Elétrica Geral" => 22000
];

$descricao_atual = $servico['descricao_servico'] ?? '';
$e_servico_customizado = !empty($descricao_atual) && !array_key_exists($descricao_atual, $servicos_padrao);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg" href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/registar_servicos.css">
</head>
<body>
    
<div class="container">
    <?php include_once('sidebar.php');?>
             
    <div class="containerc">

        <a href="servicos.php" class="btn-back">VOLTAR</a>

        <form action="./models/update_servico.php" method="POST">

            <input type="hidden" name="id" value="<?= $servico['id'] ?>">

            <div class="card2">

                <div class="section-header">Informações Gerais</div>

                <div class="row">
                    <div class="col">
                        <label>Descrição do Serviço *</label>
                        <select name="select_servico_geral" id="select_servico_geral" onchange="verificarServicoPredefinido(this)">
                            <option value="" data-preco="0">Selecione um serviço</option>
                            <?php foreach ($servicos_padrao as $nome_srv => $preco_srv): ?>
                                <option value="<?= $nome_srv ?>" data-preco="<?= $preco_srv ?>" <?= ($descricao_atual === $nome_srv) ? 'selected' : '' ?>>
                                    <?= $nome_srv ?> — <?= number_format($preco_srv, 0, ',', '.') ?> Akz
                                </option>
                            <?php endforeach; ?>
                            <option value="Outro" <?= $e_servico_customizado ? 'selected' : '' ?>>Outro Serviço...</option>
                        </select>
                        
                        <div id="container_outro_servico" style="display: <?= $e_servico_customizado ? 'block' : 'none' ?>; margin-top: 10px;">
                            <input type="text" name="nome_servico_geral_outro" id="nome_servico_geral_outro" 
                                   value="<?= $e_servico_customizado ? htmlspecialchars($descricao_atual) : '' ?>" placeholder="Escreva aqui a descrição do serviço...">
                        </div>
                        
                        <input type="hidden" name="descricao_servico" id="nome_servico_geral" value="<?= htmlspecialchars($descricao_atual) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Placa</label>
                        <input type="text" name="placa_carro" id="placa_carro"
                            value="<?= htmlspecialchars($servico['placa_carro']) ?>">
                    </div>

                    <div class="col">
                        <label>Modelo</label>
                        <input type="text" name="modelo_carro_geral" id="modelo_carro_geral"
                            value="<?= htmlspecialchars($servico['modelo_carro']) ?>">
                    </div>

                    <div class="col">
                        <label>Cliente</label>
                        <input type="text" name="nome_cliente" id="nome_cliente"
                            value="<?= htmlspecialchars($servico['nome_cliente']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Local</label>
                        <select name="local" id="local">
                            <option value="Oficina" <?= ($servico['endereco'] == 'Oficina') ? 'selected' : '' ?>>Oficina</option>
                            <option value="Fora" <?= ($servico['endereco'] != 'Oficina') ? 'selected' : '' ?>>Fora</option>
                        </select>
                    </div>

                    <div class="col" id="campoEndereco" style="display:none;">
                        <label>Endereço</label>
                        <input type="text" name="endereco_cliente" id="endereco_cliente"
                            value="<?= htmlspecialchars($servico['endereco']) ?>">
                    </div>

                    <input type="hidden" name="endereco_final" id="endereco_final"
                        value="<?= htmlspecialchars($servico['endereco']) ?>">

                    <div class="col">
                        <label>Status</label>
                        <select name="status">
                            <option value="Pendente" <?= ($servico['status'] == 'Pendente') ? 'selected' : '' ?>>Pendente</option>
                            <option value="andamento" <?= ($servico['status'] == 'andamento') ? 'selected' : '' ?>>andamento</option>
                            <option value="Concluido" <?= ($servico['status'] == 'Concluido') ? 'selected' : '' ?>>Concluído</option>
                            <option value="cancelado" <?= ($servico['status'] == 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Mecânico Responsável</label>
                        <select name="id_mecanico">
                            <?php while($mecanico = mysqli_fetch_assoc($result_mecanicos)): ?>
                                <option value="<?= $mecanico['id_funcionario'] ?>"
                                    <?= ($servico['id_mecanico'] == $mecanico['id_funcionario']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($mecanico['nome']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Observações</label>
                        <textarea name="obs" rows="5"><?= htmlspecialchars($servico['obs']) ?></textarea>
                    </div>
                </div>

            </div>

            <div class="card2">

                <div class="section-header">Peças / Serviços</div>

                <table id="tabelaItens">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Peça</th>
                            <th>Qtd</th>
                            <th>Preço</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($item = mysqli_fetch_assoc($result_itens)): ?>
                        <tr>
                            <td><input type="text" name="codigo_da_peca[]" value="<?= $item['codigo_da_peca'] ?>" onkeyup="buscarProduto(this)"></td>
                            <td><input type="text" name="peca_materia[]" value="<?= $item['peca_materia'] ?>"></td>
                            <td><input type="number" name="quantidade[]" value="<?= $item['quantidade'] ?>" onchange="calcularTotal()"></td>
                            <td><input type="number" name="preco[]" value="<?= $item['preco'] ?>" onchange="calcularTotal()"></td>
                            <td><button type="button" class="btn-remove" onclick="removerLinha(this)">✕</button></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

                <button class="btn-add" type="button" onclick="adicionarLinha()">+ ADICIONAR PEÇA</button>

            </div>

            <div class="card2">
                <label>Taxa Deslocação</label>
                <input type="number" name="taxa_deslocacao" id="taxa_deslocacao" value="<?= $servico['deslocacao'] ?>" onchange="calcularTotal()">

                <label>Cobrança</label>
                <input type="number" name="taxa_alimentacao" id="taxa_alimentacao" value="<?= $servico['cobranca'] ?>" onchange="calcularTotal()">
            </div>

            <div class="footer-containert">
                <button class="btn-save" type="submit">ATUALIZAR</button>
                <h2 class="total-containert" id="displayTotal">
                    <?= number_format($servico['total'], 2, ',', '.') ?> Akz
                </h2>
            </div>

        </form>
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

            <div class="theme-toggler">
                <span class="material-icons active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sun-fill" viewBox="0 0 16 16">
                     <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
                    </svg>
                 </span>
                 <span class="material-icons">
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
                <p>Olá! <b><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></b></p>
                <small class="text-muted">admin</small>
            </div>
        </div>
    </div>
</div>

<script src="../views/js/clientes.js"></script>
<script id="fade-js">
let enderecoClienteCarregado = "<?= addslashes($servico['endereco']) ?>";

document.addEventListener("DOMContentLoaded", function() {
    document.body.style.opacity = 0;
    setTimeout(() => { document.body.style.opacity = 1; }, 50);

    const localInicial = document.getElementById("local").value;
    if (localInicial === "Fora") {
        document.getElementById("campoEndereco").style.display = "block";
    }

    const links = document.querySelectorAll("a[href]");
    links.forEach(link => {
        link.addEventListener("click", function(e) {
            const href = link.getAttribute("href");
            if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) return;
            e.preventDefault();
            document.body.classList.add("fade-out");
            setTimeout(() => { window.location.href = href; }, 1000);
        });
    });
});

// ================= GESTÃO DA DESCRIÇÃO DINÂMICA (IGUAL REGISTAR) =================
function verificarServicoPredefinido(select) {
    const containerOutro = document.getElementById("container_outro_servico");
    const inputOutro = document.getElementById("nome_servico_geral_outro");
    const inputFinal = document.getElementById("nome_servico_geral");
    const inputCobranca = document.getElementById("taxa_alimentacao");

    const precoPredefinido = select.options[select.selectedIndex].getAttribute('data-preco');

    if (select.value === "Outro") {
        containerOutro.style.display = "block";
        inputFinal.value = inputOutro.value;
        inputCobranca.value = 0; 
        inputCobranca.readOnly = false;
    } else {
        containerOutro.style.display = "none";
        inputFinal.value = select.value;
        if(precoPredefinido !== null) {
            inputCobranca.value = precoPredefinido;
        }
        inputCobranca.readOnly = (select.value !== "");
    }
    calcularTotal();
}

document.getElementById("nome_servico_geral_outro").addEventListener("input", function() {
    document.getElementById("nome_servico_geral").value = this.value;
});

// ================= PLACA =================
document.getElementById("placa_carro").addEventListener("input", function(){
    let placaFormatada = this.value.toUpperCase().trim();
    this.value = placaFormatada;

    if(placaFormatada.length >= 8){
        fetch("buscar_carro.php?placa=" + encodeURIComponent(placaFormatada))
        .then(res => res.json())
        .then(data => {
            document.getElementById("modelo_carro_geral").value = data.modelo_carro || '';
            document.getElementById("nome_cliente").value = data.nome_cliente || '';
            
            enderecoClienteCarregado = data.endereco || '';
            
            if (document.getElementById("local").value === "Fora") {
                document.getElementById("endereco_cliente").value = enderecoClienteCarregado;
                document.getElementById("endereco_final").value = enderecoClienteCarregado;
            }
        })
        .catch(err => console.log("Erro placa:", err));
    }
});

// ================= LOCAL =================
document.getElementById("local").addEventListener("change", function(){
    const campoEndereco = document.getElementById("campoEndereco");
    const inputEndereco = document.getElementById("endereco_cliente");
    const enderecoFinal = document.getElementById("endereco_final");

    if (this.value === "Fora") {
        campoEndereco.style.display = "block";
        inputEndereco.value = (enderecoClienteCarregado !== "Oficina") ? enderecoClienteCarregado : '';
        enderecoFinal.value = inputEndereco.value;
    } else {
        campoEndereco.style.display = "none";
        inputEndereco.value = '';
        enderecoFinal.value = 'Oficina';
    }
    calcularTotal();
});

document.getElementById("endereco_cliente").addEventListener("input", function() {
    document.getElementById("endereco_final").value = this.value;
});

// ================= PRODUTO =================
function buscarProduto(input){
    let codigo = input.value.trim();
    let row = input.closest("tr");

    if(codigo.length >= 2){
        fetch("buscar_produto.php?codigo=" + encodeURIComponent(codigo))
        .then(res => res.json())
        .then(data => {
            row.querySelector("input[name='peca_materia[]']").value = data.nome || '';
            row.querySelector("input[name='preco[]']").value = data.preco || 0;
            calcularTotal();
        })
        .catch(err => console.log("Erro produto:", err));
    }
}

// ================= TOTAL =================
function calcularTotal(){
    let total = 0;
    let qtd = document.getElementsByName("quantidade[]");
    let preco = document.getElementsByName("preco[]");

    for(let i = 0; i < qtd.length; i++){
        total += (parseFloat(qtd[i].value) || 0) * (parseFloat(preco[i].value) || 0);
    }

    let td = parseFloat(document.getElementById("taxa_deslocacao").value || 0);
    let ta = parseFloat(document.getElementById("taxa_alimentacao").value || 0);

    total += td + ta;

    document.getElementById("displayTotal").innerText = 
        total.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + " Akz";
}

// ================= ADICIONAR LINHA =================
function adicionarLinha(){
    let tbody = document.querySelector("#tabelaItens tbody");
    let tr = document.createElement("tr");

    tr.innerHTML = `
        <td><input type="text" name="codigo_da_peca[]" onkeyup="buscarProduto(this)"></td>
        <td><input type="text" name="peca_materia[]"></td>
        <td><input type="number" name="quantidade[]" value="1" onchange="calcularTotal()"></td>
        <td><input type="number" name="preco[]" value="0" onchange="change="calcularTotal()"></td>
        <td><button type="button" class="btn-remove" onclick="removerLinha(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
}

// ================= REMOVER LINHA =================
function removerLinha(btn){
    let row = btn.closest("tr");
    row.remove();
    calcularTotal();
}
</script>
</body>
</html>