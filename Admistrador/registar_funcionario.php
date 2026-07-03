
<?php

  include_once("./models/registar_funcionario.php");
$setores = $conn->query("SELECT id_setor, nome_setor FROM setores ORDER BY nome_setor");

?>

<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/registar_funcionario.css?v=1.0">
  </head>
   <body>
    
    <div class="container">
          <?php include_once('sidebar.php');?>
          
                      <div class="containerc">

            

                            <div class="right">

                                <h2>REGISTRO DE FUNCIONARIO</h2>



                                <form method="POST">
 <input type="text" name="nome" placeholder="Nome do funcionario" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" maxlength="30">

<input type="text" name="endereco" placeholder="Cidade/Município/Bairro/Rua/Nº da casa" value="<?= htmlspecialchars($_POST['endereco'] ?? '') ?>">

<input type="email" name="email" placeholder="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

<input type="text" name="telefone" placeholder="Nº de telefone" value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>" maxlength="9">

<!-- Setor -->
<label>Setor:</label>
<select name="id_setor" id="setor" onchange="onSetorChange(this.value)" required>
    <option value="">Selecione</option>
    <?php while ($s = $setores->fetch_assoc()): ?>
        <option value="<?= $s['id_setor'] ?>">
            <?= htmlspecialchars($s['nome_setor']) ?>
        </option>
    <?php endwhile; ?>
    <option value="novo">Outro...</option>
</select>

<input type="text" name="novo_setor" id="novo_setor" placeholder="Nome do novo setor" style="display:none;">

<!-- Cargo -->
<label>Cargo:</label>
<select name="id_cargo" id="cargo" onchange="onCargoChange(this.value)" required>
    <option value="">Selecione o setor primeiro</option>
</select>

<input type="text" name="novo_cargo" id="novo_cargo" placeholder="Novo cargo" style="display:none;">

<input type="password" name="senha_adm" placeholder="Senha do Administrador">

<button type="submit">Registrar Funcionário</button>  
                                <a  class="Voltar" href="funcionarios.php">
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

function onSetorChange(valor) {
    const novoSetorInput = document.getElementById('novo_setor');
    const selectCargo = document.getElementById('cargo');

    if (valor === 'novo') {
        // setor novo ainda não existe, então o cargo também tem que ser novo
        novoSetorInput.style.display = 'block';
        novoSetorInput.required = true;

        selectCargo.innerHTML = '<option value="novo" selected>Outro...</option>';
        onCargoChange('novo');
        return;
    }

    novoSetorInput.style.display = 'none';
    novoSetorInput.required = false;
    novoSetorInput.value = '';

    if (!valor) {
        selectCargo.innerHTML = '<option value="">Selecione o setor primeiro</option>';
        onCargoChange('');
        return;
    }

    carregarCargos(valor);
}

function carregarCargos(idSetor) {
    const selectCargo = document.getElementById('cargo');
    selectCargo.innerHTML = '<option value="">Carregando...</option>';

    fetch(`buscar_cargos.php?id_setor=${idSetor}`)
        .then(res => res.json())
        .then(data => {
            let opcoes = '<option value="">Selecione o cargo</option>';
            data.forEach(c => {
                opcoes += `<option value="${c.id_cargo}">${c.nome_cargo}</option>`;
            });
            opcoes += '<option value="novo">Outro...</option>';
            selectCargo.innerHTML = opcoes;
        })
        .catch(() => {
            selectCargo.innerHTML = '<option value="">Erro ao carregar cargos</option><option value="novo">Outro...</option>';
        });
}

function onCargoChange(valor) {
    const novoCargoInput = document.getElementById('novo_cargo');

    if (valor === 'novo') {
        novoCargoInput.style.display = 'block';
        novoCargoInput.required = true;
    } else {
        novoCargoInput.style.display = 'none';
        novoCargoInput.required = false;
        novoCargoInput.value = '';
    }
}
    
function onCargoChange(valor) {
    const novoCargoInput = document.getElementById('novo_cargo');

    if (valor === 'novo') {
        novoCargoInput.style.display = 'block';
        novoCargoInput.required = true;
    } else {
        novoCargoInput.style.display = 'none';
        novoCargoInput.required = false;
        novoCargoInput.value = '';
    }
}

</script>
 </body>
</html>