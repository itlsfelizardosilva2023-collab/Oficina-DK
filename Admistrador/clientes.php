

<?php
        include_once('./configuracao/config.php');
        include_once('./configuracao/conexao.php');
        include_once("./models/clientes _models.php");
?>


<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/Clientes.css?v=1.0">
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
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                            <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                            <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                          </svg >
                        </span>
                        <div class="midlle">
                            
                                <h3>  CLIENTES ADICIONADOS RECENTIMENTE</h3>
                               <?php
                                $sql_clientes_recentes = "SELECT COUNT(*) AS total FROM clientes WHERE criado_em >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                                $result_clientes_recentes = mysqli_query($conn, $sql_clientes_recentes);

                                if (!$result_clientes_recentes) {
                                    die("Erro na query: " . mysqli_error($conn));
                                }

                                $row_clientes_recentes = mysqli_fetch_assoc($result_clientes_recentes);
                                $total_clientes_recentes = $row_clientes_recentes['total'];
                                ?>


                                       
                              
                            <p class="numero-dashboard"><h1><?=$total_clientes_recentes  ?></h1></p>
                        </div>
                        
                        <small class="text-muted">
                            Nos ultimos 30 dias 
                        </small>
                    </div>
 <!--------------------------------------->
                           <div class="sales">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                            <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                            <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                          </svg >

                        </span>
                        <div class="midlle">
                            
                                <h3> CLIENTES COM  CONTRATO </h3>
                            <?php
                        $sql_clientes_contrato = "SELECT COUNT(DISTINCT id_cliente) AS total FROM contratos";
                        $result_clientes_contrato = mysqli_query($conn, $sql_clientes_contrato);

                        if (!$result_clientes_contrato) {
                            die("Erro na query: " . mysqli_error($conn));
                        }

                        $row_clientes_contrato = mysqli_fetch_assoc($result_clientes_contrato);
                        $total_clientes_contrato = $row_clientes_contrato['total'];
                        ?>

                     
                        <p class="numero-dashboard"><h1><?= $total_clientes_contrato ?></h1></p>
                        </div>
                        
                    </div> 
 <!--------------------------------------->
                        <div class="sales">
                        <span>
                          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-vcard" viewBox="0 0 16 16">
                            <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                            <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                          </svg >

                        </span>
                        <div class="midlle">
                            <?php
                            $sql_total_clientes = "SELECT COUNT(*) AS total FROM clientes";
                            $result_total_clientes = mysqli_query($conn, $sql_total_clientes);

                            if (!$result_total_clientes) {
                                die("Erro na query: " . mysqli_error($conn));
                            }

                            $row_total_clientes = mysqli_fetch_assoc($result_total_clientes);
                            $total_clientes = $row_total_clientes['total'];
                            ?>

                            <h3>Total de Clientes</h3>
                            <p class="numero-dashboard">
                               
                          <h1><?= $total_clientes ?></h1> </p>
                          
                        </div>
                    </div> 
            <!------------------end--------------------->
                 </div>
                 <!------------------loanding--------------------->
        
            <!------------------recentes-------------------->

            <div class="Recent-Order">
                <h2>Informações Dos Clientes</h2>

              
                        <form method="GET" action="">
                <div class="search-container">
                   <input type="text"
       id="pesquisa"
       placeholder="Pesquisar cliente..."
        class="searchInput">
                    <button type="submit" class="Pesquisar">
                        <h3>Pesquisar</h3>                     </button>
                </div>  
            </form>

                 <div class="btn-add">
                    <a href="registar_clientes.php">
                        <button class="btn-add">
                           <h3>Adicionar </h3> 
                        </button>
                    </a>
                </div>

                    
             <div class="table-container">


                <table   class="modern-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th> Telefone</th>
                            <th>Número do bilhete</th>
                            <th>Endereço</th>
                          
                            
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tbody id="resultado">

<?php

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";

            echo "<td>".$row['nome']."</td>";
            echo "<td>".$row['telefone']."</td>";
            echo "<td>".$row['numero_bi']."</td>";
            echo "<td>".$row['endereco']."</td>";
           

            echo "<td class='actions'>
                <a class='btn view' href='Atualizacao_do_Cliente.php?id=".$row['id_cliente']."'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                        <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                    </svg>
                </a>";

            if ($_SESSION['nivel'] === "admin") {
                echo "<a class='btn delete' href='clientes.php?excluir_id=".$row['id_cliente']."' onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                        <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                        <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                    </svg>
                </a>";
            }

            echo "</td>";

            echo "</tr>";
        }

    } else {

        if ($pesquisa != '') {
            echo "<tr>
                    <td colspan='3' style='text-align:left;'>
                        Nenhum registro encontrado
                    </td>
                </tr>";
        }
    }

?>

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

            document.getElementById("pesquisa").addEventListener("keyup", function(){

    let valor = this.value;

    let xhr = new XMLHttpRequest();

    xhr.open("GET", "buscar_clientes.php?pesquisa=" + valor, true);

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