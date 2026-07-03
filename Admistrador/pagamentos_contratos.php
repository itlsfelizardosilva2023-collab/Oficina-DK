<?php


// pagamento_contrato.php
session_start();
include("./configuracao/conexao.php");

$contrato_id = isset($_GET['id_contrato']) ? intval($_GET['id_contrato']) : 0;

if (!$contrato_id) {
    header("Location: lista_contratos.php");
    exit;
}

// Buscar dados do contrato
$sql_contrato = "
    SELECT c.*, cl.nome AS cliente_nome, cl.telefone AS cliente_telefone,
           cl.numero_bi AS cliente_nif, cl.endereco AS cliente_endereco,
           GROUP_CONCAT(CONCAT(ma.nome, ' ', mo.nome, ' (', ca.matricula, ')') SEPARATOR ' | ') AS veiculos
    FROM contratos c
    JOIN clientes cl ON c.id_cliente = cl.id_cliente
    LEFT JOIN contrato_carros cc ON cc.id_contrato = c.id_contrato
    LEFT JOIN carros ca ON ca.id_carro = cc.id_carro
    LEFT JOIN modelos mo ON mo.id_modelo = ca.id_modelo
    LEFT JOIN marcas ma ON ma.id_marca = mo.id_marca
    WHERE c.id_contrato = ?
    GROUP BY c.id_contrato
";
$stmt = $conn->prepare($sql_contrato);
$stmt->bind_param("i", $contrato_id);
$stmt->execute();
$contrato = $stmt->get_result()->fetch_assoc();

if (!$contrato) {
    header("Location: lista_contratos.php");
    exit;
}

$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$ano_atual = date('Y');
$mes_atual = date('n');

// ===== PERÍODO REAL DO CONTRATO =====
$data_inicio = new DateTime($contrato['data_inicio']);
$data_fim    = new DateTime($contrato['data_fim']);
$ano_inicio_contrato = intval($data_inicio->format('Y'));
$ano_fim_contrato    = intval($data_fim->format('Y'));

// ===== VALIDAÇÃO: ano selecionado tem que estar dentro do período do contrato =====
$ano_sel = isset($_GET['ano']) ? intval($_GET['ano']) : $ano_atual;

if ($ano_sel < $ano_inicio_contrato) {
    $ano_sel = $ano_inicio_contrato;
}
if ($ano_sel > $ano_fim_contrato) {
    $ano_sel = $ano_fim_contrato;
}

// Lista de anos válidos, para usares no <select> do filtro
$anos_disponiveis = range($ano_inicio_contrato, $ano_fim_contrato);

// ===== MÊS INÍCIO / FIM dentro do ano selecionado =====
$mes_inicio = ($ano_sel == $ano_inicio_contrato) ? intval($data_inicio->format('n')) : 1;
$mes_fim    = ($ano_sel == $ano_fim_contrato)    ? intval($data_fim->format('n'))    : 12;

// ===== BUSCAR PAGAMENTOS — agora filtrado também por ano =====
$sql_pag = "SELECT * FROM pagamentos_contrato WHERE contrato_id = ? AND ano = ? ORDER BY mes ASC";
$stmt2 = $conn->prepare($sql_pag);
$stmt2->bind_param("ii", $contrato_id, $ano_sel);
$stmt2->execute();
$result_pag = $stmt2->get_result();

