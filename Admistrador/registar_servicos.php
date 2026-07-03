<?php
session_start();
include_once("./configuracao/conexao.php");
include_once('./configuracao/config.php');
require_once "./funcoes/elimina.php";
include_once("./models/servico_model.php");

$sql_mecanicos = "
SELECT *
FROM funcionarios f
WHERE NOT EXISTS (
    SELECT 1
    FROM servicos s
    WHERE s.id_mecanico = f.id_funcionario
      AND s.status NOT IN ('Concluido', 'Cancelado')
);
";

$result_mecanicos = mysqli_query($conn, $sql_mecanicos);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg" href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/registar_servicos.css">
    <style>
        .campo-erro {
            border: 2px solid #e74c3c !important;
            background-color: #fdf0ef !important;
        }
        .mensagem-erro {
            color: #e74c3c;
            font-size: 0.78rem;
            margin-top: 3px;
            display: none;
        }
        .mensagem-erro.visivel {
            display: block;
        }
        .alerta-topo {
            background: #fdecea;
            border-left: 4px solid #e74c3c;
            color: #c0392b;
            padding: 10px 16px;
            margin-bottom: 16px;
            border-radius: 4px;
            font-size: 0.9rem;
            display: none;
        }
        .alerta-topo.visivel {
            display: block;
        }
        #container_outro_servico {
            margin-top: 10px;
        }
        #wrapperTabelaItens {
            display: none;
            margin-bottom: 15px;
        }
        .campo-bloqueado {
            background-color: #eef0f2 !important;
            color: #555 !important;
            cursor: not-allowed !important;
        }
    </style>
</head>
<body>

