<?php
session_start();
include_once("./configuracao/conexao.php");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Agendamento - Oficina D.K</title>
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

        <a href="agendamentos.php" class="btn-back">VOLTAR</a>

        <div class="alerta-topo" id="alertaTopo">
            ⚠️ Corrija os campos assinalados antes de guardar.
        </div>

        <form id="formAgendamento" action="./models/criar_agendamento.php" method="POST" novalidate>

            <div class="card2">
                <div class="section-header">Novo Agendamento</div>

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
                        <label>Modelo *</label>
                        <input type="text" name="modelo_carro" id="modelo_carro">
                        <span class="mensagem-erro" id="erro_modelo">
                            O modelo do carro é obrigatório.
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Nome do Cliente *</label>
                        <input type="text" name="nome_cliente" id="nome_cliente">
                        <span class="mensagem-erro" id="erro_cliente">
                            O nome do cliente é obrigatório.
                        </span>
                    </div>
                    <div class="col">
                        <label>Telefone</label>
                        <input type="text" name="telefone_cliente" id="telefone_cliente">
                    </div>
                </div>

                <input type="hidden" name="id_cliente" id="id_cliente" value="">

                <div class="row">
                    <div class="col">
                        <label>Descrição do Serviço *</label>
                        <input type="text" name="descricao_servico" id="descricao_servico" placeholder="Ex: Troca de Óleo e Filtro">
                        <span class="mensagem-erro" id="erro_descricao">
                            A descrição do serviço é obrigatória.
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Data *</label>
                        <input type="date" name="data_agendada" id="data_agendada" min="<?= date('Y-m-d') ?>">
                        <span class="mensagem-erro" id="erro_data">
                            A data é obrigatória.
                        </span>
                    </div>
                    <div class="col">
                        <label>Hora *</label>
                        <input type="time" name="hora_agendada" id="hora_agendada">
                        <span class="mensagem-erro" id="erro_hora">
                            A hora é obrigatória.
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Local</label>
                        <select name="endereco" id="local">
                            <option value="Oficina">Oficina</option>
                            <option value="Fora">Fora (endereço do cliente)</option>
                        </select>
                    </div>

                    <div class="col" id="campoEndereco" style="display:none;">
                        <label>Endereço</label>
                        <input type="text" name="endereco_cliente" id="endereco_cliente">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>Observações</label>
                        <textarea name="obs" rows="4"></textarea>
                    </div>
                </div>
            </div>

            <div class="footer-containert">
                <button class="btn-save" type="submit">AGENDAR</button>
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
let enderecoClienteCarregado = "";

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


// ================= GESTÃO DINÂMICA DO LOCAL E ENDEREÇO =================
document.getElementById("local").addEventListener("change", function () {
    const campoEndereco = document.getElementById("campoEndereco");
    const inputEndereco = document.getElementById("endereco_cliente");

    if (this.value === "Fora") {
        campoEndereco.style.display = "block";
        inputEndereco.value = enderecoClienteCarregado;
    } else {
        campoEndereco.style.display = "none";
        inputEndereco.value = '';
    }
});


// ================= PLACA (BUSCA AUTOMÁTICA) =================
const campoModelo   = document.getElementById("modelo_carro");
const campoCliente  = document.getElementById("nome_cliente");
const campoTelefone = document.getElementById("telefone_cliente");

function bloquearCampos(bloquear) {
    [campoModelo, campoCliente, campoTelefone].forEach(campo => {
        campo.readOnly = bloquear;
        campo.classList.toggle("campo-bloqueado", bloquear);
    });
}

document.getElementById("placa_carro").addEventListener("input", function () {
    let placaFormatada = this.value.toUpperCase().trim();
    this.value = placaFormatada;

    document.getElementById("id_cliente").value = '';

    if (placaFormatada.length < 8) {
        bloquearCampos(false);
        return;
    }

    fetch("buscar_carro.php?placa=" + encodeURIComponent(placaFormatada))
        .then(res => res.json())
        .then(data => {

            campoModelo.value   = data.modelo_carro    || '';
            campoCliente.value  = data.nome_cliente     || '';
            campoTelefone.value = data.telefone_cliente || '';

            enderecoClienteCarregado = data.endereco || '';

            const localAtual = document.getElementById("local").value;
            if (localAtual === "Fora") {
                document.getElementById("endereco_cliente").value = enderecoClienteCarregado;
            }

            const idCliente = data.id_cliente ? parseInt(data.id_cliente) : 0;
            document.getElementById("id_cliente").value = idCliente > 0 ? idCliente : '';

            // ============ BLOQUEIO DOS CAMPOS QUANDO O CARRO É ENCONTRADO ============
            if (data.modelo_carro && data.nome_cliente) {
                bloquearCampos(true);
            } else {
                bloquearCampos(false);
            }
        })
        .catch(err => {
            console.log("Erro placa:", err);
            bloquearCampos(false);
        });
});


// ================= LIMPAR ERRO DE CAMPO =================
function limparErro(campo, erroId) {
    const el   = document.getElementById(campo);
    const erro = document.getElementById(erroId);
    if (el) {
        el.addEventListener('input',  function () { el.classList.remove('campo-erro'); if (erro) erro.classList.remove('visivel'); });
        el.addEventListener('change', function () { el.classList.remove('campo-erro'); if (erro) erro.classList.remove('visivel'); });
    }
}

limparErro('placa_carro',        'erro_placa');
limparErro('modelo_carro',       'erro_modelo');
limparErro('nome_cliente',       'erro_cliente');
limparErro('descricao_servico',  'erro_descricao');
limparErro('data_agendada',      'erro_data');
limparErro('hora_agendada',      'erro_hora');


// ================= VALIDAÇÃO NO SUBMIT =================
document.getElementById("formAgendamento").addEventListener("submit", function (e) {
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

    if (!document.getElementById("placa_carro").value.trim())       marcarErro('placa_carro',       'erro_placa');
    if (!document.getElementById("modelo_carro").value.trim())      marcarErro('modelo_carro',      'erro_modelo');
    if (!document.getElementById("nome_cliente").value.trim())      marcarErro('nome_cliente',       'erro_cliente');
    if (!document.getElementById("descricao_servico").value.trim()) marcarErro('descricao_servico', 'erro_descricao');
    if (!document.getElementById("data_agendada").value)            marcarErro('data_agendada',     'erro_data');
    if (!document.getElementById("hora_agendada").value)            marcarErro('hora_agendada',     'erro_hora');

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