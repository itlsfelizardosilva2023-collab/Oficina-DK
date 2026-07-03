<?php
  
  include_once("./models/contas_models.php");

?>
<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K </title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
    <link rel="stylesheet" href="../views/css/contas.css?v=1.2">
    
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
                          <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                             </svg>
                        </span>
                    <div class="midlle">
                            
                                <h3>TOTAL DE USUÁRIOS</h3>
                        <h1> 
                            <p class="valor">
                               <?php
                                    $sql = "SELECT COUNT(*) AS total FROM usuarios";
                                    $qtd = mysqli_query($conexao, $sql);
                                    $row = mysqli_fetch_assoc($qtd);
                                    echo $row['total'];
                                ?>
                            </p>
                        </h1>
                            
                 </div>      
                 </div>
               <!--------------------------------------->
                <div class="sales">
                        <span>
                             <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                             </svg>
                        </span>
                    <div class="midlle">
                    <h3>TOTAL DE USUÁRIOS O NIVEL DE ACESSO BASE</h3>
                        <h1>
                            <p class="valor">
                                     <?php
                                          $sql = "SELECT COUNT(*) AS total FROM usuarios Where nivel ='usuario'";
                                          $qtd = mysqli_query($conexao, $sql);
                                          $row = mysqli_fetch_assoc($qtd);
                                          echo $row['total'];
                                     ?>
                            </p>          
                       </h1>
                            
                    </div>
                       
                    </div> 
                   <!--------------------------------------->
                  <div class="sales">
                        <span>
                           <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                              <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                             </svg>
                        </span>
                        <div class="midlle">
                            
                            <h3> TOTAL DE ADMIN </h3>
                            <h1>
                                <p class="valor">
                                    <?php
                                        $sql = "SELECT COUNT(*) AS total FROM usuarios Where nivel ='admin'";
                                        $qtd = mysqli_query($conexao, $sql);
                                        $row = mysqli_fetch_assoc($qtd);
                                        echo $row['total'];
                                    ?>
                                </p>
                            </h1>
                            
                        </div>
                    </div> 
            
                 </div>
                
       
            <!------------------recentes-------------------->

            <div class="Recent-Order">
                <h2>Informações Dos Usuário</h2>

                <div class="btn-add">
                    <a href="criar_conta.php">
                        <button class="btn-add">
                           <h3>Adicionar</h3> 
                        </button>
                    </a>
                </div>

                   <div class="btn-rpc">
                    <a href="recuperacao.php">
                        <button class="btn-add">
                           <h3>Recuperação</h3> 
                        </button>
                    </a>
                </div>

        <div class="table-container">

<table  class="modern-table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Nível de acesso</th>
            <th>Status</th> 
            <th>Permições</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>

<?php
while($usuarios = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td class='direita'>".htmlspecialchars($usuarios['nome'])."</td>";
    echo "<td>".htmlspecialchars($usuarios['email'])."</td>";
    echo "<td>".htmlspecialchars($usuarios['nivel'])."</td>";
   
    if($usuarios['is_master_admin'] == 6){ 
        echo "<td><span class='Protegido'>Protegido</span></td>";
        echo "<td><span class='Protegido'>Protegido</span></td>";
        echo "<td><span class='Protegido'>Protegido</span></td>";
    } else {
        $id      = $usuarios['id'];
        $ativo   = $usuarios['ativo'];
        $badge   = $ativo ? 'ativo' : 'inativo';
        $label   = $ativo ? 'Ativo' : 'Inativo';
        $checked = $ativo ? 'checked' : '';

        // Coluna Status — vem primeiro
        echo "<td class='coluna-status'>
                <span class='saving' id='saving-$id'></span>
                <span class='badge $badge' id='badge-$id'>$label</span>
                <label class='toggle'>
                    <input type='checkbox' $checked onchange='toggleStatus(this, $id)'>
                    <span class='slider'></span>
                </label>
              </td>";

           if ($usuarios['nivel'] === 'admin') {
    echo "<td class='acoes'>
            <span class='Protegido'>Protegido</span>
          </td>";
} else {
    echo "<td class='acoes'>
        <a href='#'
           class='btn view btn-gerir-permissoes'
           data-id='{$usuarios['id']}'
           data-nome='" . htmlspecialchars($usuarios['nome']) . "'
          >
            Gerir
        </a>
      </td>";
}

        // Coluna Ações — fica por último
        echo "<td class='acoes'>
                <a class='btn view' href='Atualizacao_da_Conta.php?id=$id'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                        <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                    </svg>
                </a>

                <a class='btn delete'
                   href='./models/deletar_conta_models.php?id=$id'
                   onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                        <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                        <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                    </svg>
                </a>
              </td>";

              
    }

    echo "</tr>";
}
?>

    </tbody>
</table>
<?php  require_once "paginas.php";?>