<div class="container">
    <?php include_once('sidebar.php'); ?>

    <div class="containerc">

        <a href="servicos.php" class="btn-back">Voltar</a>

        <div class="alerta-topo" id="alertaTopo">
            ⚠️ Corrija os campos assinalados antes de guardar.
        </div>

        <form id="formServico" action="models/registar_servico_models.php" method="POST" novalidate>

            <div class="card2">
                <div class="section-header">Informações Gerais</div>

                <div class="row">
                    <div class="col">
                        <label>Descrição do Serviço *</label>
                        <select name="select_servico_geral" id="select_servico_geral" onchange="verificarServicoPredefinido(this)">
                            <option value="" data-preco="0">Selecione um serviço</option>
                            <option value="Troca de Óleo e Filtro" data-preco="15000">Troca de Óleo e Filtro — 15.000 Akz</option>
                            <option value="Revisão Geral Preventiva" data-preco="45000">Revisão Geral Preventiva — 45.000 Akz</option>
                            <option value="Alinhamento e Balanceamento" data-preco="20000">Alinhamento e Balanceamento — 20.000 Akz</option>
                            <option value="Manutenção do Sistema de Travões" data-preco="25000">Manutenção do Sistema de Travões — 25.000 Akz</option>
                            <option value="Reparação de Suspensão" data-preco="35000">Reparação de Suspensão — 35.000 Akz</option>
                            <option value="Diagnóstico Eletrónico via Scanner" data-preco="18000">Diagnóstico Eletrónico via Scanner — 18.000 Akz</option>
                            <option value="Reparação do Sistema de Injeção" data-preco="40000">Reparação do Sistema de Injeção — 40.000 Akz</option>
                            <option value="Manutenção de Ar Condicionado" data-preco="30000">Manutenção de Ar Condicionado — 30.000 Akz</option>
                            <option value="Troca de Embraiagem" data-preco="60000">Troca de Embraiagem — 60.000 Akz</option>
                            <option value="Reparação Elétrica Geral" data-preco="22000">Reparação Elétrica Geral — 22.000 Akz</option>
                            <option value="Outro" data-preco="0">Outro Serviço...</option>
                        </select>
                        
                        <div id="container_outro_servico" style="display: none;">
                            <input type="text" name="nome_servico_geral_outro" id="nome_servico_geral_outro" placeholder="Escreva aqui a descrição do serviço...">
                        </div>
                        
                        <input type="hidden" name="nome_servico_geral" id="nome_servico_geral" value="">
                        
                        <span class="mensagem-erro" id="erro_nome_servico">
                            A descrição do serviço é obrigatória.
                        </span>
                    </div>
                </div>

                <div class="col">
                    <label>Mecânico Responsável *</label>
                    <select name="id_mecanico" id="id_mecanico">
                        <option value="">Selecione o Mecânico</option>
                        <?php while ($mecanico = mysqli_fetch_assoc($result_mecanicos)): ?>
                            <option value="<?= $mecanico['id_funcionario'] ?>">
                                <?= htmlspecialchars($mecanico['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <span class="mensagem-erro" id="erro_mecanico">
                        Selecione um mecânico responsável.
                    </span>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Placa *</label>
                        <input type="text"
                               name="placa_carro"
                               id="placa_carro"
                               placeholder="LD-60-09-PP"
                               maxlength="12">
                        <span class="mensagem-erro" id="erro_placa">
                            A placa é obrigatória.
                        </span>
                    </div>

                    <div class="col">
                        <label>Modelo do Carro *</label>
                        <input type="text"
                               name="modelo_carro_geral"
                               id="modelo_carro_geral">
                        <span class="mensagem-erro" id="erro_modelo">
                            O modelo do carro é obrigatório.
                        </span>
                    </div>

                    <div class="col">
                        <label>Nome do Cliente *</label>
                        <input type="text"
                               name="nome_cliente"
                               id="nome_cliente">
                        <span class="mensagem-erro" id="erro_cliente">
                            O nome do cliente é obrigatório.
                        </span>
                    </div>
                </div>

                <input type="hidden" name="id_cliente" id="id_cliente" value="">

                <div class="row">
                    <div class="col">
                        <label>Local *</label>
                        <select name="local" id="local">
                            <option value="Oficina">Oficina</option>
                            <option value="Fora">Fora</option>
                        </select>
                    </div>

                    <div class="col" id="campoEndereco" style="display:none;">
                        <label>Endereço *</label>
                        <input type="text"
                               name="endereco_cliente"
                               id="endereco_cliente">
                        <span class="mensagem-erro" id="erro_endereco">
                            O endereço é obrigatório quando o serviço é fora da oficina.
                        </span>
                    </div>

                    <input type="hidden" name="endereco_final" id="endereco_final" value="Oficina">

                    <div class="col">
                        <label>Status</label>
                        <select name="status">
                            <option>Pendente</option>
                            <option>andamento</option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="card2">
                <div class="section-header">Peças / Aplicação de Materiais</div>

                <div id="wrapperTabelaItens">
                    <table id="tabelaItens">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Peça / Serviço</th>
                                <th>Qtd</th>
                                <th>Preço</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>

                <button class="btn-add" type="button" onclick="adicionarLinha()">
                    + ADICIONAR PEÇA
                </button>
            </div>


            <div class="card2">
                <div id="container_deslocacao" style="display: none; margin-bottom: 15px;">
                    <label>Taxa Deslocação</label>
                    <input type="number" name="taxa_deslocacao" id="taxa_deslocacao" value="0" min="0" step="0.01" onchange="calcularTotal()">
                </div>

                <label>Cobrança *</label>
                <input type="number" name="taxa_alimentacao" id="taxa_alimentacao" value="0" min="0" step="0.01" onchange="calcularTotal()">
                <span class="mensagem-erro" id="erro_cobranca">
                    O valor da cobrança deve ser maior ou igual a zero.
                </span>
            </div>


            <div class="card2">
                <label>Observações</label>
                <textarea name="obs" rows="5" maxlength="1000"></textarea>
            </div>


            <div class="footer-containerc">
                <button class="btn-save" type="submit">GUARDAR</button>
                <h2 class="total-containert" id="displayTotal">0,00 Akz</h2>
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
<script>
// Variável global para armazenar temporariamente o endereço retornado pelo fetch da placa
let enderecoClienteCarregado = "";
let clienteTemContrato = false;

// ================= FADE =================
document.addEventListener("DOMContentLoaded", function () {
    document.body.style.opacity = 0;
    setTimeout(() => { document.body.style.opacity = 1; }, 50);

    const links = document.querySelectorAll("a[href]");
    links.forEach(link => {
        link.addEventListener("click", function (e) {
            const href = link.getAttribute("href");
            if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) return;
            e.preventDefault();
            document.body.classList.add("fade-out");
            setTimeout(() => { window.location.href = href; }, 1000);
        });
    });
});

// ================= GESTÃO DA DESCRIÇÃO E PREÇO VIA COBRANÇA =================
function verificarServicoPredefinido(select) {
    const containerOutro = document.getElementById("container_outro_servico");
    const inputOutro = document.getElementById("nome_servico_geral_outro");
    const inputFinal = document.getElementById("nome_servico_geral");
    const inputCobranca = document.getElementById("taxa_alimentacao");
    if (clienteTemContrato) {

    inputCobranca.value = 0;
    inputCobranca.readOnly = true;

    if (select.value === "Outro") {
        document.getElementById("container_outro_servico").style.display = "block";
        inputFinal.value = document.getElementById("nome_servico_geral_outro").value;
    } else {
        document.getElementById("container_outro_servico").style.display = "none";
        inputFinal.value = select.value;
    }

    calcularTotal();
    return;
        }

    const precoPredefinido = select.options[select.selectedIndex].getAttribute('data-preco');

    if (select.value === "Outro") {
        containerOutro.style.display = "block";
        inputFinal.value = inputOutro.value;
        inputCobranca.value = 0; 
        inputCobranca.readOnly = false;
    } else {
        containerOutro.style.display = "none";
        inputFinal.value = select.value;
        inputCobranca.value = precoPredefinido || 0;
        inputCobranca.readOnly = (select.value !== "");
    }

    calcularTotal();
}

document.getElementById("nome_servico_geral_outro").addEventListener("input", function() {
    document.getElementById("nome_servico_geral").value = this.value;
});


// ================= GESTÃO DINÂMICA DO LOCAL E ENDEREÇO =================
document.getElementById("local").addEventListener("change", function () {
    const campoEndereco = document.getElementById("campoEndereco");
    const inputEndereco = document.getElementById("endereco_cliente");
    const enderecoFinal = document.getElementById("endereco_final");
    const containerDeslocacao = document.getElementById("container_deslocacao");
    const inputDeslocacao = document.getElementById("taxa_deslocacao");

    if (this.value === "Fora") {
        campoEndereco.style.display = "block";
        containerDeslocacao.style.display = "block";
        
        // Pega automaticamente o endereço que foi guardado na busca da placa
        inputEndereco.value = enderecoClienteCarregado;
        enderecoFinal.value = enderecoClienteCarregado;
    } else {
        campoEndereco.style.display = "none";
        containerDeslocacao.style.display = "none";
        inputEndereco.value = '';
        enderecoFinal.value = 'Oficina';
        inputDeslocacao.value = 0;
    }
    
    calcularTotal();
});


// ================= PLACA (BUSCA AUTOMÁTICA) =================
document.getElementById("placa_carro").addEventListener("input", function () {
    let placaFormatada = this.value.toUpperCase().trim();
    this.value = placaFormatada;

    document.getElementById("id_cliente").value = '';

    const campoModelo  = document.getElementById("modelo_carro_geral");
    const campoCliente = document.getElementById("nome_cliente");

    if (placaFormatada.length < 8) {
        campoModelo.readOnly  = false;
        campoCliente.readOnly = false;
        campoModelo.classList.remove("campo-bloqueado");
        campoCliente.classList.remove("campo-bloqueado");
    }

    if (placaFormatada.length >= 8) {
        fetch("buscar_carro.php?placa=" + encodeURIComponent(placaFormatada))
            .then(res => res.json())
            .then(data => {

                    document.getElementById("modelo_carro_geral").value = data.modelo_carro || '';
                    document.getElementById("nome_cliente").value = data.nome_cliente || '';

                    // ============ BLOQUEIO DOS CAMPOS QUANDO O CARRO É ENCONTRADO ============
                    if (data.modelo_carro && data.nome_cliente) {
                        campoModelo.readOnly  = true;
                        campoCliente.readOnly = true;
                        campoModelo.classList.add("campo-bloqueado");
                        campoCliente.classList.add("campo-bloqueado");
                    } else {
                        campoModelo.readOnly  = false;
                        campoCliente.readOnly = false;
                        campoModelo.classList.remove("campo-bloqueado");
                        campoCliente.classList.remove("campo-bloqueado");
                    }

                    enderecoClienteCarregado = data.endereco || '';

                    const localAtual = document.getElementById("local").value;

                    if (localAtual === "Fora") {
                        document.getElementById("endereco_cliente").value = enderecoClienteCarregado;
                        document.getElementById("endereco_final").value = enderecoClienteCarregado;
                    }

                    const idCliente = data.id_cliente ? parseInt(data.id_cliente) : 0;
                    document.getElementById("id_cliente").value = idCliente > 0 ? idCliente : '';

                    // ================= CONTRATO =================
                    clienteTemContrato = data.tem_contrato;

                    const cobranca = document.getElementById("taxa_alimentacao");

                    if (clienteTemContrato) {

                        cobranca.value = 0;
                        cobranca.readOnly = true;

                    } else {

                        verificarServicoPredefinido(
                            document.getElementById("select_servico_geral")
                        );

                    }

                    calcularTotal();

                })
            .catch(err => console.log("Erro placa:", err));
    }
});


// ================= PRODUTO =================
function buscarProduto(input) {
    let codigo = input.value.trim();
    let row = input.closest("tr");

    if (codigo.length >= 2) {
        fetch("buscar_produto.php?codigo=" + encodeURIComponent(codigo))
            .then(res => res.json())
            .then(data => {
                row.querySelector("input[name='peca_materia[]']").value = data.nome  || '';
                row.querySelector("input[name='preco[]']").value        = data.preco || 0;
                calcularTotal();
            })
            .catch(err => console.log("Erro produto:", err));
    }
}


// ================= CALCULAR TOTAL =================
function calcularTotal() {
    let total = 0;

    let qtd   = document.getElementsByName("quantidade[]");
    let preco = document.getElementsByName("preco[]");

    for (let i = 0; i < qtd.length; i++) {
        total += (parseFloat(qtd[i].value) || 0) * (parseFloat(preco[i].value) || 0);
    }

    total += parseFloat(document.getElementById("taxa_deslocacao").value) || 0;
    total += parseFloat(document.getElementById("taxa_alimentacao").value) || 0;

    document.getElementById("displayTotal").innerText =
        total.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) + " Akz";
}


