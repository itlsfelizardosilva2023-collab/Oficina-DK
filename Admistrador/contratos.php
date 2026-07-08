<?php
   session_start();
include("./configuracao/conexao.php");




$pesquisa = "";

// ================= PESQUISA =================
if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){

    $pesquisa = mysqli_real_escape_string($conn, $_GET['pesquisa']);

    $sql = "
    SELECT 
        contratos.id_contrato,
        contratos.numero_transacao,
        pagamentos_contrato.status AS status_mes,
        contratos.data_fim,
        contratos.total_geral,
        clientes.nome

    FROM contratos

    INNER JOIN clientes
    ON contratos.id_cliente = clientes.id_cliente

    WHERE contratos.numero_transacao LIKE '%$pesquisa%'
    OR clientes.nome LIKE '%$pesquisa%'
    OR pagamentos_contrato.status AS status_mes LIKE '%$pesquisa%'
    OR contratos.data_fim LIKE '%$pesquisa%'

    ORDER BY contratos.id_contrato DESC
    ";

}else{

    $sql = "
    SELECT 
        contratos.id_contrato,
        contratos.numero_contrato,
        contratos.status,
        contratos.data_fim,
        contratos.total_geral,
        clientes.nome

    FROM contratos

    INNER JOIN clientes
    ON contratos.id_cliente = clientes.id_cliente

    ORDER BY contratos.id_contrato DESC
    ";
}

$res = $conn->query($sql);



// ═══════════════════════════════════════════
//   ELIMINAR CONTRATO (cascata)
// ═══════════════════════════════════════════
if (isset($_GET['excluir_id'])) {

    $id_contrato = intval($_GET['excluir_id']);

    if ($id_contrato > 0) {

        $conn->begin_transaction();

        try {
            // 1. apagar lançamentos financeiros ligados a este contrato
            $stmt = $conn->prepare("DELETE FROM capital_empresa WHERE id_contrato = ?");
            $stmt->bind_param("i", $id_contrato);
            $stmt->execute();

            // 2. apagar pagamentos mensais ligados
            $stmt = $conn->prepare("DELETE FROM pagamentos_contrato WHERE contrato_id = ?");
            $stmt->bind_param("i", $id_contrato);
            $stmt->execute();

            // 3. apagar veículos associados ao contrato (tabela junção)
            $stmt = $conn->prepare("DELETE FROM contrato_carros WHERE id_contrato = ?");
            $stmt->bind_param("i", $id_contrato);
            $stmt->execute();

            // 4. por fim, apagar o contrato
            $stmt = $conn->prepare("DELETE FROM contratos WHERE id_contrato = ?");
            $stmt->bind_param("i", $id_contrato);
            $stmt->execute();

            $conn->commit();

            header("Location: contratos.php?msg=eliminado");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            header("Location: contratos.php?erro=" . urlencode($e->getMessage()));
            exit;
        }
    }
}



##########################status
$mes_atual = (int)date('n');
$ano_atual = (int)date('Y');

// calcular mês anterior, com ajuste de ano se for Janeiro
$mes_anterior = $mes_atual - 1;
$ano_anterior = $ano_atual;
if ($mes_anterior < 1) {
    $mes_anterior = 12;
    $ano_anterior = $ano_atual - 1;
}

$sql_pag_mes = "SELECT contrato_id, status FROM pagamentos_contrato WHERE mes = ? AND ano = ?";
$stmt_pag = $conn->prepare($sql_pag_mes);
$stmt_pag->bind_param("ii", $mes_anterior, $ano_anterior);
$stmt_pag->execute();
$res_pag = $stmt_pag->get_result();

$pagamentos_mes = [];
while ($p = $res_pag->fetch_assoc()) {
    $pagamentos_mes[$p['contrato_id']] = $p['status'];
}

// Busca o status de pagamento do mês atual para cada contrato,
// direto da tabela pagamentos_contrato (fonte da verdade)
$mesAtual = date('n');
$anoAtual = date('Y');

$sqlPagamentosMes = "SELECT contrato_id, status 
                      FROM pagamentos_contrato 
                      WHERE mes = ? AND ano = ?";

$stmt = $conn->prepare($sqlPagamentosMes);
$stmt->bind_param("ii", $mesAtual, $anoAtual);
$stmt->execute();
$resultPag = $stmt->get_result();

$pagamentos_mes = [];
while ($p = $resultPag->fetch_assoc()) {
    $pagamentos_mes[$p['contrato_id']] = $p['status'];
}
$stmt->close();

?>



