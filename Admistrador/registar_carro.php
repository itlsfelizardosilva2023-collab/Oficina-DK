
<?php


session_start();
require_once "salvar_carro.php";

  

  
?>

<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/registar_carro.css?v=1.0">
  </head>
   <body>
    
    <div class="container">
          
     <?php include_once('sidebar.php');?>
           
            <div class="containerc">

            

            <div class="right">

            <h2>ADICIONAR AUTOMÓVEL</h2>


                      <form method="POST">

            <!-- Marca -->
            <label>Marca:</label>
            <select name="id_marca" id="marca" onchange="carregarModelos(this.value)" >
                <option value="">Selecione</option>
                <?php
                $res = $conn->query("SELECT * FROM marcas");
                while($m = $res->fetch_assoc()){
                    echo "<option value='{$m['id_marca']}'>{$m['nome']}</option>";
                }
                ?>
                <option value="nova">Outra...</option>
            </select>

            <input type="text" name="nova_marca" id="nova_marca" placeholder="Nova marca" style="display:none;">

            <!-- Modelo -->
            <label>Modelo:</label>
            <select name="id_modelo" id="modelo">
                <option value="">Selecione a marca primeiro</option>
            </select>

            <input type="text" name="novo_modelo" id="novo_modelo" placeholder="Novo modelo">

            <!-- Outros dados -->
            <input type="text" name="matricula" placeholder="Matrícula Exe:. LD-00-00-XX" required>
            <input type="text" name="cor" placeholder="Cor"  required>
           
          <!-- Cliente por BI -->
                    <label>Nº de BI do Cliente:</label>
                    <input 
                        type="text" 
                        id="bi_input" 
                        placeholder="Digite o BI do cliente..." 
                        maxlength="14"
                        autocomplete="off"
                    >

                    <!-- Nome aparece aqui automaticamente -->
                    <div id="cliente_info" style="display:none;">
                        <p id="cliente_nome_display"></p>
                    </div>

                    <!-- Campo hidden que envia o id real -->
                    <input type="hidden" name="id_cliente" id="id_cliente_hidden">

                    <p id="bi_erro" style="color:red; display:none;">Cliente não encontrado.</p>

            <button type="submit">Cadastrar</button>

            <a href="carros.php">
                <h3>Voltar</h3>
                </a>

               <?php
                       if($erro != ""){
                            echo "<p class='erro'>$erro</p>";
                             }
                             ?>      
        </form>

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

            // mostrar campo nova marca
        document.getElementById("marca").addEventListener("change", function(){
            if(this.value === "nova"){
                document.getElementById("nova_marca").style.display = "block";
            } else {
                document.getElementById("nova_marca").style.display = "none";
            }
        });

        // carregar modelos via AJAX
        function carregarModelos(id_marca){
            if(id_marca === "nova") return;

            fetch("buscar_modelos.php?id_marca=" + id_marca)
            .then(res => res.text())
            .then(data => {
                document.getElementById("modelo").innerHTML = data;
            });
        }

        const biInput     = document.getElementById('bi_input');
const clienteInfo = document.getElementById('cliente_info');
const clienteNome = document.getElementById('cliente_nome_display');
const idHidden    = document.getElementById('id_cliente_hidden');
const biErro      = document.getElementById('bi_erro');

let typingTimer;

biInput.addEventListener('input', function(){
    clearTimeout(typingTimer);

    const bi = this.value.trim();

    // Limpa estado anterior
    clienteInfo.style.display = 'none';
    biErro.style.display      = 'none';
    idHidden.value            = '';

    if(bi.length < 14) return; // só busca a partir de 5 caracteres

    typingTimer = setTimeout(() => {
        fetch('buscar_cliente_bi.php?bi=' + encodeURIComponent(bi))
        .then(res => res.json())
        .then(data => {
            if(data.encontrado){
                clienteNome.textContent   = 'NOME:' + data.nome;
                clienteInfo.style.display = 'block';
                idHidden.value            = data.id;
                biErro.style.display      = 'none';
            } else {
                clienteInfo.style.display = 'none';
                biErro.style.display      = 'block';
                idHidden.value            = '';
            }
        });
    }, 500); // aguarda 500ms depois de parar de digitar
});

</script>
 </body>
</html>