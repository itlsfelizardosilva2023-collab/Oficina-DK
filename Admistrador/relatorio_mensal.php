<?php
session_start();
include_once("./configuracao/conexao.php");
include_once('./configuracao/config.php');

$mes_atual  = (int) date('m');
$ano_atual  = (int) date('Y');
$inicio     = "$ano_atual-" . str_pad($mes_atual,2,'0',STR_PAD_LEFT) . "-01";
$fim        = date('Y-m-t', mktime(0,0,0,$mes_atual,1,$ano_atual));
$meses_pt   = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho',
               'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
$nomeMes    = $meses_pt[$mes_atual];
$dataGeracao = date('d/m/Y H:i');

/* ============================================================
   1. ACTIVIDADE DO MÊS
   ============================================================ */

// Clientes distintos com serviço concluído no mês
$r = $conn->query("
    SELECT COUNT(DISTINCT nome_cliente) AS total
    FROM servicos
    WHERE DATE(data_registo) BETWEEN '$inicio' AND '$fim'
      AND status = 'concluido'
");
$nClientes = $r ? (int)$r->fetch_assoc()['total'] : 0;

// Total serviços concluídos no mês
$r = $conn->query("
    SELECT COUNT(*) AS total
    FROM servicos
    WHERE DATE(data_registo) BETWEEN '$inicio' AND '$fim'
      AND status = 'concluido'
");
$nServicos = $r ? (int)$r->fetch_assoc()['total'] : 0;

// Viaturas distintas com serviço no mês
$r = $conn->query("
    SELECT COUNT(DISTINCT placa_carro) AS total
    FROM servicos
    WHERE DATE(data_registo) BETWEEN '$inicio' AND '$fim'
      AND status = 'concluido'
");
$nCarros = $r ? (int)$r->fetch_assoc()['total'] : 0;

// Contratos activos no mês (data_inicio válida)
$r = $conn->query("
    SELECT COUNT(*) AS total
    FROM contratos
    WHERE status = 'pago'
      AND data_inicio != '1111-11-11'
      AND data_inicio <= '$fim'
      AND (data_fim >= '$inicio' OR data_fim IS NULL)
");
$nContratos = $r ? (int)$r->fetch_assoc()['total'] : 0;

/* ============================================================
   2. FINANCEIRO
   ============================================================ */

// Receita serviços concluídos no mês
$r = $conn->query("
    SELECT COALESCE(SUM(total),0) AS total
    FROM servicos
    WHERE DATE(data_registo) BETWEEN '$inicio' AND '$fim'
      AND status = 'concluido'
");
$receitaServicos = $r ? (float)$r->fetch_assoc()['total'] : 0;

// Receita contratos criados no mês (com data válida)
$r = $conn->query("
    SELECT COALESCE(SUM(total_geral),0) AS total
    FROM contratos
    WHERE DATE(criado_em) BETWEEN '$inicio' AND '$fim'
      AND status = 'pago'
");
$receitaContratos = $r ? (float)$r->fetch_assoc()['total'] : 0;

$receita = $receitaServicos + $receitaContratos;

// Despesas (saídas capital_empresa)
$r = $conn->query("
    SELECT COALESCE(SUM(valor),0) AS total
    FROM capital_empresa
    WHERE fluxo = 'saida'
      AND DATE(data_registro) BETWEEN '$inicio' AND '$fim'
");
$despesas = $r ? (float)$r->fetch_assoc()['total'] : 0;

$liquido = $receita - $despesas;
$margem  = $receita > 0 ? round(($liquido / $receita) * 100, 1) : 0;

// Entradas de capital externo
$r = $conn->query("
    SELECT COALESCE(SUM(valor),0) AS total
    FROM capital_empresa
    WHERE fluxo = 'entrada'
      AND DATE(data_registro) BETWEEN '$inicio' AND '$fim'
");
$capital = $r ? (float)$r->fetch_assoc()['total'] : 0;

/* ============================================================
   3. TOP CLIENTES (por receita de serviços)
   ============================================================ */
$r = $conn->query("
    SELECT
        nome_cliente AS nome,
        COUNT(DISTINCT placa_carro) AS viaturas,
        COUNT(id)                   AS servicos,
        COALESCE(SUM(total), 0)     AS total
    FROM servicos
    WHERE DATE(data_registo) BETWEEN '$inicio' AND '$fim'
      AND status = 'concluido'
    GROUP BY nome_cliente
    ORDER BY total DESC
    LIMIT 5
");
$topClientes = [];
if ($r) while ($row = $r->fetch_assoc()) $topClientes[] = $row;

/* ============================================================
   4. ESTOQUE
   ============================================================ */
$r = $conn->query("
    SELECT
        nome AS peca,
        tipo,
        marca,
        quantidade,
        preco,
        data_expiracao,
        CASE
            WHEN quantidade = 0 THEN 'out'
            WHEN quantidade <= 3 THEN 'low'
            ELSE 'ok'
        END AS estado
    FROM estoque
    ORDER BY
        CASE WHEN quantidade = 0 THEN 0
             WHEN quantidade <= 3 THEN 1
             ELSE 2 END ASC,
        nome ASC
");
$stockRows = [];
if ($r) while ($row = $r->fetch_assoc()) $stockRows[] = $row;

$semStock   = count(array_filter($stockRows, fn($s) => $s['estado'] === 'out'));
$stockBaixo = count(array_filter($stockRows, fn($s) => $s['estado'] === 'low'));
$totalItens = count($stockRows);

/* ============================================================
   5. PEÇAS MAIS USADAS NO MÊS (servico_itens)
   ============================================================ */
$r = $conn->query("
    SELECT
        si.peca_materia AS peca,
        SUM(si.quantidade) AS qtd_usada,
        COUNT(DISTINCT si.id_servico) AS em_servicos
    FROM servico_itens si
    JOIN servicos s ON s.id = si.id_servico
    WHERE DATE(s.data_registo) BETWEEN '$inicio' AND '$fim'
    GROUP BY si.peca_materia
    ORDER BY qtd_usada DESC
    LIMIT 5
");
$pecasUsadas = [];
if ($r) while ($row = $r->fetch_assoc()) $pecasUsadas[] = $row;

/* ============================================================
   6. MECÂNICOS COM MAIS SERVIÇOS
   ============================================================ */
$r = $conn->query("
    SELECT
        f.nome AS mecanico,
        COUNT(s.id) AS servicos,
        COALESCE(SUM(s.total), 0) AS total
    FROM servicos s
    JOIN funcionarios f ON f.id_funcionario = s.id_mecanico
    WHERE DATE(s.data_registo) BETWEEN '$inicio' AND '$fim'
      AND s.status = 'concluido'
    GROUP BY s.id_mecanico, f.nome
    ORDER BY servicos DESC
");
$mecanicos = [];
if ($r) while ($row = $r->fetch_assoc()) $mecanicos[] = $row;
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Oficina D.K</title>
  <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
 
  <style>
    @font-face {
    font-family: 'Inter';
    src: url('Inter-Italic-VariableFont_opsz,wght.ttf');

}

    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{ font-family: 'Inter', sans-serif;background:#0b1e38;min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:40px 16px}

    .demo-card{background:#0f2744;border:1px solid #1e90ff;border-radius:14px;padding:32px;max-width:480px;width:100%;text-align:center}
    .demo-card h2{color:#c9a84c;font-size:13px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;margin-bottom:6px}
    .demo-card p{color:#7a9abf;font-size:13px;margin-bottom:24px}
    .btn-print{display:inline-flex;align-items:center;gap:9px;background:transparent;color:#c9a84c;border:1.5px solid #000000;border-radius:8px;padding:11px 24px;font-family:'Jost',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:background .2s,color .2s}
    .btn-print:hover{background:#c9a84c;color:#0f2744}
    .btn-print svg{width:18px;height:18px;flex-shrink:0}

    /* MODAL */
    .modal-overlay{display:flex;position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9999;align-items:flex-start;justify-content:center;padding:24px 16px 40px;overflow-y:auto}
    .modal-overlay.open{display:flex}
    .modal-box{background:#fff;border-radius:14px;width:100%;max-width:780px;padding:40px 44px;font-family:Georgia,'Times New Roman',serif;color:#1a1a1a}

    /* HEADER */
    .rel-header{display:flex;align-items:flex-start;justify-content:space-between;border-bottom:2.5px solid #0f2744;padding-bottom:18px;margin-bottom:28px;gap:16px}
    .rel-header-left h1{font-family:'Jost',sans-serif;font-size:21px;font-weight:700;color:#0f2744;margin-bottom:4px}
    .rel-header-left p{font-family:'Jost',sans-serif;font-size:12px;color:#666}
    .rel-logo{font-family:'Jost',sans-serif;font-weight:700;font-size:22px;color:#0f2744;white-space:nowrap}
    .rel-logo span{color:#c9a84c}

    .section-title{font-family:'Jost',sans-serif;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#000000;border-bottom:1px solid #e5dfd3;padding-bottom:6px;margin:26px 0 14px}

    .kpi-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}
    .kpi-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px}
    .kpi-card{background:#f8f5ef;border:1px solid #e5dfd3;border-radius:8px;padding:12px 14px}
    .kpi-label{font-family:'Jost',sans-serif;font-size:10px;color:#999;font-weight:500;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em}
    .kpi-value{font-family:'Jost',sans-serif;font-size:22px;font-weight:700;color:#0f2744;line-height:1;margin-bottom:4px}
    .kpi-sub{font-family:'Jost',sans-serif;font-size:11px;color:#888}

    .para{font-size:13.5px;line-height:1.8;color:#2e2e2e;margin-bottom:12px}
    .hl{color:#0f2744;font-weight:600;font-family:'Jost',sans-serif}
    .pos{color:#1a7a4a;font-weight:600;font-family:'Jost',sans-serif}
    .neg{color:#b03030;font-weight:600;font-family:'Jost',sans-serif}
    .warn{color:#b03030;font-weight:600;font-family:'Jost',sans-serif}

    .tbl{width:100%;border-collapse:collapse;font-family:'Jost',sans-serif;font-size:12.5px;margin-top:6px}
    .tbl th{background:#f5f5f5;color:#000000;padding:8px 11px;text-align:left;font-size:10.5px;font-weight:600;letter-spacing:.04em}
    .tbl td{padding:8px 11px;border-bottom:1px solid #ece8e0;color:#333;vertical-align:middle}
    .tbl tr:last-child td{border-bottom:none}
    .tbl .r{text-align:right;font-weight:600;color:#0f2744}
    .tbl .c{text-align:center}

    .badge{display:inline-block;padding:3px 9px;border-radius:4px;font-size:11px;font-weight:600;font-family:'Jost',sans-serif}
    .badge-ok{background:#d1e7dd;color:#0a5c36}
    .badge-low{background:#b03030;color:#000000}
    .badge-out{background:#f8d7da;color:#842029}
    .badge-tipo{background:#e8f0fb;color:#1a4080;font-size:10px;padding:2px 7px}

    .empty{font-family:'Jost',sans-serif;font-size:13px;color:#aaa;padding:12px 0;font-style:italic}

    .modal-footer{display:flex;justify-content:flex-end;gap:10px;margin-top:30px;border-top:1px solid #e5dfd3;padding-top:20px}
    .btn-fechar{background:none;border:1px solid #ccc;border-radius:6px;padding:9px 20px;cursor:pointer;font-size:13px;color:#555;font-family:'Jost',sans-serif;font-weight:500}
    .btn-fechar:hover{background:#f5f5f5}
    .btn-imprimir{background:#1e90ff;border:none;border-radius:6px;padding:9px 22px;cursor:pointer;font-size:13px;color:#000000;font-weight:600;font-family:'Jost',sans-serif;display:flex;align-items:center;gap:7px;transition:background .2s}
    .btn-imprimir:hover{background:#4ba3fc}
    .btn-imprimir svg{width:16px;height:16px}

    @media print{
      body{background:#fff;padding:0}
      .demo-card{display:none}
      .modal-overlay{display:flex!important;position:static;background:none;padding:0;overflow:visible}
      .modal-box{border-radius:0;padding:20px 28px;max-width:100%;box-shadow:none}
      .modal-footer{display:none}
    }
    @media(max-width:600px){
      .modal-box{padding:24px 18px}
      .kpi-grid-4,.kpi-grid-3{grid-template-columns:repeat(2,1fr)}
      .rel-header{flex-direction:column}
    }
  </style>
</head>
<body>


<div class="modal-overlay"  >
  <div class="modal-box">

    <div class="rel-header">
      <div class="rel-header-left">
        <h1>Relatório Mensal</h1>
        <p>Período: <?= $nomeMes . ' de ' . $ano_atual ?> &nbsp;•&nbsp; Gerado em: <?= $dataGeracao ?></p>
      </div>
      <div class="rel-logo"><img src="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png"    width="80px"></div>
    </div>

    <!-- ===== ACTIVIDADE ===== -->
    <div class="section-title">Actividade do mês</div>
    <div class="kpi-grid-4">
      <div class="kpi-card">
        <div class="kpi-label">Clientes atendidos</div>
        <div class="kpi-value"><?= $nClientes ?></div>
        <div class="kpi-sub">Distintos</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Serviços concluídos</div>
        <div class="kpi-value"><?= $nServicos ?></div>
        <div class="kpi-sub">Ordens fechadas</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Viaturas tratadas</div>
        <div class="kpi-value"><?= $nCarros ?></div>
        <div class="kpi-sub">Matrículas distintas</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Contratos activos</div>
        <div class="kpi-value"><?= $nContratos ?></div>
        <div class="kpi-sub">Em vigor</div>
      </div>
    </div>
    <p class="para">
      Em <span class="hl"><?= $nomeMes . ' de ' . $ano_atual ?></span>, a oficina
      atendeu <span class="hl"><?= $nClientes ?> cliente(s)</span> distinto(s),
      realizou <span class="hl"><?= $nServicos ?> serviço(s) concluído(s)</span>
      e interveio em <span class="hl"><?= $nCarros ?> viatura(s)</span>.
      <?php if ($nContratos > 0): ?>
        Estiveram em vigor <span class="hl"><?= $nContratos ?> contrato(s) de manutenção periódica</span>.
      <?php endif ?>
      <?php if ($nClientes > 0 && $nServicos > 0): ?>
        Em média, cada cliente gerou
        <span class="hl"><?= round($nServicos / $nClientes, 1) ?> ordem(ns) de serviço</span>.
      <?php endif ?>
    </p>

    <!-- ===== FINANCEIRO ===== -->
    <div class="section-title">Resumo financeiro</div>
    <div class="kpi-grid-3">
      <div class="kpi-card">
        <div class="kpi-label">Receita total</div>
        <div class="kpi-value" style="font-size:16px">Kz <?= number_format($receita,0,'.','.') ?></div>
        <div class="kpi-sub">Serviços: Kz <?= number_format($receitaServicos,0,'.','.') ?> &nbsp;|&nbsp; Contratos: Kz <?= number_format($receitaContratos,0,'.','.') ?></div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Despesas</div>
        <div class="kpi-value" style="font-size:16px">Kz <?= number_format($despesas,0,'.','.') ?></div>
        <div class="kpi-sub">Saídas registadas</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Resultado líquido</div>
        <div class="kpi-value" style="font-size:16px">Kz <?= number_format($liquido,0,'.','.') ?></div>
        <div class="kpi-sub <?= $liquido >= 0 ? 'pos' : 'neg' ?>"><?= $liquido >= 0 ? '▲' : '▼' ?> <?= abs($margem) ?>% de margem</div>
      </div>
    </div>
    <p class="para">
      A receita total do mês foi de <span class="hl">Kz <?= number_format($receita,0,'.','.') ?></span>
      <?php if ($receitaServicos > 0 && $receitaContratos > 0): ?>
        (Kz <?= number_format($receitaServicos,0,'.','.') ?> em serviços e
        Kz <?= number_format($receitaContratos,0,'.','.') ?> em contratos)
      <?php endif ?>,
      com despesas de <span class="hl">Kz <?= number_format($despesas,0,'.','.') ?></span>.
      O resultado líquido foi de
      <span class="<?= $liquido >= 0 ? 'pos' : 'neg' ?>">Kz <?= number_format($liquido,0,'.','.') ?></span>,
      correspondendo a uma margem de <span class="<?= $liquido >= 0 ? 'pos' : 'neg' ?>"><?= $margem ?>%</span>.
      <?php if ($capital > 0): ?>
        Registaram-se entradas de capital externo no valor de
        <span class="hl">Kz <?= number_format($capital,0,'.','.') ?></span>.
      <?php endif ?>
    </p>

    <!-- ===== CLIENTES ===== -->
    <div class="section-title">Clientes com maior movimento</div>
    <?php if (!empty($topClientes)): ?>
    <table class="tbl">
      <thead>
        <tr>
          <th>Cliente</th>
          <th class="c">Viaturas</th>
          <th class="c">Serviços</th>
          <th class="r">Total (Kz)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topClientes as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['nome']) ?></td>
          <td class="c"><?= (int)$c['viaturas'] ?></td>
          <td class="c"><?= (int)$c['servicos'] ?></td>
          <td class="r"><?= number_format((float)$c['total'],0,'.','.') ?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <?php else: ?>
      <p class="empty">Sem serviços concluídos registados neste mês.</p>
    <?php endif ?>

    <!-- ===== MECÂNICOS ===== -->
    <?php if (!empty($mecanicos)): ?>
    <div class="section-title">Desempenho dos mecânicos</div>
    <table class="tbl">
      <thead>
        <tr>
          <th>Mecânico</th>
          <th class="c">Serviços</th>
          <th class="r">Receita gerada (Kz)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mecanicos as $m): ?>
        <tr>
          <td><?= htmlspecialchars($m['mecanico']) ?></td>
          <td class="c"><?= (int)$m['servicos'] ?></td>
          <td class="r"><?= number_format((float)$m['total'],0,'.','.') ?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <?php endif ?>

    <!-- ===== PEÇAS USADAS ===== -->
    <?php if (!empty($pecasUsadas)): ?>
    <div class="section-title">Peças e materiais mais usados no mês</div>
    <table class="tbl">
      <thead>
        <tr>
          <th>Peça / Material</th>
          <th class="c">Qtd. usada</th>
          <th class="c">Nº de serviços</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pecasUsadas as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['peca']) ?></td>
          <td class="c"><?= (int)$p['qtd_usada'] ?></td>
          <td class="c"><?= (int)$p['em_servicos'] ?></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <?php endif ?>

    <!-- ===== ESTOQUE ===== -->
    <div class="section-title">Estado do estoque</div>
    <p class="para">
      <?php if (!empty($stockRows)): ?>
        O estoque regista <span class="hl"><?= $totalItens ?> referência(s)</span>.
        <?php if ($semStock > 0): ?>
          <span class="neg"><?= $semStock ?> item(ns) esgotado(s)</span> reposição urgente necessária.
        <?php endif ?>
        <?php if ($stockBaixo > 0): ?>
          <span class="warn"><?= $stockBaixo ?> item(ns) com quantidade baixa</span> ( menor ou igual a 3 unidades).
        <?php endif ?>
        <?php if ($semStock === 0 && $stockBaixo === 0): ?>
          Todos os materiais e peças apresentam níveis adequados.
        <?php endif ?>
      <?php else: ?>
        Sem itens registados no estoque.
      <?php endif ?>
    </p>
    <?php if (!empty($stockRows)): ?>
    <table class="tbl">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Tipo</th>
          <th>Marca</th>
          <th class="r">Qtd.</th>
          <th class="r">Preço (Kz)</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($stockRows as $s):
          $badgeClass = ['ok'=>'badge-ok','low'=>'badge-low','out'=>'badge-out'][$s['estado']] ?? 'badge-ok';
          $badgeLabel = ['ok'=>'OK','low'=>'Baixo','out'=>'Esgotado'][$s['estado']] ?? 'OK';
        ?>
        <tr>
          <td><?= htmlspecialchars($s['peca']) ?></td>
          <td><span class="badge badge-tipo"><?= htmlspecialchars($s['tipo']) ?></span></td>
          <td><?= htmlspecialchars($s['marca'] ?? '—') ?></td>
          <td class="r"><?= (int)$s['quantidade'] ?></td>
          <td class="r"><?= number_format((float)$s['preco'],0,'.','.') ?></td>
          <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
        </tr>
        <?php endforeach ?>
      </tbody>
    </table>
    <?php endif ?>

    <!-- ===== NOTAS ===== -->
    <div class="section-title">Notas finais</div>
    <p class="para">
      Este relatório foi gerado  pelo sistema  da Oficina D.K  com base nos registos do período de
      <span class="hl"><?= $nomeMes . ' de ' . $ano_atual ?></span>.
      Para detalhes adicionais consulte os módulos de serviços, contratos, estoque e movimentos financeiros.
    </p>

    <div class="modal-footer">
     <a href="estatistica.php"><button class="btn-fechar" >Fechar</button></a> 
      <button class="btn-imprimir" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 9V2h12v7"/><rect x="6" y="17" width="12" height="5" rx="1"/>
          <path d="M6 13H4a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h3"/><path d="M18 13h2a2 2 0 0 1 2 2v3a1 1 0 0 1-1 1h-3"/>
          <circle cx="18" cy="15" r="0.5" fill="currentColor"/>
        </svg>
        Confirmar impressão
      </button>
    </div>

  </div>
</div>

</body>
</html>