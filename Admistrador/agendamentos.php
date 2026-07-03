
<?php
session_start();
include_once("./configuracao/conexao.php");
include_once("./models/agendamento_converter.php");

// Processa conversões automáticas vencidas sempre que a página carrega
// (complementa o cron job - garante que a lista nunca mostra um agendamento
// "atrasado" como se ainda estivesse pendente, mesmo que o cron não tenha corrido ainda)
processarAgendamentosVencidos($conn);

// Filtro simples por status (opcional, via querystring ?filtro=Agendado)
$filtro = $_GET['filtro'] ?? 'Agendado';
$filtrosValidos = ['Agendado', 'Convertido', 'Cancelado', 'Todos'];
if (!in_array($filtro, $filtrosValidos)) {
    $filtro = 'Agendado';
}

if ($filtro === 'Todos') {
    $sql = "SELECT * FROM agendamentos ORDER BY data_agendada ASC, hora_agendada ASC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM agendamentos WHERE status_agendamento = ? ORDER BY data_agendada ASC, hora_agendada ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filtro);
}

$stmt->execute();
$agendamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
 <head>
    <meta charset="UTF-8">
    
   <title>Oficina D.K</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
   <link rel="stylesheet" href="../views/css/agendamentos.css">
  </head>
  
   <body>
    
    <div class="container">
         <?php include_once('sidebar.php');?>
        <!-----------------fim do sidebar---------------------->

<div class="containerc">

        <div class="section-header">Agendamentos</div>

<div class="cards-agendamentos">
  <!-- Serviços -->
    <a href="servicos.php" class="card-agendamento servicos">
        <div class="info">
            <span>Serviços</span>
            <h2>Abrir</h2>
        </div>

        <div class="icone">
           <svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"class="bi bi-car-front-fill" viewBox="0 0 16 16">
            <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
            </svg>
        </div>
    </a>

</div>

        <a href="novo_agendamento.php" class="btn-save" style="display:inline-block;margin-bottom:15px;text-decoration:none;">+ NOVO AGENDAMENTO</a>

        <div class="filtros">
            <a href="?filtro=Agendado"   class="<?= $filtro === 'Agendado'   ? 'ativo' : '' ?>">Pendentes</a>
            <a href="?filtro=Convertido" class="<?= $filtro === 'Convertido' ? 'ativo' : '' ?>">Convertidos</a>
            <a href="?filtro=Cancelado"  class="<?= $filtro === 'Cancelado'  ? 'ativo' : '' ?>">Cancelados</a>
            <a href="?filtro=Todos"      class="<?= $filtro === 'Todos'      ? 'ativo' : '' ?>">Todos</a>
        </div>

<div class="servicos-grid">
<?php if (empty($agendamentos)): ?>
    <p class="sem-resultado">Nenhum agendamento encontrado.</p>
<?php else: ?>
    <?php foreach ($agendamentos as $ag): ?>
        <?php
            // Gera as iniciais do nome do cliente
            $partes = explode(' ', trim($ag['nome_cliente']));
            $iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

            // Define classe do status
            $classeBadge = [
                'Agendado'   => 'status-pendente',
                'Convertido' => 'status-concluido',
                'Cancelado'  => 'status-cancelado',
            ][$ag['status_agendamento']] ?? 'status-pendente';

            $dataFormatada = date('d/m/Y', strtotime($ag['data_agendada']));
            $horaFormatada = date('H:i', strtotime($ag['hora_agendada']));
        ?>
        <div class="servico-card">

            <div class="servico-header">
                <div class="avatar"><?= $iniciais ?></div>
                <div class="servico-info">
                    <h4><?= htmlspecialchars($ag['nome_cliente']) ?></h4>
                    <span class="placa"><?= htmlspecialchars($ag['placa_carro']) ?></span><br>
                    <span class="status-badge <?= $classeBadge ?>"><?= htmlspecialchars($ag['status_agendamento']) ?></span>
                </div>
            </div>

            <div class="servico-detalhes">
                <p><strong>Modelo:</strong> <?= htmlspecialchars($ag['modelo_carro']) ?></p>
                <p><strong>Serviço:</strong> <?= htmlspecialchars($ag['descricao_servico']) ?></p>
                <p><strong>Data:</strong> <?= $dataFormatada ?> às <?= $horaFormatada ?></p>
            </div>

            <div class="servico-acoes">
                <?php if ($ag['status_agendamento'] === 'Agendado'): ?>
                    <form action="./models/ativar_agendamento.php" method="POST" style="flex:1;">
                        <input type="hidden" name="id_agendamento" value="<?= $ag['id_agendamento'] ?>">
                        <button type="submit" class="btn-acao btn-factura" style="width:100%;border:none;cursor:pointer;"
                                onclick="return confirm('Ativar este agendamento agora e gerar o serviço?')">
                            Ativar agora
                        </button>
                    </form>
                    <form action="./models/cancelar_agendamento.php" method="POST" style="flex:0 0 38px;">
                        <input type="hidden" name="id_agendamento" value="<?= $ag['id_agendamento'] ?>">
                        <button type="submit" class="btn-acao btn-deletar" style="width:100%;border:none;cursor:pointer;"
                                onclick="return confirm('Cancelar este agendamento?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg>
                        </button>
                    </form>
                <?php elseif ($ag['status_agendamento'] === 'Convertido'): ?>
                    <a class="btn-acao btn-editar" href="editar_servico.php?id=<?= $ag['id_servico_gerado'] ?>" style="width:100%;">
                        Ver serviço #<?= $ag['id_servico_gerado'] ?>
                    </a>
                <?php else: ?>
                    <span class="btn-acao" style="width:100%;color:#8a8f98;justify-content:center;">—</span>
                <?php endif; ?>
            </div>

        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

  
</div>

           
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
          <script>
document.addEventListener("DOMContentLoaded", function () {
    document.body.style.opacity = 0;
    setTimeout(() => { document.body.style.opacity = 1; }, 50);
});
</script>
 </body>
</html>