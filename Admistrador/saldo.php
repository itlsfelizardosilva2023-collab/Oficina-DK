<?php
session_start();
include_once("./models/saldo_models.php");  
?>

<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="stylesheet" href="../views/css/saldo.css">
   <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
  </head>
   <body>
    
    <div class="container">
           <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->
              <main>
                <h1 class="cina">PAINEL </h1>
                 <div class="insights">
                    
 <!--------------------------------------->
                   
 <!--------------------------------------->
                        <div class="sales">
                     <span class="card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                            <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                            <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
                        </svg>
                     </span>
                              <!------------gráfico----------------------->
                            
                        <div class="midlle">
                            
                                <h3>Capital da Empresa</h3>

                                <h1>
                                    <?= number_format($capital_total, 2, ",", ".") ?> Akz
                                </h1>
                     
                        </div>
                          <small class="text-muted">
                           Bom  rendimento 
                        </small>
                    </div> 

      

                    
                 </div>
                  




                                        
                 <!------------------recentes-------------------->

           

            <div class="Recent-Order">
                <h2>Operações Realizadas</h2>
                    <form method="POST">

                    <div class="search-container">
                       <input  class="searchInput" type="text" id="pesquisaMovimentos"placeholder="Pesquisar descrição, fluxo, valor ou data...">
                        <button type="submit" class="Pesquisar">
                            <h3>Pesquisar</h3>
                        </button>
                    </div>

        </form>
         <div class="btn-add">
    <button class="btn-add" onclick="abrirModalRetirar()">
        <h3>Retirar</h3>
    </button>
</div>

         <div class="table-container">
            <table class="modern-table">
    <thead >
        <tr>
            <th>Descrição</th>
            <th>Fluxo</th>
            <th>Valor</th>
            <th>Data</th>
        </tr>
    </thead>

    <tbody>
        <?php 
        $total_entradas = 0;
        $total_saidas   = 0;
        $rows = [];

        while($row = mysqli_fetch_assoc($result)):
            if ($row['fluxo'] === 'entrada') {
                $total_entradas += $row['valor'];
            } else {
                $total_saidas += $row['valor'];
            }
            $rows[] = $row;
        endwhile;

        foreach($rows as $i => $row):
            $cor_fluxo = $row['fluxo'] === 'entrada' ? 'green' : 'red';
        ?>
        <tr class="linha-movimento" style="<?= $i >= 5 ? 'display:none;' : '' ?>">
            <td><?= htmlspecialchars($row['descricao']) ?></td>
            <td style="color:<?= $cor_fluxo ?>; font-weight:600;">
                <?= strtoupper($row['fluxo']) ?>
            </td>
            <td style="font-weight:bold; color:<?= $cor_fluxo ?>;">
                <?= number_format($row['valor'], 2, ',', '.') ?> Kz
            </td>
            <td><?= date('d/m/Y H:i', strtotime($row['data_movimento'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (count($rows) > 5): ?>
<div style="text-align:center; margin-top:8px;">
    <button id="btnVerMais" onclick="verMais()" style="padding:6px 18px; cursor:pointer;">
        Ver mais (<?= count($rows) - 5 ?>)
    </button>
    <button id="btnVerMenos" onclick="verMenos()" style="padding:6px 18px; cursor:pointer; display:none;">
        Ver menos
    </button>
</div>  
<?php endif; ?>


              
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
          


<div class="modal-overlay" id="modalRetirar" style="display:none;">
    <div class="containerc">
        <div class="right">
            <span class="modal-fechar" onclick="fecharModalRetirar()">&times;</span>

            <h2>Retirar dinheiro</h2>

            <form method="POST">
                <input type="text" name="descricao" placeholder="Descrição" maxlength="30" required>
                <input type="number" step="0.01" name="valor" placeholder="Valor" required>
                <select name="fluxo" required>
                    <option value="">Observação</option>
                    <option value="saida">Saída</option>
                </select>
                <button type="submit" name="registar_fluxo">Registar</button>
            </form>
        </div>

            </div>
            <?php if (!empty($_GET['erro'])): ?>
            <div style="color: #f87171; font-size: 13px;">
                <?= htmlspecialchars($_GET['erro']) ?>
            </div>
        <?php endif; ?>
        </div>


        
                <script src="../views/js/saldo.js"></script>
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

                            function verMais() {
                        document.querySelectorAll('.linha-movimento').forEach(tr => tr.style.display = '');
                        document.getElementById('btnVerMais').style.display  = 'none';
                        document.getElementById('btnVerMenos').style.display = '';
                    }
                    function verMenos() {
                        document.querySelectorAll('.linha-movimento').forEach((tr, i) => {
                            tr.style.display = i >= 5 ? 'none' : '';
                        });
                        document.getElementById('btnVerMais').style.display  = '';
                        document.getElementById('btnVerMenos').style.display = 'none';
                    }
                  //Movimentos  pesquisa 
                        document.getElementById('pesquisaMovimentos').addEventListener('keyup', function(){

            let pesquisa = this.value.toLowerCase();
            let linhas = document.querySelectorAll('.linha-movimento');

            linhas.forEach(function(linha){

                let texto = linha.textContent.toLowerCase();

                if(texto.includes(pesquisa)){
                    linha.style.display = '';
                }else{
                    linha.style.display = 'none';
                }

            });

            let btnMais = document.getElementById('btnVerMais');
            let btnMenos = document.getElementById('btnVerMenos');

            if(pesquisa.length > 0){
                if(btnMais) btnMais.style.display = 'none';
                if(btnMenos) btnMenos.style.display = 'none';
            }

        });

        function abrirModalRetirar() {
    document.getElementById('modalRetirar').style.display = 'flex';
}

function fecharModalRetirar() {
    document.getElementById('modalRetirar').style.display = 'none';
}

// Fecha se clicar fora do card (no overlay)
document.getElementById('modalRetirar').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalRetirar();
    }
});

// Fecha com a tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalRetirar();
    }
});
        
            </script>
            </body>
</html>