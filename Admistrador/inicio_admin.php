<?php
         session_start();
        include_once('./configuracao/config.php');
        include_once('./configuracao/conexao.php');
        include_once("./models/servico_model.php");  
        include_once("./models/stok_models.php");
        
        // TOTAL DE SERVIÇOS DOS ÚLTIMOS 30 DIAS
$sql_servicos = "
    SELECT COUNT(*) AS total
    FROM servicos
    WHERE data >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";

$result_servicos = mysqli_query($conn, $sql_servicos);
$dados_servicos = mysqli_fetch_assoc($result_servicos);

$total_servicos = $dados_servicos['total'];

$sql_total = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM carros"
);

if (!$sql_total) {
    die("Erro: " . mysqli_error($conn));
}

$totalCarros = mysqli_fetch_assoc($sql_total)['total'];


##total de carros com servicos finalizado
$sql = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT placa_carro) AS total
     FROM servicos
     WHERE status = 'Concluido'"
);

if (!$sql) {
    die("Erro: " . mysqli_error($conn));
}

$total_carros_concluidos = mysqli_fetch_assoc($sql)['total'];


## do grafico

$sql_finalizados = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT placa_carro) AS total
     FROM servicos
     WHERE status = 'Concluido'"
);

if (!$sql_finalizados) {
    die("Erro: " . mysqli_error($conn));
}

$finalizados = mysqli_fetch_assoc($sql_finalizados)['total'];

// Percentagem
$percent = ($totalCarros > 0)
    ? round(($finalizados / $totalCarros) * 100)
    : 0;
?>


<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="stylesheet" href="../views/css/inicio.css?v=1.0">
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
  </head>
   <body>
    
    <div class="container">
       <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
              <main>
                <h1 class="cina">PAINEL <?php if ($_SESSION['nivel'] === "admin") { ?>ADMINISTRATIVO <?php } ?></h1>

                 <div class="insights">
                    <div class="sales">
                        <span class="card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
                             <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                             <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                           </svg>
                        </span>
                               <!------------gráfico----------------------->
                             

                        <div class="midlle">
                            
                               <h3>Total de Serviços</h3>
                                 <h1><?= $total_servicos ?></h1>
                            
                            
                        </div>
                        <small class="text-muted">
                            Nos ultimos 30 dias 
                        </small>
                    </div>
 <!--------------------------------------->
                           <div class="sales">
                        <span class="card">
                         <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                          <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                        </svg>
                        </span>
                             <!------------gráfico----------------------->
                       <div class="progress-circle small">
                                <svg width="60" height="60">
                                    <circle cx="30" cy="30" r="24" class="bg"></circle>
                                    <circle cx="30" cy="30" r="24" class="progress" id="progress-carros"></circle>
                                </svg>

                                <div class="number">
                                    <?php echo $percent; ?>%
                                </div>
                            </div>

                        <div class="midlle">
                            
                                <h3> Automóveis Finalizados</h3>
                                    <h1>  <?php echo $total_carros_concluidos; ?></h1>
                             
                        </div>
                        <small class="text-muted">
                            Nos ultimos 7 dias 
                        </small>
                    </div> 
 <!--------------------------------------->
                        <div class="sales">
                     <span class="card">
                           <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-stack" viewBox="0 0 16 16">
                            <path d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z"/>
                            <path d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z"/>
                            </svg>
                     </span>
                              <!------------gráfico----------------------->
                             <div class="progress-circle small">
                            
                            <div class="number" id="percent-carros"></div>
                        </div>
                        <div class="midlle">
                            
                                 <h3>Peças e Materias</h3>
                                 <a href="stok.php"><h1>Stock</h1></a>
                                
                     
                        </div>
                 <?php if(count($alertas) > 0){ ?>

    <small class="alerta-stock">
        ⚠ Atenção
    </small>

<?php } else { ?>

    <small class="text-muted">
        Stock estável
    </small>

<?php } ?>
                    </div> 

                     <!-----------------AVISO DE MANUTENCAO--------------------->
                       
            <!------------------end--------------------->
                 </div>
                  <!------------------assistente-------------------->
                                      <div id="assistente">
                                            <button onclick="abrirChat()" class="btn-assistente">
                                                <div class="circulo-pulse">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-chat-dots-fill" viewBox="0 0 16 16">
                                                    <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M5 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0m4 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                                                    </svg>
                                                </div>
                                            </button>
                                        </div>




                                        
                 <!------------------recentes-------------------->

           

            <div class="Recent-Order">
                <h2>Serviços Recentes</h2>

                
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Viatura</th>
            <th>Status</th>
            <th>Data</th>
            <th>Mais</th>
        </tr>
    </thead>

    <tbody>
    <?php
    $sql_recentes = "SELECT * FROM servicos ORDER BY data_registo DESC LIMIT 5";
    $res_recentes = mysqli_query($conn, $sql_recentes);

    while($s = mysqli_fetch_assoc($res_recentes)):
    ?>
    <tr>
        <td><?= htmlspecialchars($s['nome_cliente']) ?></td>
        <td><?= htmlspecialchars($s['placa_carro']) ?></td>

        <td>
            <span class="status <?= strtolower($s['status']) ?>">
                <?= htmlspecialchars($s['status']) ?>
            </span>
        </td>

        <td><?= date('d/m/Y', strtotime($s['data_registo'])) ?></td>

        <td>
            <a href="factura.php?id=<?= $s['id'] ?>">
                Detalhes
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
              
              
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
                  
                  <!-- SINETA -->
