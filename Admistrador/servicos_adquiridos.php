
<?php
        session_start();
        include_once('./configuracao/config.php');
        include_once('./configuracao/conexao.php');
        require_once "./funcoes/elimina.php";
        include_once("./models/servico_model.php");     
        
        
?>


<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/servicos.css?v=1.0">
  </head>
   <body>
    
    <div class="container">
         <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
              <main>
                <h1 class="cina">PAINEL DO  TECNICO</h1>
                 <div class="insights">
                    <div class="sales">
                        <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
                          <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                          <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                        </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3> SERVIÇOS ADICIONADOS RECENTIMENTE</h3>
                              <?php
                            $nivel = $_SESSION['nivel'] ?? '';
                            $id_funcionario_logado = $_SESSION['id_funcionario'] ?? null;

                            $sql_servicos_recentes = "
                                SELECT COUNT(*) AS total 
                                FROM servicos 
                                WHERE data_registo >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                            ";

                            if ($nivel === 'tecnico') {
                                $sql_servicos_recentes .= " AND id_mecanico = ?";
                                $stmt = $conn->prepare($sql_servicos_recentes);
                                $stmt->bind_param("i", $id_funcionario_logado);
                                $stmt->execute();
                                $result_servicos_recentes = $stmt->get_result();
                            } else {
                                $result_servicos_recentes = mysqli_query($conn, $sql_servicos_recentes);
                            }

                            if (!$result_servicos_recentes) {
                                die("Erro na query: " . mysqli_error($conn));
                            }

                            $row_servicos_recentes = mysqli_fetch_assoc($result_servicos_recentes);
                            $total_servicos_recentes = $row_servicos_recentes['total'];
                            ?>

                            <p class="numero-dashboard"><h1><?= $total_servicos_recentes ?></h1></p>
                        </div>
                        <small class="text-muted">
                            Nos ultimos 30 dias 
                        </small>
                    </div>
 <!--------------------------------------->
                           <div class="sales">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
                            <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                         </svg>

                        </span>
                        <div class="midlle">
                            
                               <h3>SERVIÇOS CONCLUÍDOS </h3>

                                                <?php
                        $nivel = $_SESSION['nivel'] ?? '';
                        $id_funcionario_logado = $_SESSION['id_funcionario'] ?? null;

                        $sql_servicos_concluidos = "SELECT COUNT(*) AS total FROM servicos WHERE status = 'concluido'";

                        if ($nivel === 'tecnico') {
                            $sql_servicos_concluidos .= " AND id_mecanico = ?";
                            $stmt = $conn->prepare($sql_servicos_concluidos);
                            $stmt->bind_param("i", $id_funcionario_logado);
                            $stmt->execute();
                            $result_servicos_concluidos = $stmt->get_result();
                        } else {
                            $result_servicos_concluidos = mysqli_query($conn, $sql_servicos_concluidos);
                        }

                        if (!$result_servicos_concluidos) {
                            die("Erro na query: " . mysqli_error($conn));
                        }

                        $row_servicos_concluidos = mysqli_fetch_assoc($result_servicos_concluidos);
                        $total_servicos_concluidos = $row_servicos_concluidos['total'];
                        ?>

                        <div class="numero-dashboard"><h1><?= $total_servicos_concluidos ?></h1></div>
                            
                        </div>
                        
                    </div> 
 <!--------------------------------------->
                        <div class="sales">
                        <span>
                         <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
                           <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
                           <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
                         </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3>SERVIÇOS EM ANDAMENTO </h3>
                                            <?php
                $nivel = $_SESSION['nivel'] ?? '';
                $id_funcionario_logado = $_SESSION['id_funcionario'] ?? null;

                $sql_servicos_andamento = "SELECT COUNT(*) AS total FROM servicos WHERE status = 'andamento'";

                if ($nivel === 'tecnico') {
                    $sql_servicos_andamento .= " AND id_mecanico = ?";
                    $stmt = $conn->prepare($sql_servicos_andamento);
                    $stmt->bind_param("i", $id_funcionario_logado);
                    $stmt->execute();
                    $result_servicos_andamento = $stmt->get_result();
                } else {
                    $result_servicos_andamento = mysqli_query($conn, $sql_servicos_andamento);
                }

                if (!$result_servicos_andamento) {
                    die("Erro na query: " . mysqli_error($conn));
                }

                $row_servicos_andamento = mysqli_fetch_assoc($result_servicos_andamento);
                $total_servicos_andamento = $row_servicos_andamento['total'];
                ?>

                <div class="numero-dashboard"><h1><?= $total_servicos_andamento ?></h1></div>
                        </div>
                    </div> 
            <!------------------end--------------------->
                 </div>
            <!------------------recentes-------------------->

            <div class="Recent-Order">
                <h2>Informações Dos Serviços</h2>

              
                     <input 
    type="text" 
    name="pesquisar" 
    id="inputPesquisaServicos"
    class="searchInput" 
    placeholder="Pesquisar Serviços" 
    maxlength="50"
