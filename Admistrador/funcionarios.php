
<?php


    include_once("./models/funcionarios_model.php");
   $pesquisa = trim($_GET['pesquisa'] ?? '');

$sql = "
    SELECT 
        f.id_funcionario,
        f.nome,
        f.email,
        f.endereco,
        f.telefone,
        c.nome_cargo,
        s.nome_setor
    FROM funcionarios f
    LEFT JOIN cargos c ON c.id_cargo = f.id_cargo
    LEFT JOIN setores s ON s.id_setor = c.id_setor
";

if ($pesquisa !== '') {
    $sql .= "
        WHERE f.nome LIKE ?
           OR f.email LIKE ?
           OR c.nome_cargo LIKE ?
           OR s.nome_setor LIKE ?
        ORDER BY f.id_funcionario DESC
    ";
    $stmt = $conexao->prepare($sql);
    $like = "%{$pesquisa}%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
} else {
    $sql .= " ORDER BY f.id_funcionario DESC LIMIT 10";
    $stmt = $conexao->prepare($sql);
}

if (!$stmt) {
    die("Erro na query: " . $conexao->error);
}


// 🔑 Roda só UMA VEZ, no topo da página, antes de todos os cards
$sql = "
    SELECT s.nome_setor, COUNT(f.id_funcionario) AS total
    FROM setores s
    LEFT JOIN cargos c ON c.id_setor = s.id_setor
    LEFT JOIN funcionarios f ON f.id_cargo = c.id_cargo
    GROUP BY s.id_setor, s.nome_setor
";
$result = mysqli_query($conexao, $sql);

if (!$result) {
    die("Erro SQL: " . mysqli_error($conexao));
}

$totaisPorSetor = [];
while ($row = mysqli_fetch_assoc($result)) {
    $totaisPorSetor[$row['nome_setor']] = (int)$row['total'];
}
$stmt->execute();
$result = $stmt->get_result(); 
?>

<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K<</title>
   <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/funcionarios.css?v=1.0">
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
                            
                                <h3>  NÚMEROS DE FUNCIONARIOS DA OFICINA</h3>
                                <h1><p class="valor">
                                     <?php
                                          $sql = "SELECT COUNT(*) AS total FROM funcionarios ";
                                          $qtd = mysqli_query($conexao, $sql);
                                          $row = mysqli_fetch_assoc($qtd);
                                          echo $row['total'];
                                     ?></h1></h1>
                            
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
                            
                                <h3> NÚMEROS DE FUNCIONARIOS DA MECÂNICA</h3>
                                
                                <h1>
                                     <p class="valor">
                                     <h1><p class="valor"><?= $totaisPorSetor['Mecânica'] ?? 0 ?></p></h1>
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
                            
                                <h3> NÚMEROS DE FUNCIONARIOS DA FUNILARIA </h3>
                                <h1><p class="valor">
                                     <h1><p class="valor"><?= $totaisPorSetor['Funilaria'] ?? 0 ?></p></h1>
                                   </h1>

                          
                        </div>
                    </div> 
                    
            <!------------------end--------------------->
                 </div>
                 <!------------------loanding--------------------->
       
            <!------------------recentes-------------------->
            
            <div class="Recent-Order">
                <h2>Informações Dos Funcionarios</h2>

                        <form method="GET" action="">
                <div class="search-container">
                    <input type="text"
       id="pesquisa"
       placeholder="Pesquisar funcionário..."
       class="searchInput">
                   
                    <button type="submit" class="Pesquisar">
                        <h3>Pesquisar</h3>                     </button>
                </div>  
            </form>

                 <div class="btn-add">
                    <a href="registar_funcionario.php">
                        <button class="btn-add">
                           <h3>Adicionar</h3> 
                        </button>
                    </a>
                </div>

                    <div class="table-container">



                <table class="modern-table">
                    <thead>
                        <tr><th>Nome</th>
                            <th>Email</th>
                            <th>Endereço</th>
                            <th>Telefone</th>
                            <th>Cargo</th>
                            <th>Setor</th>
                            <th>Ações</th>
                           
                        </tr>
                    </thead>
             
                       <tbody id="resultado">

   <?php
if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['endereco']) . "</td>";
        echo "<td>" . htmlspecialchars($row['telefone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nome_cargo'] ?? '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['nome_setor'] ?? '—') . "</td>";

        echo "<td class='actions'>
            <a class='btn view' href='Atualizacao_funcionario.php?id=" . (int)$row['id_funcionario'] . "'>
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                </svg>
            </a>

            <a class='btn delete' href='funcionarios.php?excluir_id=" . (int)$row['id_funcionario'] . "' onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                    <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                    <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                </svg>
            </a>
        </td>";

        echo "</tr>";
    }

} else {
    if ($pesquisa != '') {
        echo "<tr>
                <td colspan='7' style='text-align:center;'>
                    Nenhum registro encontrado
                </td>
            </tr>";
    } else {
        echo "<tr>
                <td colspan='7' style='text-align:center;'>
                    Nenhum funcionário cadastrado
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

    <script src="../views/js/funcionarios.js"></script>
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

            
function buscarFuncionarios(valor) {
    let xhr = new XMLHttpRequest();

    xhr.open("GET", "buscar_funcionarios.php?pesquisa=" + encodeURIComponent(valor), true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById("resultado").innerHTML = xhr.responseText;
        }
    };

    xhr.send();
}

// Dispara a busca enquanto escreves
document.getElementById("pesquisa").addEventListener("keyup", function () {
    buscarFuncionarios(this.value);
});

// 🔑 Carrega a lista padrão (últimos 10) assim que a página abre
document.addEventListener("DOMContentLoaded", function () {
    buscarFuncionarios("");
});



</script>
</script>
 </body>
</html>