<div class="bell-wrap" id="bellWrap">
    <span class="bell-trigger" id="bellBtn">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
        </svg>
    </span>

    <!-- PAINEL DESLIZANTE -->
    <div class="notif-panel" id="notifPanel">

        <!-- HEADER -->
        <div class="notif-panel-header">
            <span>Notificações</span>
            <div class="header-acoes">
                <button class="btn-limpar-tudo" id="limparTudo" title="Limpar tudo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1zm-5 4h11l-.5 8.5A2 2 0 0 1 10 15H6a2 2 0 0 1-2-1.5L3.5 5z"/>
                    </svg>
                    Limpar tudo
                </button>
                <button class="fechar-notif" id="fecharNotif" title="Fechar painel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- TABS -->
        <div class="ntabs">
            <button class="ntab active" data-tab="carros">🚗 Carros prontos</button>
            <button class="ntab" data-tab="alertas">🔔 Alertas</button>
        </div>

        <!-- ABA CARROS PRONTOS -->
        <div class="nsec active" id="nsec-carros">
            <?php include_once('enviar_lembretes_manutencao.php');?>
        </div>

        <!-- ABA ALERTAS -->
        <div class="nsec" id="nsec-alertas">

            <div class="nitem" id="notif-4">
                <div class="nicon nicon-warn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                    </svg>
                </div>
                <div class="nbody">
                    <p><strong>Carlos Mendes</strong></p>
                    <p class="nsub">Toyota Hilux · revisão em 1 mês</p>
                    <small>há 2 dias</small>
                </div>
                <span class="kmpill kmpill-warn">9.000 km</span>
                <button class="btn-apagar-notif" onclick="apagarNotif('notif-4')" title="Apagar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

            <div class="nitem" id="notif-5">
                <div class="nicon nicon-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                    </svg>
                </div>
                <div class="nbody">
                    <p><strong>Paulo Neto</strong></p>
                    <p class="nsub">Nissan Navara · revisão urgente</p>
                    <small>há 1 dia</small>
                </div>
                <span class="kmpill kmpill-danger">300 km</span>
                <button class="btn-apagar-notif" onclick="apagarNotif('notif-5')" title="Apagar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</div>
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
                          <h3>SAIR</h3>
                       </a>
                     </div>
                        <div class="info">
                            <p>Olá!
                                 <b>
                                    <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                                 </b>
                             </p>
                            <small class="text-muted">
                               <?= htmlspecialchars($_SESSION['nivel']) ?>
                           </small>
                        </div>
                     </div>  
                    
              
        </div>
           <!------------------loanding--------------------->
        
               
         <!------------------assistente--------------------->
    <!---------------------------------------------------------------------------------------------------------------->
    <!---------------------------------------------------------------------------------------------------------------->
    <!---------------------------------------------------------------------------------------------------------------->

 
            </div>
            <div id="chat-overlay" style="display:none; position:fixed; inset:0; z-index:999; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
            <div style="position:relative;">
                <button onclick="fecharChat()" style="position:absolute; top:-14px; right:-14px; z-index:10; width:32px; height:32px; border-radius:50%; background:#222; border:1px solid #444; color:#fff; font-size:18px; cursor:pointer;">×</button>
                <div class="chat-wrapper">
                    <div class="chat-wrapper">
                    <!-- Header -->
                    <div class="chat-header">
                        <div class="bot-avatar"> DK</div>
                        <div class="header-info">
                        <h1>Oficina DK</h1>
                        <p>Assistente Virtual • Suporte ao Sistema</p>
                        </div>
                        <div class="online-dot"></div>
                    </div>
                    
                    <!-- Messages -->
                    <div class="chat-body" id="chatBody"></div>
                    
                    <!-- Quick replies -->
                    <div class="quick-replies" id="quickReplies">
                        <button class="quick-btn" onclick="sendQuick('criar conta?')"> como criar conta?</button>
                        <button class="quick-btn" onclick="sendQuick('senha esquecida?')"> esqueci a senha?</button>
                        <button class="quick-btn" onclick="sendQuick('imprimir relatório')">imprimir relatório?</button>
                        <button class="quick-btn" onclick="sendQuick('remover registo?')">remover registo?</button>
                    </div>
            <!-- Input -->
            <div class="chat-input">
                <input type="text" id="userInput" placeholder="Digite sua dúvida..." onkeydown="handleKey(event)" />
                <button class="send-btn" onclick="sendMessage()">
                <svg viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2z"/></svg>
                </button>
            </div>
            </div>
                </div>
            </div>
            </div>

            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
            <!---------------------------------------------------------------------------------------------------------------->
                            <!------------------assistente--------------------->
                <script src="../views/js/inicio.js"></script>
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
                                                const percent = <?php echo $percent; ?>;

                            document.getElementById("percent-carros").innerText = percent + "%";

                            const circle = document.getElementById("progress-carros");
                            const radius = 24;
                            const circumference = 2 * Math.PI * radius;

                            circle.style.strokeDasharray = circumference;

                            const offset = circumference - (percent / 100) * circumference;

                            circle.style.strokeDashoffset = offset;
                                                        
