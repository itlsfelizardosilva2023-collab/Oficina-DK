<?php
session_start();
    include_once("./models/carros_models.php");
   
?>

          
<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Oficina D.K</title>
     <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/carros.css?v=1.0">
  </head>
   <body>
    
    <div class="container">
         <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
              <main>
                <h1 class="cina">PAINEL </h1>
                 <div class="insights">
                    <div class="sales">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                          <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                        </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3>  CARROS ADICIONADOS RECENTIMENTE</h3>
                               <?php
                                    $sql = "SELECT COUNT(*) AS total_carros_30_dias
                                            FROM carros
                                            WHERE criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)";

                                    $resultado = mysqli_query($conn, $sql);
                                    $dados = mysqli_fetch_assoc($resultado);

                                    $totalCarros = $dados['total_carros_30_dias'];
                                    ?>

                                    <h1><?= $totalCarros ?></h1>
                            
                            
                        </div>
                        <small class="text-muted">
                            Nos ultimos 30 dias 
                        </small>
                    </div>
 <!--------------------------------------->
                           <div class="sales">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                          <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                        </svg>

                        </span>
                        <div class="midlle">
                            
                                <h3> CARROS COM CONTRATO </h3>
                               <?php
                        $sql_com_contrato = "
                            SELECT COUNT(DISTINCT cc.id_carro) AS total
                            FROM contrato_carros cc
                        ";
                        $res_com_contrato = $conn->query($sql_com_contrato);
                        $total_com_contrato = $res_com_contrato->fetch_assoc()['total'] ?? 0;
                        ?>

                        <h1><?= $total_com_contrato ?> </h1>
                                                    
                        </div>
                        
                    </div> 
 <!--------------------------------------->
                        <div class="sales">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                          <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                        </svg>
                        </span>
                        <div class="midlle">
                            
                                <h3>  CARROS  SEM SERVIÇO </h3>
                               <?php
                                    $sql_sem_servico = "
                                        SELECT COUNT(*) AS total
                                        FROM carros c
                                        WHERE NOT EXISTS (
                                            SELECT 1 FROM servicos s WHERE s.placa_carro = c.matricula
                                        )
                                    ";
                                    $res_sem_servico = $conn->query($sql_sem_servico);
                                    $total_sem_servico = $res_sem_servico->fetch_assoc()['total'] ?? 0;
                                    ?>

                                    <h1><?= $total_sem_servico ?></h1>
                          
                        </div>
                    </div> 
            <!------------------end--------------------->
                 </div>
             
            <!------------------recentes-------------------->

            <div class="Recent-Order">
                <h2>Informações Das Viaturas</h2>

              
                 <form method="GET" action="">
                <div class="search-container">
                   <input type="text"
       id="pesquisa"
       placeholder="Pesquisar carros..."
       class="searchInput">
                         
                          <button  class="Pesquisar" type="submit">Pesquisar</button>
                </div>  
            </form>
 

                 <div class="btn-add">
                    <a href="registar_carro.php">
                        <button class="btn-add">
                           <h3>Adicionar</h3> 
                        </button>
                    </a>
                </div>

                   
              
                <table  class="modern-table" >
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Modelo</th>
                            <th>Matrícula</th>
                            <th>Cor</th>
                             <th>Data de entrada</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                   <tbody id="resultado">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>

            <?php
                $status = $row['status_servico'] ?? 'Sem serviço';

                $statusClass = '';

                if ($status !== 'Sem serviço') {
                    $statusClass = match ($status) {
                        'Cancelado' => 'status-vermelho',
                        'Pendente' => 'status-castanho',
                        'Em andamento' => 'status-amarelo',
                        'Concluído' => 'status-verde',
                        default => 'status-default'
                    };
                }
            ?>

            <tr>
                <td><?= htmlspecialchars($row['cliente']) ?></td>

                <td>
                    <?php if ($status === 'Sem serviço'): ?>
                        <?= htmlspecialchars($status) ?>
                    <?php else: ?>
                        <span class="status <?= $statusClass ?>">
                            <?= htmlspecialchars($status) ?>
                        </span>
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($row['modelo']) ?></td>
                <td><?= htmlspecialchars($row['matricula']) ?></td>
                <td><?= htmlspecialchars($row['cor']) ?></td>
                <td><?= htmlspecialchars($row['criado_em']) ?></td>

                <td class="actions">

                    <!-- EDITAR -->
                    <a class='btn view' href='atualizar_carro.php?id=<?= $row["id_carro"] ?>'>
                         <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                        <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                    </svg> 
                    </a>

                    
                  <?php if ($_SESSION['nivel'] === "admin") { ?>
                                <!-- ELIMINAR -->
                    <a class='btn delete'
                       href='carros.php?excluir_id=<?= $row["id_carro"] ?>'
                       onclick="return confirm('Tem certeza que deseja deletar?')">
                         <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                            <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                            <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                        </svg>
                    </a>
                 <?php } ?>
                                 
                </td>
            </tr>

        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center; color:red;">
                Nenhum carro encontrado
            </td>
        </tr>
    <?php endif; ?>
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

    <script src="../views/js/carros.js"></script>
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

             document.getElementById("pesquisa").addEventListener("input", function () {

    let valor = this.value;

    fetch("buscar_carros.php?pesquisa=" + encodeURIComponent(valor))
        .then(response => response.text())
        .then(data => {
            document.getElementById("resultado").innerHTML = data;
        });

});
</script>
 </body>
</html>