<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K<</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/contratos.css?v=1.0">
  </head>
   <body>
    
    <div class="container">
      <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
              <main>
                <h1 class="cina">PAINEL ADMINISTRATIVO</h1>
                 <div class="insights">
                    <div class="sales">
                        <span>
                          
                           <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                          </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3>CONTRATOS REALIZADOS  </h3>
                                <h1><p class="valor">
                                   <?php
                                $sql_ultimos_30 = "
                                    SELECT COUNT(*) AS total
                                    FROM contratos
                                    WHERE criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                ";
                                $res_ultimos_30 = $conn->query($sql_ultimos_30);
                                $total_ultimos_30 = $res_ultimos_30->fetch_assoc()['total'] ?? 0;
                                ?>

                                <h1><?= $total_ultimos_30 ?></h1>
                             <small class="text-muted">
                            Nos ultimos 30 dias 
                        </small>
                        </div>
                       
                    </div>
 <!--------------------------------------->
                           <div class="sales">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                            </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3> CONTRATOS PAGOS </h3>
                                
                                <h1>
                                     <p class="valor">
                                 <?php
                                $mesAtual = date('n');
                                $anoAtual = date('Y');

                                $sql_pagos = "
                                    SELECT COUNT(*) AS total
                                    FROM pagamentos_contrato
                                    WHERE mes = ? AND ano = ? AND status = 'pago'
                                ";
                                $stmt_pagos = $conn->prepare($sql_pagos);
                                $stmt_pagos->bind_param("ii", $mesAtual, $anoAtual);
                                $stmt_pagos->execute();
                                $total_pagos = $stmt_pagos->get_result()->fetch_assoc()['total'] ?? 0;
                                $stmt_pagos->close();
                                ?>

                            <h1><?= $total_pagos ?></h1>
                            </p>          
                                </h1>
                            
                        </div>
                        
                    </div> 
 <!--------------------------------------->
                        <div class="sales">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                            </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3> CONTRATOS EXPIRADOS  </h3>
                                <h1> <?php
                                $sql_expirados = "
                                    SELECT COUNT(*) AS total
                                    FROM contratos
                                    WHERE data_fim < CURDATE()
                                    AND data_fim != '0000-00-00'
                                ";
                                $res_expirados = $conn->query($sql_expirados);
                                $total_expirados = $res_expirados->fetch_assoc()['total'] ?? 0;
                                ?>

                                <h1><?= $total_expirados ?></h1></h1>
                          
                        </div>
                    </div> 
                    
            <!------------------end--------------------->
                 </div>
              
            <!------------------recentes-------------------->
            
            <div class="Recent-Order">
                <h2>Informações Dos contratos</h2>

                        <form method="GET" action="">
                <div class="search-container">
                   <input type="text"
       id="pesquisa"
       placeholder="Pesquisar contrato ou cliente..."
       class="searchInput">
                    <button type="submit" class="Pesquisar">
                        <h3>Pesquisar</h3>                     </button>
                </div>  
            </form>




                 <div class="btn-add">
                    <a href="registrar_contratos.php">
                        <button class="btn-add">
                           <h3>Adicionar</h3> 
                        </button>
                    </a>
                </div>

             <div class="table-container">

    <table class="modern-table">

        <thead>
            <tr>
                <th>Nº Contrato</th>
                <th>Cliente</th>
                <th>Data Fim</th>
                <th>Total</th> 
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody id="resultado">

            <?php while($contrato = $res->fetch_assoc()): ?>

            <tr>

                <td>
                    <span class="badge">Contrato Nº: <?= htmlspecialchars($contrato['numero_contrato'] ?? 'N/D') ?></span>
                </td>

                <td class="client">

                    <?= htmlspecialchars($contrato['nome']) ?>
                </td>
                  
             
         <td><?= date('d/m/Y', strtotime($contrato['data_fim'])) ?></td>

                <td class="price">
                    <?= number_format($contrato['total_geral'],2,",",".") ?> Akz
                </td>

                 <?php
                    $status_mes = $pagamentos_mes[$contrato['id_contrato']] ?? 'pendente';
                    $labels = [
                        'pago'      => 'Pago',
                        'pendente'  => 'Pendente',
                        'cancelado' => 'Cancelado'
                    ];
                    $classes = [
                        'pago'      => 'badge-pago',
                        'pendente'  => 'badge-pendente',
                        'cancelado' => 'badge-cancelado'
                    ];
                    ?>
                    <td>
                    <span class="badges <?= $classes[$status_mes] ?>">
                        <?= $labels[$status_mes] ?>
                    </span>
                    </td>

                <td class="actions">
                    <a class="btn view" href="fatura_contrato.php?id=<?= $contrato['id_contrato'] ?>">
             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
            </svg>
                    </a>
                <?php if ($_SESSION['nivel'] === "admin") { ?>
            <a class="btn delete"
            href="contratos.php?excluir_id=<?= $contrato['id_contrato'] ?>"
            onclick="return confirm('Eliminar contrato?')">
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                    <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                    <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                </svg>
            </a>
        <?php } ?>
                </td>

            </tr>

            <?php endwhile; ?>

        </tbody>

    </table>

</div>      

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

    <script src="../views/js/contratos.js"></script>
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
      document.getElementById("pesquisa").addEventListener("keyup", function(){

    let valor = this.value;

    let xhr = new XMLHttpRequest();

    xhr.open("GET", "buscar_contratos.php?pesquisa=" + valor, true);

    xhr.onload = function(){
        if(xhr.status === 200){
            document.getElementById("resultado").innerHTML = xhr.responseText;
        }
    };

    xhr.send();

});
            
</script>
 </body>
</html>