$pagamentos = [];
while ($row = $result_pag->fetch_assoc()) {
    $pagamentos[$row['mes']] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pagamento do Contrato – Oficina D.K</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../views/css/pagamentos_contratos.css?v=1.0">
<link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">

</head>
<body>
 

 <?php include_once('sidebar.php');?>
<div class="page-body">
   <a href="contratos.php" class="back-btn"><i class="bi bi-arrow-left">Voltar</i></a>
  <!-- CARD CONTRATO -->
  <div class="contract-card">
    <div>
      <div class="section-label"><i class="bi bi-person"></i> Cliente</div>
      <div class="val"><?= htmlspecialchars($contrato['cliente_nome']) ?></div>
      <div style="font-size:13px;color:var(--text-muted);margin-top:3px;"><?= htmlspecialchars($contrato['cliente_telefone']) ?></div>
    </div>
    <div>
      <div class="section-label"><i class="bi bi-car-front"></i> Veículos</div>
      <div class="val" style="font-size:13px;"><?= htmlspecialchars($contrato['veiculos'] ?? '–') ?></div>
    </div>
    <div>
      <div class="section-label"><i class="bi bi-cash-stack"></i> Valor Mensal</div>
      <div class="val big"><?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Kz</div>
    </div>
    <div class="divider"></div>
    <div>
      <div class="section-label"><i class="bi bi-calendar-range"></i> Período</div>
      <div class="val"><?= date('d/m/Y', strtotime($contrato['data_inicio'])) ?> → <?= date('d/m/Y', strtotime($contrato['data_fim'])) ?></div>
    </div>
    <div>
      <div class="section-label"><i class="bi bi-card-text"></i> NIF Cliente</div>
      <div class="val"><?= htmlspecialchars($contrato['cliente_nif'] ?? '–') ?></div>
    </div>
    <div>
      <div class="section-label"><i class="bi bi-geo-alt"></i> Endereço</div>
      <div class="val" style="font-size:13px;"><?= htmlspecialchars($contrato['cliente_endereco'] ?? '–') ?></div>
    </div>
  </div>
 
  <!-- YEAR BAR -->
  <div class="year-bar">
    <label><i class="bi bi-calendar3" style="color:var(--gold);margin-right:6px;"></i>Ano:</label>
    <select id="selectAno" onchange="location.href='?id_contrato=<?= $contrato_id ?>&ano='+this.value">
  <?php
  $ano_ini_c = intval((new DateTime($contrato['data_inicio']))->format('Y'));
  $ano_fim_c = intval((new DateTime($contrato['data_fim']))->format('Y'));
  for($y = $ano_ini_c; $y <= $ano_fim_c; $y++): ?>
    <option value="<?= $y ?>" <?= (isset($_GET['ano']) && $_GET['ano'] == $y) || (!isset($_GET['ano']) && $y == $ano_atual) ? 'selected' : '' ?>><?= $y ?></option>
  <?php endfor; ?>
</select>
    <div class="summary-pills">
      <div class="pill pago"><i class="bi bi-check-circle-fill"></i> <?= count(array_filter($pagamentos, fn($p) => $p['status'] === 'pago')) ?> Pagos</div>
      <div class="pill pendente"><i class="bi bi-clock"></i> <?= count(array_filter($pagamentos, fn($p) => $p['status'] === 'pendente')) ?> Pendentes</div>
    </div>
  </div>
 
  <!-- TABELA MESES -->
  <div class="meses-table">
    <table>
      <thead>
        <tr>
          <th>Mês</th>
          <th>Valor</th>
          <th>Vencimento</th>
          <th>Status</th>
          <th>Transação</th>
          <th>Acções</th>
        </tr>
      </thead>
      <tbody>
        <?php

$ano_sel = isset($_GET['ano']) ? intval($_GET['ano']) : $ano_atual;
foreach ($meses as $num => $nome):

  // ===== PULAR MESES FORA DO PERÍODO DO CONTRATO =====
  if ($num < $mes_inicio || $num > $mes_fim) continue;

  $pag = $pagamentos[$num] ?? null;
  $venc = date('d/m/Y', mktime(0,0,0,$num,10,$ano_sel));
  $passado = ($ano_sel < $ano_atual) || ($ano_sel == $ano_atual && $num < $mes_atual);
  $futuro   = ($ano_sel > $ano_atual) || ($ano_sel == $ano_atual && $num > $mes_atual);

  if ($pag) {
    $status = $pag['status']; // 'pago' ou 'pendente'
  } elseif ($futuro) {
    $status = 'futuro';
  } elseif ($passado) {
    $status = 'atrasado';
  } else {
    $status = 'pendente';
  }
?>
      
        <tr>
          <td>
            <span class="mes-num"><?= $num ?></span>
            <span class="mes-nome"><?= $nome ?></span>
          </td>
          <td>
            <div class="valor-mes"><?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Kz</div>
          </td>
          <td style="color:var(--text-muted);font-size:13px;"><?= $venc ?></td>
          <td>
            <?php
              $icons = ['pago'=>'bi-check-circle-fill','pendente'=>'bi-clock','atrasado'=>'bi-exclamation-circle-fill','futuro'=>'bi-calendar'];
              $labels = ['pago'=>'Pago','pendente'=>'Pendente','atrasado'=>'Atrasado','futuro'=>'Futuro'];
            ?>
            <span class="status-badge <?= $status ?>">
              <i class="bi <?= $icons[$status] ?>"></i>
              <?= $labels[$status] ?>
            </span>
          </td>
          <td style="font-size:12px;color:var(--text-muted);">
            <?php if ($pag && $pag['num_transacao']): ?>
              <code style="background:var(--gold-dim);color:var(--gold-light);padding:3px 8px;border-radius:4px;font-size:12px;"><?= htmlspecialchars($pag['num_transacao']) ?></code>
              <div class="data-pag"><?= $pag['data_pagamento'] ? date('d/m/Y', strtotime($pag['data_pagamento'])) : '–' ?></div>
            <?php else: echo '–'; endif; ?>
          </td>
          <td>
            <div class="acoes">
              <?php if ($status === 'pago'): ?>
              <button class="btn-imprimir" onclick='verFatura(<?= $num ?>, "<?= htmlspecialchars($nome, ENT_QUOTES) ?>", "<?= $ano_sel ?>", <?= htmlspecialchars(json_encode($pag), ENT_QUOTES, "UTF-8") ?>)'>
  <i class="bi bi-printer"></i> Fatura
</button>
                  <i class="bi bi-printer"></i> 
                </button>
              <?php elseif ($status !== 'futuro'): ?>
                <button class="btn-pagar" onclick="abrirModal(<?= $num ?>, '<?= $nome ?>', '<?= $ano_sel ?>')">
                  <i class="bi bi-credit-card"></i> Pagar
                </button>
              <?php else: ?>
                <span class="btn-disabled"><i class="bi bi-lock"></i> Aguardar</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
 
</div><!-- /page-body -->
 
<!-- ════════════════════════════════
     MODAL PAGAMENTO
════════════════════════════════ -->
<div class="modal-overlay" id="modalPagamento">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Efectuar <span>Pagamento</span></div>
      <button class="modal-close" onclick="fecharModal()"><i class="bi bi-x">X</i></button>
    </div>
 
    <div class="modal-info-strip">
      <i class="bi bi-calendar-check icon"></i>
      <div class="info-text">
        <div class="label">Mês de Referência</div>
        <div class="val" id="modal-mes-label">–</div>
      </div>
      <div style="margin-left:auto;text-align:right;">
        <div style="font-size:11px;color:var(--text-muted);">Valor</div>
        <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:var(--gold-light);">
          <?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Kz
        </div>
      </div>
    </div>
 
    <div class="form-group">
      <label class="form-label"><i class="bi bi-hash"></i> Número de Transação</label>
      <input type="text" class="form-control" id="num_transacao" placeholder="Ex: 17890028" autocomplete="off">
    </div>
 
    <div class="form-group">
      <label class="form-label"><i class="bi bi-chat-left-text"></i> Observação</label>
      <textarea class="form-control" id="observacao" placeholder="Ex: Pago via Multicaixa Express..."></textarea>
    </div>
 
    <div class="form-group">
      <label class="form-label"><i class="bi bi-calendar-date"></i> Data do Pagamento</label>
      <input type="date" class="form-control" id="data_pagamento" value="<?= date('Y-m-d') ?>">
    </div>
 
    <div class="modal-footer">
      <button class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
      <button class="btn-confirmar-pagar" onclick="confirmarPagamento()">
        <span class="spinner" id="spinner-pay"></span>
        <i class="bi bi-check-lg" id="icon-pay"></i>
        Confirmar Pagamento
      </button>
    </div>
  </div>
</div>
 
<!-- TOAST -->
<div class="toast" id="toast">
  <i class="bi bi-check-circle-fill"></i>
  <span id="toast-msg">Pagamento registado com sucesso!</span>
</div>
 
<!-- ════════════════════════════════
     ÁREA DE FATURA (para impressão)
════════════════════════════════ -->
<div id="area-fatura">
  <button class="btn-close-fatura" onclick="fecharFatura()"><i class="bi bi-x"></i></button>
  <button class="btn-print-fatura" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
 
  <div class="fatura-header-print">
    <div class="fatura-empresa">
      <h2><span><img src="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png" alt="" width="90px"></span></h2>
      <p>Oficina &amp; Serviços Automóveis</p>
      <p>Capalanga Vila, Luanda, Angola</p>
      <p>NIF: 000000000</p>
    </div>
    <div class="fatura-titulo">
      <h3>Fatura</h3>
      <div class="num" id="fatura-num">Nº –</div>
      <div style="font-size:12px;color:#888;margin-top:4px;" id="fatura-data">Data: –</div>
    </div>
  </div>
 
  <div class="fatura-grid-info">
    <div class="fatura-info-box">
      <div class="lbl">Faturado a</div>
      <p id="fatura-cliente-nome"><strong><?= htmlspecialchars($contrato['cliente_nome']) ?></strong></p>
      <p id="fatura-cliente-nif">NIF: <?= htmlspecialchars($contrato['cliente_nif'] ?? '–') ?></p>
      <p id="fatura-cliente-end"><?= htmlspecialchars($contrato['cliente_endereco'] ?? '') ?></p>
      <p id="fatura-cliente-tel"><?= htmlspecialchars($contrato['cliente_telefone'] ?? '') ?></p>
    </div>
    <div class="fatura-info-box">
      <div class="lbl">Referência</div>
      <p>Contrato Nº <?= str_pad($contrato['id_contrato'], 4, '0', STR_PAD_LEFT) ?></p>
      <p>Veículos: <?= htmlspecialchars($contrato['veiculos'] ?? '–') ?></p>
      <p id="fatura-ref-mes">Mês: –</p>
      <p id="fatura-transacao">Transação: –</p>
    </div>
  </div>
 
  <div class="fatura-table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Descrição</th>
          <th>Qtd</th>
          <th>Preço Unit.</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td id="fatura-desc-servico">Manutenção Mensal – –</td>
          <td>1</td>
          <td id="fatura-valor-unit"><?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Kz</td>
          <td id="fatura-valor-total"><?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Kz</td>
          
        </tr>
      </tbody>
    </table>
  </div>
 
  <div class="fatura-total-row">
    <div class="fatura-total-box">
      <div class="t-label">TOTAL A PAGAR</div>
      <div class="t-val" id="fatura-total-val"><?= number_format($contrato['total_geral'] ?? 0, 2, ',', '.') ?> Kz</div>
    </div>
  </div>
 
  <div id="fatura-obs" style="margin-top:16px;padding:12px 16px;background:#f7f7f7;border-radius:8px;font-size:13px;color:#555;display:none;">
    <strong>Observação:</strong> <span id="fatura-obs-text"></span>
  </div>
 
  <div class="fatura-rodape">
    <div class="assinatura">
      <div class="linha"></div>
      <p>Assinatura do Cliente</p>
    </div>
    <div class="fatura-stamp">PAGO</div>
    <div class="assinatura">
      <div class="linha"></div>
      <p>Autorizado por</p>
    </div>
  </div>
</div>
 
<script>
let modalMes = null;
let modalAno = null;
 
function abrirModal(mes, nome, ano) {
  modalMes = mes;
  modalAno = ano;
  document.getElementById('modal-mes-label').textContent = nome + ' / ' + ano;
  document.getElementById('num_transacao').value = '';
  document.getElementById('observacao').value = '';
  document.getElementById('data_pagamento').value = new Date().toISOString().split('T')[0];
  document.getElementById('modalPagamento').classList.add('active');
}
 
function fecharModal() {
  document.getElementById('modalPagamento').classList.remove('active');
}
 
function confirmarPagamento() {
  const num_transacao = document.getElementById('num_transacao').value.trim();
  const observacao    = document.getElementById('observacao').value.trim();
  const data_pag      = document.getElementById('data_pagamento').value;

  if (!num_transacao) {
    document.getElementById('num_transacao').focus();
    document.getElementById('num_transacao').style.borderColor = '#e74c3c';
    return;
  }
  document.getElementById('num_transacao').style.borderColor = '';

  const spinner = document.getElementById('spinner-pay');
  const icon    = document.getElementById('icon-pay');
  spinner.style.display = 'inline-block';
  icon.style.display = 'none';

  const fd = new FormData();
  fd.append('contrato_id', <?= $contrato_id ?>);
  fd.append('mes', modalMes);
  fd.append('ano', modalAno);
  fd.append('num_transacao', num_transacao);
  fd.append('observacao', observacao);
  fd.append('data_pagamento', data_pag);
  fd.append('valor', <?= $contrato['total_geral'] ?? 0 ?>);

  fetch('registar_pagamento_contrato.php', { method: 'POST', body: fd })
    .then(async r => {
      const texto = await r.text();
      try {
        return JSON.parse(texto);
      } catch (e) {
        console.error('Resposta do servidor não é JSON:', texto);
        throw new Error('Resposta inválida do servidor (ver console).');
      }
    })
    .then(res => {
      spinner.style.display = 'none';
      icon.style.display = 'inline-block';
      if (res.sucesso) {
        fecharModal();
        mostrarToast('Pagamento registado com sucesso!');
        setTimeout(() => location.reload(), 1500);
      } else {
        alert('Erro: ' + (res.erro || 'Falha ao registar.'));
      }
    })
    .catch(err => {
      spinner.style.display = 'none';
      icon.style.display = 'inline-block';
      alert(err.message || 'Erro de ligação.');
    });
}
 
function mostrarToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}
 