<div class="overlay-modal" id="modalPermissoes">
  <div class="caixa-modal">
    <div class="cabecalho-modal">
      <h3>Permissões de <span id="nomeUsuarioPermissao"></span></h3>
      <button type="button" class="fechar-modal" id="fecharModalPermissoes">&times;</button>
    </div>
    <div class="corpo-modal">
      <input type="hidden" id="idUsuarioPermissao">
      <div id="listaPermissoes">
        <?php foreach ($paginas_sistema as $arquivo => $nome): ?>
          <label class="item-permissao">
            <input type="checkbox" class="checkbox-permissao" value="<?php echo $arquivo; ?>">
            <span><?php echo $nome; ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="rodape-modal">
      <button type="button" class="btn-cancelar" id="btnCancelarPermissoes">Cancelar</button>
      <button type="button" class="btn-salvar" id="btnSalvarPermissoes">Salvar</button>
    </div>
  </div>
</div>
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

    <script src="../views/js/contas.js"></script>

 <script id="fade-js">
     document.addEventListener("DOMContentLoaded", function() {
   
    document.body.style.opacity = 0;
    setTimeout(() => {
        document.body.style.opacity = 1;
    }, 50);

    
    const links = document.querySelectorAll("a[href]");
    
    links.forEach(link => {
        link.addEventListener("click", function(e) {
            const href = link.getAttribute("href");

            
            if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) {
                return;
            }

            e.preventDefault();
            document.body.classList.add("fade-out"); 

            
            setTimeout(() => {
                window.location.href = href;
            }, 100); 
        });
    });
   });



    let tempoInatividade = 0; // Contador em milissegundos
    const tempoMaximo = 10000000000* 60 * 1000; // 5 minutos = 300000 ms

    function resetTimer() {
        tempoInatividade = 0; // Reseta o contador quando houver atividade
    }

    // Incrementa o contador a cada segundo
    setInterval(() => {
        tempoInatividade += 1000;
        if (tempoInatividade >= tempoMaximo) {
            alert("Você ficou inativo por 5 minutos. Voltando para o login.");
            window.location.href = "logout.php"; // Redireciona para login
        }
    }, 1000);

    // Eventos que indicam atividade do usuário
    document.addEventListener("mousemove", resetTimer);
    document.addEventListener("keypress", resetTimer);
    document.addEventListener("scroll", resetTimer);
    document.addEventListener("click", resetTimer);

function toggleStatus(el, id) {
  const status = el.checked ? 1 : 0;
  const badge  = document.getElementById('badge-' + id);
  const saving = document.getElementById('saving-' + id);

  saving.style.display = 'inline';

  fetch('./models/toggle_user.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id: id, status: status })
  })
  .then(res => res.json())
  .then(data => {
    saving.style.display = 'none';

    if (!data.success) throw new Error();

    badge.textContent = status === 1 ? 'Ativo' : 'Inativo';
    badge.className   = status === 1 ? 'badge ativo' : 'badge inativo';
  })
  .catch(() => {
    el.checked = !el.checked;
    saving.style.display = 'none';
    alert('Erro ao atualizar. Tenta novamente.');
  });
}

const modalPermissoes = document.getElementById('modalPermissoes');

function abrirModalPermissoes() {
    modalPermissoes.classList.add('ativo');
}

function fecharModalPermissoes() {
    modalPermissoes.classList.remove('ativo');
}

// Abrir modal ao clicar em "Gerir"
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-gerir-permissoes');
    if (!btn) return;

    e.preventDefault();

    const idUsuario = btn.dataset.id;
    const nome = btn.dataset.nome;

    document.getElementById('idUsuarioPermissao').value = idUsuario;
    document.getElementById('nomeUsuarioPermissao').textContent = nome;

    document.querySelectorAll('.checkbox-permissao').forEach(cb => cb.checked = false);

    fetch(`buscar_permissoes.php?id_usuario=${idUsuario}`)
        .then(res => res.json())
        .then(data => {
            if (data.sucesso) {
                data.permissoes.forEach(pagina => {
                    const cb = document.querySelector(`.checkbox-permissao[value="${pagina}"]`);
                    if (cb) cb.checked = true;
                });
            }
        })
        .catch(err => console.error('Erro ao buscar permissões:', err));

    abrirModalPermissoes();
});

// Fechar modal (botão X, botão Cancelar, ou clique fora da caixa)
document.getElementById('fecharModalPermissoes').addEventListener('click', fecharModalPermissoes);
document.getElementById('btnCancelarPermissoes').addEventListener('click', fecharModalPermissoes);

modalPermissoes.addEventListener('click', function (e) {
    if (e.target === modalPermissoes) {
        fecharModalPermissoes();
    }
});

// Salvar permissões selecionadas
document.getElementById('btnSalvarPermissoes').addEventListener('click', function () {
    const idUsuario = document.getElementById('idUsuarioPermissao').value;
    const paginasSelecionadas = Array.from(document.querySelectorAll('.checkbox-permissao:checked'))
        .map(cb => cb.value);

    fetch('salvar_permissoes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_usuario: idUsuario, paginas: paginasSelecionadas })
    })
    .then(res => res.json())
    .then(data => {
        if (data.sucesso) {
            alert('Permissões atualizadas com sucesso!');
            fecharModalPermissoes();
        } else {
            alert('Erro ao salvar: ' + (data.mensagem || ''));
        }
    })
    .catch(err => console.error('Erro ao salvar permissões:', err));
});

</script>
    </body>
</html>