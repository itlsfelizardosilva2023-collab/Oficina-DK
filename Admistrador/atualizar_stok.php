

<?php
session_start();

include_once("./models/stok_models.php");
include("./configuracao/conexao.php");
include_once("./funcoes/funcao_atualizar.php");





include_once("./funcoes/funcao_atualizar.php");
require_once("./configuracao/conexao.php");


####################### atualizar #########################

include_once("./funcoes/funcao_atualizar.php");
require_once("./configuracao/conexao.php");

// pega o id da URL (necessário para o GET inicial, quando a página carrega)
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = (int) ($_POST['id_estoque'] ?? $id);

    $dados = [
        'tipo'           => $_POST['tipo'] ?? '',
        'nome'           => $_POST['nome'] ?? '',
        'quantidade'     => (int) ($_POST['quantidade'] ?? 0),
        'marca'          => $_POST['marca'] ?? '',
        'preco'          => (float) ($_POST['preco'] ?? 0),
        'codigo'         => $_POST['codigo'] ?? '',
        'data_expiracao' => $_POST['data_expiracao'] ?? null,
    ];

    $resultado = atualizarRegistro($conn, 'estoque', $dados, $id, 'id_estoque');

    if ($resultado === true) {
        header("Location: stok.php?id=$id&msg=atualizado");
        exit();
    } else {
        echo "Erro ao actualizar: " . $resultado;
    }
}