function verFatura(mes, nomeMes, ano, pag) {
  // Preencher fatura
  const valor = <?= $contrato['total_geral'] ?? 0 ?>;
  const valorFmt = valor.toLocaleString('pt-AO', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' Kz';
  const dataHoje = pag && pag.data_pagamento
    ? new Date(pag.data_pagamento).toLocaleDateString('pt-AO')
    : new Date().toLocaleDateString('pt-AO');
 
  document.getElementById('fatura-num').textContent = 'Nº PAG-' + String(mes).padStart(2,'0') + '/' + ano + '-C<?= str_pad($contrato['id_contrato'],4,"0",STR_PAD_LEFT) ?>';
  document.getElementById('fatura-data').textContent = 'Data: ' + dataHoje;
  document.getElementById('fatura-ref-mes').textContent = 'Mês: ' + nomeMes + ' / ' + ano;
  document.getElementById('fatura-transacao').textContent = 'Transação: ' + (pag && pag.num_transacao ? pag.num_transacao : '–');
  document.getElementById('fatura-desc-servico').textContent = 'Manutenção Mensal – ' + nomeMes + ' ' + ano;
  document.getElementById('fatura-valor-unit').textContent = valorFmt;
  document.getElementById('fatura-valor-total').textContent = valorFmt;
  document.getElementById('fatura-total-val').textContent = valorFmt;
 
  if (pag && pag.observacao) {
    document.getElementById('fatura-obs').style.display = 'block';
    document.getElementById('fatura-obs-text').textContent = pag.observacao;
  } else {
    document.getElementById('fatura-obs').style.display = 'none';
  }
 
  document.getElementById('area-fatura').classList.add('visible');
}
 
function fecharFatura() {
  document.getElementById('area-fatura').classList.remove('visible');
}
 
// Fechar modal ao clicar fora
document.getElementById('modalPagamento').addEventListener('click', function(e) {
  if (e.target === this) fecharModal();
});

// loading--------------------------------
            const container = document.getElementById("container");
            const loading = document.getElementById("loading");

            const vines = [];
            const total = 60;

            // curva infinito
            function infinito(t) {
                const a = 140;
                const x = (a * Math.cos(t)) / (1 + Math.sin(t) * Math.sin(t));
                const y = (a * Math.sin(t) * Math.cos(t)) / (1 + Math.sin(t) * Math.sin(t));
                return { x, y };
            }

            // criar vinhas
            for (let i = 0; i < total; i++) {
                const el = document.createElement("div");
                el.className = "vinha";
                container.appendChild(el);

                vines.push({
                    el,
                    t: (i / total) * Math.PI * 2
                });
            }

            let tempo = 0;

            // animação
            function animar() {
                tempo += 0.06;

                const deslocamentoY = Math.sin(tempo) * 40;

                vines.forEach(v => {
                    v.t += 0.015;

                    const p = infinito(v.t);

                    const cx = 200;
                    const cy = 100 + deslocamentoY;

                    v.el.style.left = (cx + p.x) + "px";
                    v.el.style.top = (cy + p.y) + "px";

                    const scale = 1 + Math.sin(v.t * 3) * 0.6;
                    v.el.style.transform = `scale(${scale})`;
                });

                requestAnimationFrame(animar);
            }

            animar();

            /* esconder loading ao carregar */
            window.addEventListener("load", () => {
                setTimeout(() => {
                    loading.classList.add("hidden");
                }, 1000);
            });

            /* mostrar loading ao clicar em links */
            const links = document.querySelectorAll("a[href]");

            links.forEach(link => {
                link.addEventListener("click", function(e) {
                    const href = link.getAttribute("href");

                    if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) {
                        return;
                    }

                    e.preventDefault();

                    loading.classList.remove("hidden");

                    setTimeout(() => {
                        window.location.href = href;
                    }, 800);
                });
            });

</script>
</body>
</html>