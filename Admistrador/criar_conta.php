
<?php
    include_once("./models/criar_conta_models.php");

$funcionarios = $conn->query("SELECT id_funcionario, nome, email FROM funcionarios");
$func_array = [];
while($f = $funcionarios->fetch_assoc()){
    $func_array[$f['id_funcionario']] = $f;
}
?>

<!DOCTYPE html>  
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/cadastro.css?v=2.1">
  </head>
   <body>
    
    <div class="container">
     <?php include_once('sidebar.php');?>
 
       
             <div class="containerc">

            

            <div class="right">

         <h2>CRIAR CONTA</h2>

<form method="POST">
    <label>Funcionário:</label>
    <select name="id_funcionario" id="funcionario_select" required>
        <option value="">-- Selecione um funcionário --</option>
        <?php foreach($func_array as $id => $f): ?>
            <option value="<?= $id ?>"><?= $f['nome'] ?></option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="nome" id="nome" placeholder="Nome do usuário" required    readonly value="<?= $_POST['nome'] ?? '' ?>">
    <input type="email" name="email" id="email" placeholder="Email" required  readonly   value="<?= $_POST['email'] ?? '' ?>">
    <input type="password" name="senha_usuario" placeholder="Senha" required value="<?= $_POST['senha_usuario'] ?? '' ?>">
    <input type="password" name="confirmar" placeholder="Confirmar senha" required value="<?= $_POST['confirmar'] ?? '' ?>">

    <select name="nivel" required>
        <option value="">Nível de acesso</option>
        <option value="admin">Administrador</option>
        <option value="usuario">Usuário</option>
           <option value="tecnico">tecnico</option>
    </select>

    <input type="password" name="senha_admin" placeholder="Senha do Administrador" required value="<?= $_POST['senha_admin'] ?? '' ?>">

    <button type="submit" name="cadastrar">CRIAR CONTA</button>



   
   <a  class="Voltar" href="contas.php">
      <h3>Voltar</h3> 
   </a>

   </form>
   <?php
                       if($erro != ""){
                            echo "<p class='erro'>$erro</p>";
                             }
                             ?>
   </div>

   </div>

     


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
             document.addEventListener("click", function(){

      let erro = document.querySelector(".erro");


      if(erro){
      erro.style.display = "none";
      }


      });

      const funcionarios = <?= json_encode($func_array) ?>;

document.getElementById('funcionario_select').addEventListener('change', function() {
    const id = this.value;
    if(id && funcionarios[id]){
        document.getElementById('nome').value = funcionarios[id]['nome'];
        document.getElementById('email').value = funcionarios[id]['email'];
    } else {
        document.getElementById('nome').value = '';
        document.getElementById('email').value = '';
    }
});

</script>
 </body>
</html>