// Query 1: produto único, usado para preencher o FORMULÁRIO de edição
$stmt = $conn->prepare("SELECT * FROM estoque WHERE id_estoque = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();

if (!$produto) {
    die("Produto não encontrado");
}

// Query 2: todos os produtos, usado para a LISTAGEM lateral
$stmtLista = $conn->prepare("SELECT * FROM estoque ORDER BY nome ASC");
$stmtLista->execute();
$result = $stmtLista->get_result();
?>



<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="stylesheet" href="../views/css/stok.css?v=1.0">
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
  </head>
   <body>
    
    <div class="container">
       <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
              <main>
                <h1 class="cina">PAINEL ADMINISTRATIVO</h1>
                 <div class="insights">
                    
 <!--------------------------------------->
                   
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
                            <svg width="60" height="60">
                                <circle cx="30" cy="30" r="24" class="bg"></circle>
                                <circle cx="30" cy="30" r="24" class="progress" id="progress-carros"></circle>
                            </svg>
                            <div class="number" id="percent-carros">0%</div>
                        </div>
                        <div class="midlle">
                            
                                 <h3>Peças e Materias</h3>
                                <h1>Stok</h1>
                     
                        </div>
                          <small class="text-muted">
                           estavel
                        </small>
                    </div> 

                                               <div class="sales">
                        <span class="card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-safe-fill" viewBox="0 0 16 16">
                            <path d="M9.778 9.414A2 2 0 1 1 6.95 6.586a2 2 0 0 1 2.828 2.828"/>
                            <path d="M2.5 0A1.5 1.5 0 0 0 1 1.5V3H.5a.5.5 0 0 0 0 1H1v3.5H.5a.5.5 0 0 0 0 1H1V12H.5a.5.5 0 0 0 0 1H1v1.5A1.5 1.5 0 0 0 2.5 16h12a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 14.5 0zm3.036 4.464 1.09 1.09a3 3 0 0 1 3.476 0l1.09-1.09a.5.5 0 1 1 .707.708l-1.09 1.09c.74 1.037.74 2.44 0 3.476l1.09 1.09a.5.5 0 1 1-.707.708l-1.09-1.09a3 3 0 0 1-3.476 0l-1.09 1.09a.5.5 0 1 1-.708-.708l1.09-1.09a3 3 0 0 1 0-3.476l-1.09-1.09a.5.5 0 1 1 .708-.708M14 6.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 1 0"/>
                        </svg>
                                                    </span>
                             <!------------gráfico----------------------->
                             <div class="progress-circle small">
                            <svg width="60" height="60">
                                <circle cx="30" cy="30" r="24" class="bg"></circle>
                                <circle cx="30" cy="30" r="24" class="progress" id="progress-carros"></circle>
                            </svg>
                            <div class="number" id="percent-carros">0%</div>
                        </div>

                        <div class="midlle">
                            
                                <h3>Capital em Stok</h3>
                        <h1><?= number_format($total, 2, ',', '.') ?> Kz</h1>
                            
                        </div>
                      
                    </div> 

                     <!-----------------AVISO DE MANUTENCAO--------------------->
                       
            <!------------------end--------------------->
                 </div>
                  




                                        
                 <!------------------recentes-------------------->

           

            <div class="Recent-Order">
                <h2>Materias e peças em Stock</h2>
                    <form method="POST">

                    <div class="search-container">
                        <input 
                            type="text" 
                            name="pesquisar" 
                            class="searchInput" 
                            placeholder="Pesquisar Produtos"
                            maxlength="50"
                        />

                        <button type="submit" class="Pesquisar">
                            <h3>Pesquisar</h3>
                        </button>
                    </div>

        </form>
                    <div class="table-container">

                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Marca</th>
                            <th>Tipo</th>
                            <th>codigo</th>

                           
                        </tr>
                                                </thead>
                                              <tbody>

                    <?php while($row = $result->fetch_assoc()){ ?>

                    <tr>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                        <td><?= number_format($row['preco'], 2, ',', '.') ?></td>
                        <td><?= (int)$row['quantidade'] ?></td>
                        <td><?= htmlspecialchars($row['marca']) ?></td>
                        <td><?= htmlspecialchars($row['tipo']) ?></td>
                        <td><?= htmlspecialchars($row['codigo']) ?></td>
                           <td class="actions">

                                    <a class="btn view"
                                    href="atualizar_stok.php?id=<?= $row['id_estoque'] ?>">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-pencil"
                                            viewBox="0 0 16 16">

                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z"/>
                                        </svg>

                                    </a>

                                     <?php if ($_SESSION['nivel'] === "admin") { ?>
                                    <a class="btn delete"
                                    href="stok.php?excluir_id=<?= $row['id_estoque'] ?>"
                                    onclick="return confirm('Tem certeza que deseja deletar?')">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-trash3"
                                            viewBox="0 0 16 16">

                                            <path d="M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z"/>
                                            <path d="M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z"/>
                                        </svg>

                                    </a>
                               <?php } ?>

                                </td>

                            </tr>

                            <?php } ?>

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
                   
                      <!------------------end--------------------->
                       
                       </div>
              
        </div>
          
                               <div class="containerc">

            

                            <div class="right">

                                <h2>Atualizar Stok</h2>




<form method="POST">

    <input type="hidden" name="id_estoque" value="<?= $produto['id_estoque'] ?>">

    <select name="tipo" required>
        <option value="Peça" <?= ($produto['tipo'] == 'Peça') ? 'selected' : '' ?>>
            Peça
        </option>
        <option value="Material" <?= ($produto['tipo'] == 'Material') ? 'selected' : '' ?>>
            Material
        </option>
    </select>

    <input
        type="text"
        name="nome"
        value="<?= htmlspecialchars($produto['nome']) ?>"
        required
    >

    <input
        type="number"
        name="quantidade"
        value="<?= (int)$produto['quantidade'] ?>"
        min="0"
        required
    >

    <input
        type="number"
        step="0.01"
        name="preco"
        value="<?= $produto['preco'] ?>"
        min="0"
        required
    >

    <input
        type="text"
        name="marca"
        value="<?= htmlspecialchars($produto['marca']) ?>"
    >

    <input
        type="text"
        name="codigo"
        value="<?= htmlspecialchars($produto['codigo']) ?>"
        required
    >

    <input
        type="date"
        name="data_expiracao"
        value="<?= $produto['data_expiracao'] ?>"
    >

    <button type="submit">
        Actualizar Produto
    </button>

</form>
                     
                           </div>

                        </div>


        
                <script src="../views/js/stok.js"></script>
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

           
            </script>
            </body>
</html>