// ================= ADICIONAR LINHA =================
function adicionarLinha() {
    const wrapper = document.getElementById("wrapperTabelaItens");
    const tbody = document.querySelector("#tabelaItens tbody");
    
    wrapper.style.display = "block";

    let tr = document.createElement("tr");
    tr.innerHTML = `
        <td><input type="text" name="codigo_da_peca[]" onkeyup="buscarProduto(this)"></td>
        <td><input type="text" name="peca_materia[]" placeholder="Nome da peça" onchange="calcularTotal()"></td>
        <td><input type="number" name="quantidade[]" value="1" min="1" onchange="calcularTotal()"></td>
        <td><input type="number" name="preco[]" value="0" min="0" step="0.01" onchange="calcularTotal()"></td>
        <td><button class="btn-remove" type="button" onclick="removerLinha(this)">✕</button></td>
    `;
    tbody.appendChild(tr);
}


// ================= REMOVER LINHA =================
function removerLinha(btn) {
    let row = btn.closest("tr");
    row.remove();
    
    const linhas = document.querySelectorAll("#tabelaItens tbody tr");
    if (linhas.length === 0) {
        document.getElementById("wrapperTabelaItens").style.display = "none";
    }
    calcularTotal();
}


// ================= LIMPAR ERRO DE CAMPO =================
function limparErro(campo, erroId) {
    const el   = document.getElementById(campo);
    const erro = document.getElementById(erroId);
    if (el) {
        el.addEventListener('input',  function () { el.classList.remove('campo-erro'); if (erro) erro.classList.remove('visivel'); });
        el.addEventListener('change', function () { el.classList.remove('campo-erro'); if (erro) erro.classList.remove('visivel'); });
    }
}