//======================================================================================
                            const bellBtn     = document.getElementById('bellBtn');
const notifPanel  = document.getElementById('notifPanel');
const fecharNotif = document.getElementById('fecharNotif');
const limparTudo  = document.getElementById('limparTudo');
const nbadge      = document.getElementById('nbadge');

/* abrir painel */
bellBtn.addEventListener('click', () => {
    notifPanel.classList.add('open');
});

/* fechar pelo X do header */
fecharNotif.addEventListener('click', () => {
    notifPanel.classList.remove('open');
});

/* fechar ao clicar fora */
document.addEventListener('click', (e) => {
    if (!notifPanel.contains(e.target) && !bellBtn.contains(e.target)) {
        notifPanel.classList.remove('open');
    }
});

/* apagar notificação individual */
function apagarNotif(id) {
    const item = document.getElementById(id);
    if (!item) return;
    item.classList.add('saindo');
    setTimeout(() => {
        item.remove();
        atualizarBadge();
    }, 300);
}

/* limpar tudo da aba activa em cascata */
limparTudo.addEventListener('click', () => {
    const visiveis = document.querySelectorAll('.nsec.active .nitem');
    visiveis.forEach((item, i) => {
        setTimeout(() => {
            item.classList.add('saindo');
            setTimeout(() => { item.remove(); atualizarBadge(); }, 300);
        }, i * 60);
    });
});

/* atualizar badge */
function atualizarBadge() {
    const total = document.querySelectorAll('.nitem').length;
    nbadge.textContent = total;
    nbadge.style.display = total === 0 ? 'none' : 'flex';
}

/* tabs */
document.querySelectorAll('.ntab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.ntab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.nsec').forEach(s => s.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('nsec-' + tab.dataset.tab).classList.add('active');
    });
});


            </script>
            </body>
</html>