/>

<div class="servicos-grid" id="servicosGrid">
    
</div> 

                 
                   



<?php

 include_once('./configuracao/config.php');
include_once('./configuracao/conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$nivel = $_SESSION['nivel'] ?? '';
$id_funcionario_logado = $_SESSION['id_funcionario'] ?? null;

// Bloqueia técnico sem vínculo de funcionário, evitando ver tudo ou nada silenciosamente
if ($nivel === 'tecnico' && !$id_funcionario_logado) {
    die("⚠️ A tua conta não está associada a nenhum registo de funcionário. Contacta o administrador.");
}

$pesquisa = trim($_GET['pesquisa'] ?? '');

$sql = "
    SELECT s.*
    FROM servicos s
";

$condicoes = [];
$params = [];
$tipos = "";

if ($nivel === 'tecnico') {
    $condicoes[] = "s.id_mecanico = ?";
    $params[] = $id_funcionario_logado;
    $tipos .= "i";
}

if ($pesquisa !== '') {
    $condicoes[] = "(s.nome_cliente LIKE ? OR s.placa_carro LIKE ?)";
    $like = "%{$pesquisa}%";
    $params[] = $like;
    $params[] = $like;
    $tipos .= "ss";
}

if (!empty($condicoes)) {
    $sql .= " WHERE " . implode(" AND ", $condicoes);
}

$sql .= " ORDER BY s.data_registo DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($tipos, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="servicos-grid">
<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Gera as iniciais do nome do cliente
        $partes = explode(' ', trim($row['nome_cliente']));
        $iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

        // Define classe e texto do status
        $status = $row['status'];
        switch ($status) {
            case 'andamento':
                $status_classe = 'status-andamento';
                $status_texto = 'Em Andamento';
                break;
            case 'concluido':
                $status_classe = 'status-concluido';
                $status_texto = 'Concluído';
                break;
            default:
                $status_classe = 'status-pendente';
                $status_texto = 'Pendente';
                break;
        }

        // Formata a data de registo (dd/mm/aaaa)
        $data_formatada = date('d/m/Y', strtotime($row['data_registo']));

        echo "<div class='servico-card'>";

        echo "<div class='servico-header'>";
        echo "<div class='avatar'>{$iniciais}</div>";
        echo "<div class='servico-info'>";
        echo "<h4>" . htmlspecialchars($row['nome_cliente']) . "</h4>";
        echo "<span class='placa'>" . htmlspecialchars($row['placa_carro']) . "</span><br>";
        echo "<span class='status-badge {$status_classe}'>{$status_texto}</span>";
        echo "</div></div>";

        echo "<div class='servico-detalhes'>";
        echo "<p><strong>Endereço:</strong> " . htmlspecialchars($row['endereco']) . "</p>";
        echo "<p><strong>Modelo:</strong> " . htmlspecialchars($row['modelo_carro']) . "</p>";
        echo "<p><strong>Data:</strong> {$data_formatada}</p>";
        echo "</div>";

        echo "<div class='servico-acoes'>";

        echo "<a class='btn-acao btn-editar' href='editar_servico.php?id=" . (int)$row['id'] . "'>
                <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'>
                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                </svg> Editar
              </a>";

        echo "<a class='btn-acao btn-factura' href='factura.php?id=" . (int)$row['id'] . "' target='_blank'>
                Factura
              </a>";

        if ($nivel === 'admin') {
            echo "<a class='btn-acao btn-deletar' href='models/eliminar_servico.php?id=" . (int)$row['id'] . "' onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                    <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'>
                        <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                        <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                    </svg>
                  </a>";
        }

        echo "</div>"; // servico-acoes
        echo "</div>"; // servico-card
    }
} else {
    if ($pesquisa != '') {
        echo "<p class='sem-resultado'>Nenhum registro encontrado</p>";
    } else {
        echo "<p class='sem-resultado'>Nenhum serviço encontrado</p>";
    }
}
?>
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

const inputPesquisa = document.getElementById("inputPesquisaServicos");
const cards = document.querySelectorAll(".servico-card");

inputPesquisa.addEventListener("keyup", function () {

    let texto = this.value.toLowerCase();

    cards.forEach(function(card){

        let conteudo = card.textContent.toLowerCase();

        if(conteudo.indexOf(texto) > -1){
            card.style.display = "";
        }else{
            card.style.display = "none";
        }

    });

});

  
   </script>         
 </body>
</html>