document.getElementById('select_servico_geral').addEventListener('change', function() {
    document.getElementById('select_servico_geral').classList.remove('campo-erro');
    document.getElementById('nome_servico_geral_outro').classList.remove('campo-erro');
    document.getElementById('erro_nome_servico').classList.remove('visivel');
});
document.getElementById('nome_servico_geral_outro').addEventListener('input', function() {
    document.getElementById('nome_servico_geral_outro').classList.remove('campo-erro');
    document.getElementById('erro_nome_servico').classList.remove('visivel');
});
document.getElementById('taxa_alimentacao').addEventListener('input', function() {
    this.classList.remove('campo-erro');
    document.getElementById('erro_cobranca').classList.remove('visivel');
});

limparErro('id_mecanico',        'erro_mecanico');
limparErro('placa_carro',        'erro_placa');
limparErro('modelo_carro_geral', 'erro_modelo');
limparErro('nome_cliente',       'erro_cliente');
limparErro('endereco_cliente',   'erro_endereco');


// ================= VALIDAÇÃO NO SUBMIT =================
document.getElementById("formServico").addEventListener("submit", function (e) {
    e.preventDefault();

    let valido = true;
    const alertaTopo = document.getElementById("alertaTopo");
    alertaTopo.classList.remove("visivel");

    function marcarErro(campoId, erroId) {
        const campo = document.getElementById(campoId);
        const erro  = document.getElementById(erroId);
        if (campo) campo.classList.add('campo-erro');
        if (erro)  erro.classList.add('visivel');
        valido = false;
    }

    const selectServico = document.getElementById("select_servico_geral");
    const inputFinalServico = document.getElementById("nome_servico_geral");
    const cobrancaInput = document.getElementById("taxa_alimentacao");

    if (!selectServico.value) {
        marcarErro('select_servico_geral', 'erro_nome_servico');
    } else if (selectServico.value === "Outro" && !inputFinalServico.value.trim()) {
        marcarErro('nome_servico_geral_outro', 'erro_nome_servico');
    }

    if (cobrancaInput.value === "" || parseFloat(cobrancaInput.value) < 0) {
        marcarErro('taxa_alimentacao', 'erro_cobranca');
    }

    if (!document.getElementById("id_mecanico").value)              marcarErro('id_mecanico',        'erro_mecanico');
    if (!document.getElementById("placa_carro").value.trim())        marcarErro('placa_carro',        'erro_placa');
    if (!document.getElementById("modelo_carro_geral").value.trim()) marcarErro('modelo_carro_geral', 'erro_modelo');
    if (!document.getElementById("nome_cliente").value.trim())       marcarErro('nome_cliente',       'erro_cliente');

    const local              = document.getElementById("local").value;
    const enderecoInput      = document.getElementById("endereco_cliente");
    const enderecoFinalInput = document.getElementById("endereco_final");

    if (local === "Fora") {
        const endereco = enderecoInput.value.trim();
        if (!endereco) {
            marcarErro('endereco_cliente', 'erro_endereco');
        } else {
            enderecoFinalInput.value = endereco;
        }
    } else {
        enderecoFinalInput.value = "Oficina";
    }

    if (!valido) {
        alertaTopo.classList.add("visivel");
        alertaTopo.scrollIntoView({ behavior: "smooth", block: "start" });
        return;
    }

    this.submit();
});
</script>

</body>
</html>