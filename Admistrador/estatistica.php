
<?php

session_start();



if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != "admin") {
    header("Location: login.php");
    exit();
}

include("./configuracao/conexao.php");


// ================= CAPITAL TOTAL REAL =================
$sql_capital = "
SELECT 
(
    -- SOMA DOS SERVIÇOS CONCLUÍDOS
    (SELECT COALESCE(SUM(total),0) FROM servicos WHERE status = 'Concluido')
    +
    -- SOMA DOS PAGAMENTOS DE CONTRATO (mensais, já efectivamente pagos)
    (SELECT COALESCE(SUM(valor),0) FROM pagamentos_contrato WHERE status = 'pago')
    +
    -- ENTRADAS MANUAIS
    (SELECT COALESCE(SUM(valor),0) FROM capital_empresa WHERE fluxo = 'entrada')
    -
    -- SAÍDAS DA EMPRESA
    (SELECT COALESCE(SUM(valor),0) FROM capital_empresa WHERE fluxo = 'saida')
) AS capital_total
";

$res_capital = mysqli_query($conn, $sql_capital);
if (!$res_capital) {
    die("Erro capital: " . mysqli_error($conn));
}

$capital_total = mysqli_fetch_assoc($res_capital)['capital_total'];


// ================= SERVIÇOS =================
function countStatus($conn, $status){
    $q = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM servicos
        WHERE status = '$status'
    ");

    if(!$q){
        die("Erro servicos ($status): " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($q)['total'];
}

$servicos_concluidos = countStatus($conn,'concluido');
$servicos_pendentes  = countStatus($conn,'pendente');
$servicos_andamento  = countStatus($conn,'andamento');
$servicos_cancelados = countStatus($conn,'cancelado');


// ================= CLIENTES =================
$q_clientes = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM clientes
");

if(!$q_clientes){
    die("Erro clientes: " . mysqli_error($conn));
}

$total_clientes = mysqli_fetch_assoc($q_clientes)['total'];


// ================= ESTOQUE =================
$q_estoque = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM estoque
    WHERE quantidade <= 5
");

if(!$q_estoque){
    die("Erro estoque: " . mysqli_error($conn));
}

$alerta_estoque = mysqli_fetch_assoc($q_estoque)['total'];



// ================= EVOLUÇÃO FINANCEIRA =================
$sqlGrafico = mysqli_query($conn, "
    SELECT 
        mes,
        SUM(valor) AS total
    FROM (
        SELECT MONTH(data_registo) AS mes, total AS valor
        FROM servicos
        WHERE YEAR(data_registo) = YEAR(CURDATE())

        UNION ALL

        SELECT MONTH(criado_em) AS mes, total_geral AS valor
        FROM contratos
        WHERE YEAR(criado_em) = YEAR(CURDATE())
    ) AS movimentos
    GROUP BY mes
    ORDER BY mes
");

if (!$sqlGrafico) {
    die("Erro gráfico: " . mysqli_error($conn));
}

$nomesMeses = [
    1=>"Jan", 2=>"Fev", 3=>"Mar", 4=>"Abr",
    5=>"Mai", 6=>"Jun", 7=>"Jul", 8=>"Ago",
    9=>"Set", 10=>"Out", 11=>"Nov", 12=>"Dez"
];

// 12 meses sempre a zero
$dadosMeses = array_fill(1, 12, 0);

while ($graf = mysqli_fetch_assoc($sqlGrafico)) {
    $dadosMeses[(int)$graf['mes']] = (float)$graf['total'];
}

// Arrays finais sempre com 12 pontos
$meses   = array_values($nomesMeses);
$valores = array_values($dadosMeses);


// ═══════════════════════════════════════════════════
//  QUERIES – Totais por mês (ano actual)
// ═══════════════════════════════════════════════════
 
$nomesMeses = [
    1=>"Jan",2=>"Fev",3=>"Mar",4=>"Abr",
    5=>"Mai",6=>"Jun",7=>"Jul",8=>"Ago",
    9=>"Set",10=>"Out",11=>"Nov",12=>"Dez"
];
 
// Inicializa os 12 meses a zero
$dadosServicos  = array_fill(1, 12, 0);
$dadosContratos = array_fill(1, 12, 0);
$dadosClientes  = array_fill(1, 12, 0);
$dadosCarros    = array_fill(1, 12, 0);
 
// Serviços por mês
$q = mysqli_query($conn, "
    SELECT MONTH(data_registo) AS mes, COUNT(*) AS total
    FROM servicos
    WHERE YEAR(data_registo) = YEAR(CURDATE())
    GROUP BY mes
");
while ($r = mysqli_fetch_assoc($q)) $dadosServicos[(int)$r['mes']] = (int)$r['total'];
 
// Contratos por mês
$q = mysqli_query($conn, "
    SELECT MONTH(criado_em) AS mes, COUNT(*) AS total
    FROM contratos
    WHERE YEAR(criado_em) = YEAR(CURDATE())
    GROUP BY mes
");
while ($r = mysqli_fetch_assoc($q)) $dadosContratos[(int)$r['mes']] = (int)$r['total'];
 
// Clientes por mês
$q = mysqli_query($conn, "
    SELECT MONTH(criado_em) AS mes, COUNT(*) AS total
    FROM clientes
    WHERE YEAR(criado_em) = YEAR(CURDATE())
    GROUP BY mes
");
while ($r = mysqli_fetch_assoc($q)) $dadosClientes[(int)$r['mes']] = (int)$r['total'];
 
// Carros por mês
$q = mysqli_query($conn, "
    SELECT MONTH(criado_em) AS mes, COUNT(*) AS total
    FROM carros
    WHERE YEAR(criado_em) = YEAR(CURDATE())
    GROUP BY mes
");
while ($r = mysqli_fetch_assoc($q)) $dadosCarros[(int)$r['mes']] = (int)$r['total'];
 
$mesesLabels     = json_encode(array_values($nomesMeses));
$jsServicos      = json_encode(array_values($dadosServicos));
$jsContratos     = json_encode(array_values($dadosContratos));
$jsClientes      = json_encode(array_values($dadosClientes));
$jsCarros        = json_encode(array_values($dadosCarros));

#### graficos de circolo ##################
$qStatus = mysqli_query($conn, "
    SELECT status, COUNT(*) AS total
    FROM servicos
    GROUP BY status
");
 
$statusMap = [
    'concluido' => 0,
    'pendente'  => 0,
    'andamento' => 0,
    'cancelado' => 0
];
 
while ($r = mysqli_fetch_assoc($qStatus)) {
    if (isset($statusMap[$r['status']])) {
        $statusMap[$r['status']] = (int)$r['total'];
    }
}
 
$totalServicos = array_sum($statusMap);

// ═══════════════════════════════════════════
//   Entradas e Saídas por mês
// ═══════════════════════════════════════════

$anoAtual = (int) date('Y');

$qEntradas = mysqli_query($conn, "
    SELECT mes, SUM(total) AS total
    FROM (
        SELECT pc.mes AS mes, pc.valor AS total
        FROM pagamentos_contrato pc
        WHERE pc.status = 'pago'

        UNION ALL

        SELECT MONTH(data_registo) AS mes, total AS total
        FROM servicos
        WHERE status = 'Concluido'

        UNION ALL

        SELECT MONTH(data_registro) AS mes, valor AS total
        FROM capital_empresa
        WHERE fluxo = 'entrada'
    ) AS receitas
    GROUP BY mes
    ORDER BY mes
");

$qSaidas = mysqli_query($conn, "
    SELECT MONTH(data_registro) AS mes, SUM(valor) AS total
    FROM capital_empresa
    WHERE fluxo = 'saida'
    GROUP BY mes
    ORDER BY mes
");

// Saídas = capital_empresa com fluxo = saida (apenas ano atual)
$qSaidas = mysqli_query($conn, "
    SELECT MONTH(data_registro) AS mes, SUM(valor) AS total
    FROM capital_empresa
    WHERE fluxo = 'saida'
      AND YEAR(data_registro) = $anoAtual
    GROUP BY mes
    ORDER BY mes
");

$entradas = array_fill(1, 12, 0);
$saidas   = array_fill(1, 12, 0);

while ($r = mysqli_fetch_assoc($qEntradas)) $entradas[(int)$r['mes']] = (float)$r['total'];
while ($r = mysqli_fetch_assoc($qSaidas))   $saidas[(int)$r['mes']]   = (float)$r['total'];

$jsEntradas = json_encode(array_values($entradas));
$jsSaidas   = json_encode(array_values($saidas));

$totalEntradas = array_sum($entradas);
$totalSaidas   = array_sum($saidas);
$saldo         = $totalEntradas - $totalSaidas;

//  QUERY – Produtos mais vendidos (via servico_itens + factura_itens)
// ═══════════════════════════════════════════
$qVendidos = mysqli_query($conn, "
    SELECT 
        nome,
        total_vendido
    FROM (
        SELECT 
            e.nome,
            SUM(si.quantidade) AS total_vendido
        FROM servico_itens si
        JOIN estoque e ON e.codigo = si.codigo_da_peca
        GROUP BY e.id_estoque, e.nome
 
        UNION ALL
 
        SELECT 
            e.nome,
            SUM(fi.quantidade) AS total_vendido
        FROM factura_itens fi
        JOIN estoque e ON e.codigo = fi.codigo_da_peca
        GROUP BY e.id_estoque, e.nome
    ) AS vendas
    GROUP BY nome
    ORDER BY total_vendido DESC
    LIMIT 8
");
 
$nomes    = [];
$qtds     = [];
$estoques = [];
 
while ($r = mysqli_fetch_assoc($qVendidos)) {
    $nomes[] = $r['nome'];
    $qtds[]  = (int)$r['total_vendido'];
}




 
// Stock actual de cada produto
foreach ($nomes as $nome) {
    $n = mysqli_real_escape_string($conn, $nome);
    $q = mysqli_query($conn, "SELECT SUM(quantidade) AS qty FROM estoque WHERE nome = '$n'");
    $row = mysqli_fetch_assoc($q);
    $estoques[] = (int)($row['qty'] ?? 0);
}
 
$jsNomes    = json_encode($nomes);
$jsQtds     = json_encode($qtds);
$jsEstoques = json_encode($estoques);
$totalProdutos = count($nomes);

##alwerta de stok 
$qStockBaixo = mysqli_query($conn, "
    SELECT nome, quantidade
    FROM estoque
    WHERE quantidade <= 5
    ORDER BY quantidade ASC
    LIMIT 3
");
$produtosBaixos = [];
while ($r = mysqli_fetch_assoc($qStockBaixo)) $produtosBaixos[] = $r;
?>
 
<?php if (!empty($produtosBaixos)): ?>







<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="stylesheet" href="../views/css/estatistica.css">
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script src="..\views\js\chart.umd.js"></script>


  </head>
   <body>
    
    <div class="container">
           <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
            <main class="maine">
                <h1 class="cina">Dashboard Estatístico</h1>
       
               

        <div class="topbare">

            <div>
           
                <div class="subtitle">Visão geral da empresa</div>
            </div>
                        <a href="relatorio_mensal.php" class="btn-print">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V2h12v7"/><rect x="6" y="17" width="12" height="5" rx="1"/>
                <path d="M6 13H4a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h3"/><path d="M18 13h2a2 2 0 0 1 2 2v3a1 1 0 0 1-1 1h-3"/>
                <circle cx="18" cy="15" r="0.5" fill="currentColor"/>
            </svg>
            Imprimir relatório do mês
            </a>

        </div>

        <div class="kpi-gride">

            <div class="kpi">
            
                <div class="fluxo-header">
    <p class="fluxo-titulo">Entradas &amp; Saídas mensais</p>
    <div class="fluxo-resumo">
        <span class="fluxo-tag entrada">▲ <?= number_format($totalEntradas, 0, ',', '.') ?> Kz</span>
        <span class="fluxo-tag saida">▼ <?= number_format($totalSaidas, 0, ',', '.') ?> Kz</span>
       <span class="fluxo-tag saldo"><?= $saldo >= 0 ? '+' : '−' ?> <?= number_format(abs($saldo), 0, ',', '.') ?> Kz</span>
    </div>
</div>
 
<div class="fluxo-legenda">
    <span class="fluxo-leg"><span class="fluxo-leg-dot" style="background:#4ade80;"></span>Entradas</span>
    <span class="fluxo-leg"><span class="fluxo-leg-dot" style="background:#f87171;"></span>Saídas</span>
</div>
 
<div class="fluxo-wrap">
    <canvas id="graficoCapitalFluxo" aria-label="Gráfico de barras com entradas e saídas mensais"></canvas>
</div>
               
            </div>

            <div class="kpi">
               <div class="donut-wrap">
    <p class="donut-titulo">Estado dos Serviços</p>
 
    <div class="donut-canvas-wrap">
        <canvas id="graficoDonut" aria-label="Gráfico donut com estado dos serviços"></canvas>
        <div class="donut-centro">
            <div class="donut-centro-num"><?= $totalServicos ?></div>
            <div class="donut-centro-label">Total</div>
        </div>
    </div>
 
        <div class="donut-legenda">
                <div class="donut-leg-item">
                    <div class="donut-leg-esq">
                        <span class="donut-leg-dot" style="background:#22c55e;"></span>
                        <span>Concluídos</span>
                    </div>
                    <span class="donut-leg-num"><?= $statusMap['concluido'] ?></span>
                </div>
                <div class="donut-leg-item">
                    <div class="donut-leg-esq">
                        <span class="donut-leg-dot" style="background:#f59e0b;"></span>
                        <span>Pendentes</span>
                    </div>
                    <span class="donut-leg-num"><?= $statusMap['pendente'] ?></span>
                </div>
                <div class="donut-leg-item">
                    <div class="donut-leg-esq">
                        <span class="donut-leg-dot" style="background:#3b82f6;"></span>
                        <span>Andamento</span>
                    </div>
                    <span class="donut-leg-num"><?= $statusMap['andamento'] ?></span>
                </div>
                <div class="donut-leg-item">
                    <div class="donut-leg-esq">
                        <span class="donut-leg-dot" style="background:#ef4444;"></span>
                        <span>Cancelados</span>
                    </div>
                    <span class="donut-leg-num"><?= $statusMap['cancelado'] ?></span>
                </div>
            </div>
        </div>
            </div>

            
            <div class="kpi">
                    <div class="hbar-header">
                <p class="hbar-titulo">Produtos mais vendidos</p>
                <span class="hbar-badge">Top <?= $totalProdutos ?> produtos</span>
            </div>
            
            <div class="hbar-legenda">
                <span class="hbar-leg"><span class="hbar-leg-dot" style="background:#a78bfa;"></span>Unid. vendidas</span>
                <span class="hbar-leg"><span class="hbar-leg-dot" style="background:#38bdf8;"></span>Stock actual</span>
            </div>
            
            <div class="hbar-wrap">
                <canvas id="graficoProdutosVendidos" aria-label="Gráfico de barras horizontal com produtos mais vendidos"></canvas>
            </div>
            <?php foreach ($produtosBaixos as $p): ?>
            <div class="alerta-linha">
                <span><?= htmlspecialchars($p['nome']) ?></span>
                <span><?= $p['quantidade'] ?> un.</span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            </div>


        </div>

            <div class="chart-card">
            <div class="chart-top">
                <span class="chart-top-title">Receita mensal (Kz)</span>
                <div class="chart-legend">
                <span><span class="chart-legend-dot" style="background:#3b82f6;"></span>Receita</span>
                <span><span class="chart-legend-dot" style="background:#22c55e;"></span>Média</span>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas id="grafico" role="img" aria-label="Gráfico de linha com evolução de receita mensal"></canvas>
            </div>

            
            </div>

            
               
                
                        <div class="gb-header">
                <p class="gb-titulo">Actividade mensal por categoria</p>
                <div class="gb-filtros">
                    <button class="gb-btn s-activo"  onclick="gbFiltro('servicos',  this)">Serviços</button>
                    <button class="gb-btn"           onclick="gbFiltro('contratos', this)">Contratos</button>
                    <button class="gb-btn"           onclick="gbFiltro('clientes',  this)">Clientes</button>
                    <button class="gb-btn"           onclick="gbFiltro('carros',    this)">Carros</button>
                    <button class="gb-btn"           onclick="gbFiltro('todos',     this)">Todos</button>
                </div>
            </div>
            
            <div class="gb-legenda" id="gb-legenda">
                <span class="gb-leg-item"><span class="gb-leg-dot" style="background:#3b82f6;"></span>Serviços</span>
            </div>
            
            <div class="gb-wrap">
                <canvas id="graficoBarras" role="img" aria-label="Gráfico de barras com actividade mensal da Oficina D.K"></canvas>
            </div>
            
              
               <div class="gb-header">
                
          
        </div>
       
        <div class="gb-header">
                
          
        </div>

    </main>





 



                
         
               <!------------------end--------------------->

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
                   
                      <!------------------end--------------------->
                       
                       </div>
              
        </div>
                          


        
                <script src="../views/js/saldo.js"></script>
                <script id="fade-js">

                 
                const _meses   = <?= json_encode($meses) ?>;
                const _valores = <?= json_encode($valores) ?>;
                const _media   = <?= round(array_sum($valores) / count($valores)) ?>;
                const _mediaLine = new Array(_meses.length).fill(_media);

                new Chart(document.getElementById('grafico'), {
                type: 'line',
                data: {
                    labels: _meses,
                    datasets: [
                    {
                        label: 'Receita',
                        data: _valores,
                        borderColor: '#3b82f6',
                        backgroundColor: function(ctx) {
                        const { ctx: c, chartArea } = ctx.chart;
                        if (!chartArea) return 'transparent';
                        const g = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        g.addColorStop(0,   'rgba(59,130,246,.35)');
                        g.addColorStop(0.6, 'rgba(59,130,246,.08)');
                        g.addColorStop(1,   'rgba(59,130,246,0)');
                        return g;
                        },
                        fill: true,
                        tension: .45,
                        borderWidth: 2.5,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#0d1117',
                        pointBorderWidth: 2,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#3b82f6',
                        pointHoverBorderWidth: 2
                    },
                    {
                        label: 'Média',
                        data: _mediaLine,
                        borderColor: 'rgba(34,197,94,.5)',
                        borderWidth: 1.5,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        fill: false
                    }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1c2128',
                        borderColor: '#30363d',
                        borderWidth: 1,
                        titleColor: '#c9d9cc',
                        bodyColor: '#7d8590',
                        padding: 14,
                        callbacks: {
                        label: ctx => {
                            const label = ctx.dataset.label === 'Média' ? 'Média' : 'Receita';
                            return '  ' + label + ': ' + ctx.parsed.y.toLocaleString('pt-AO') + ' Kz';
                        }
                        }
                    }
                    },
                    scales: {
                    x: {
                        grid: { color: 'rgba(143, 142, 142, 0.14)', drawTicks: false },
                        ticks: { color: '#1f61be', font: { size: 12 }, padding: 8 },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: 'rgba(143, 142, 142, 0.14)', drawTicks: false },
                        ticks: {
                        color: '#7d8590',
                        font: { size: 11 },
                        padding: 10,
                        callback: v => (v / 1000).toFixed(0) + 'K'
                        },
                        border: { display: false }
                    }
                    }
                }
                });
           // grafico de barra
                (function(){
                    const meses = <?= $mesesLabels ?>;
                
                    const series = {
                        servicos:  { label:'Serviços',   data: <?= $jsServicos ?>,  color:'#3b82f6' },
                        contratos: { label:'Contratos',  data: <?= $jsContratos ?>, color:'#fbbf24' },
                        clientes:  { label:'Clientes',   data: <?= $jsClientes ?>,  color:'#34d399' },
                        carros:    { label:'Carros',     data: <?= $jsCarros ?>,    color:'#f472b6' }
                    };
                
                    const classeActivo = {
                        servicos:'s-activo', contratos:'c-activo', clientes:'cl-activo', carros:'cr-activo', todos:'s-activo'
                    };
                
                    function mkDataset(key) {
                        const s = series[key];
                        return [{
                            label: s.label,
                            data: s.data,
                            backgroundColor: s.color + '99',
                            borderColor: s.color,
                            borderWidth: 1.5,
                            borderRadius: 5,
                            borderSkipped: false
                        }];
                    }
                
                    function mkTodos() {
                        return Object.values(series).map(s => ({
                            label: s.label,
                            data: s.data,
                            backgroundColor: s.color + '99',
                            borderColor: s.color,
                            borderWidth: 1.5,
                            borderRadius: 5,
                            borderSkipped: false
                        }));
                    }
                
                    function actualizarLegenda(tipo) {
                        const leg = document.getElementById('gb-legenda');
                        const itens = tipo === 'todos'
                            ? Object.values(series)
                            : [series[tipo]];
                        leg.innerHTML = itens.map(s =>
                            `<span class="gb-leg-item"><span class="gb-leg-dot" style="background:${s.color};"></span>${s.label}</span>`
                        ).join('');
                    }
                
                    const ctx = document.getElementById('graficoBarras').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: { labels: meses, datasets: mkDataset('servicos') },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode:'index', intersect:false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1c2128',
                                    borderColor: '#30363d',
                                    borderWidth: 1,
                                    titleColor: '#c9d1d9',
                                    bodyColor: '#7d8590',
                                    padding: 12,
                                    callbacks: {
                                        label: c => '  ' + c.dataset.label + ': ' + c.parsed.y
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color:'rgba(143, 142, 142, 0.14)', drawTicks:false },
                                    ticks: { color:'#7d8590', font:{ size:11 }, padding:6 },
                                    border: { display:false }
                                },
                                y: {
                                    grid: { color:'rgba(143, 142, 142, 0.14)', drawTicks:false },
                                    ticks: {
                                        color:'#7d8590', font:{ size:11 }, padding:8,
                                        stepSize: 1,
                                        callback: v => Number.isInteger(v) ? v : ''
                                    },
                                    border: { display:false },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                
                    window.gbFiltro = function(tipo, btn) {
                        // Remove todas as classes activo
                        document.querySelectorAll('.gb-btn').forEach(b => {
                            b.classList.remove('s-activo','c-activo','cl-activo','cr-activo');
                        });
                        // Adiciona a classe certa
                        const cls = classeActivo[tipo] || 's-activo';
                        btn.classList.add(cls);
                
                        chart.data.datasets = tipo === 'todos' ? mkTodos() : mkDataset(tipo);
                        chart.update();
                        actualizarLegenda(tipo);
                    };
                })();

                 // grafico de circulo

                            (function(){
                const data = {
                    concluido: <?= $statusMap['concluido'] ?>,
                    pendente:  <?= $statusMap['pendente'] ?>,
                    andamento: <?= $statusMap['andamento'] ?>,
                    cancelado: <?= $statusMap['cancelado'] ?>
                };
            
                new Chart(document.getElementById('graficoDonut'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Concluídos','Pendentes','Andamento','Cancelados'],
                        datasets: [{
                            data: [data.concluido, data.pendente, data.andamento, data.cancelado],
                            backgroundColor: [
                                'rgba(34,197, 94,.85)',
                                'rgba(245,158, 11,.85)',
                                'rgba(59, 130,246,.85)',
                                'rgba(239, 68, 68,.85)'
                            ],
                            borderColor: [
                                '#22c55e','#f59e0b','#3b82f6','#ef4444'
                            ],
                            borderWidth: 1.5,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1c2128',
                                borderColor: '#30363d',
                                borderWidth: 1,
                                titleColor: '#c9d1d9',
                                bodyColor: '#7d8590',
                                padding: 12,
                                callbacks: {
                                    label: c => '  ' + c.label + ': ' + c.parsed
                                }
                            }
                        }
                    }
                });
            })();

            //grafico de barra da saidas e antradas 
                (function(){
                    const meses    = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                    const entradas = <?= $jsEntradas ?>;
                    const saidas   = <?= $jsSaidas ?>;
                
                    new Chart(document.getElementById('graficoCapitalFluxo'), {
                        type: 'bar',
                        data: {
                            labels: meses,
                            datasets: [
                                {
                                    label: 'Entradas',
                                    data: entradas,
                                    backgroundColor: 'rgba(74,222,128,.75)',
                                    borderColor: '#4ade80',
                                    borderWidth: 1.5,
                                    borderRadius: 5,
                                    borderSkipped: false
                                },
                                {
                                    label: 'Saídas',
                                    data: saidas,
                                    backgroundColor: 'rgba(248,113,113,.75)',
                                    borderColor: '#f87171',
                                    borderWidth: 1.5,
                                    borderRadius: 5,
                                    borderSkipped: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode:'index', intersect:false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1c2128',
                                    borderColor: '#30363d',
                                    borderWidth: 1,
                                    titleColor: '#c9d1d9',
                                    bodyColor: '#7d8590',
                                    padding: 12,
                                    callbacks: {
                                        label: c => '  ' + c.dataset.label + ': ' + c.parsed.y.toLocaleString('pt-AO') + ' Kz'
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color:'rgba(143, 142, 142, 0.14)', drawTicks:false },
                                    ticks: { color:'#7d8590', font:{ size:11 }, padding:6 },
                                    border: { display:false }
                                },
                                y: {
                                    grid: { color:'rgba(143, 142, 142, 0.14)', drawTicks:false },
                                    ticks: {
                                        color: '#7d8590',
                                        font: { size:11 },
                                        padding: 8,
                                        callback: v => (v/1000).toFixed(0) + 'K'
                                    },
                                    border: { display:false },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })();

                //grafico de barras invertida do stock
                            (function(){
                const nomes    = <?= $jsNomes ?>;
                const vendidos = <?= $jsQtds ?>;
                const estoque  = <?= $jsEstoques ?>;
            
                new Chart(document.getElementById('graficoProdutosVendidos'), {
                    type: 'bar',
                    data: {
                        labels: nomes,
                        datasets: [
                            {
                                label: 'Unid. vendidas',
                                data: vendidos,
                                backgroundColor: 'rgba(167,139,250,.75)',
                                borderColor: '#a78bfa',
                                borderWidth: 1.5,
                                borderRadius: 4,
                                borderSkipped: false
                            },
                            {
                                label: 'Stock actual',
                                data: estoque,
                                backgroundColor: 'rgba(56,189,248,.65)',
                                borderColor: '#38bdf8',
                                borderWidth: 1.5,
                                borderRadius: 4,
                                borderSkipped: false
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode:'index', intersect:false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1c2128',
                                borderColor: '#30363d',
                                borderWidth: 1,
                                titleColor: '#c9d1d9',
                                bodyColor: '#7d8590',
                                padding: 12,
                                callbacks: {
                                    label: c => '  ' + c.dataset.label + ': ' + c.parsed.x + ' un.'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color:'rgba(143, 142, 142, 0.14)', drawTicks:false },
                                ticks: {
                                    color: '#7d8590',
                                    font: { size:11 },
                                    padding: 6,
                                    stepSize: 1,
                                    callback: v => Number.isInteger(v) ? v : ''
                                },
                                border: { display:false },
                                beginAtZero: true
                            },
                            y: {
                                grid: { display:false },
                                ticks: { color:'#c9d1d9', font:{ size:12 }, padding:8 },
                                border: { display:false }
                            }
                        }
                    }
                });
            })();
            </script>
            </body>
</html>
