
<?php
session_start();
include("./configuracao/conexao.php");

if (!isset($_GET['id'])) {
    die("Contrato não encontrado");
}

$id_contrato = intval($_GET['id']);

// ================= CONTRATO + CLIENTE =================
$sql = "
SELECT 
    contratos.*,
    clientes.nome,
    clientes.numero_bi
FROM contratos
INNER JOIN clientes ON contratos.id_cliente = clientes.id_cliente
WHERE contratos.id_contrato = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_contrato);
$stmt->execute();
$res = $stmt->get_result();
$contrato = $res->fetch_assoc();

if (!$contrato) {
    die("Contrato não encontrado");
}

// ================= VIATURAS =================
$sql = "
SELECT
    carros.matricula,
    marcas.nome AS marca,
    modelos.nome AS modelo,
    contrato_carros.preco_viatura
FROM contrato_carros
INNER JOIN carros ON contrato_carros.id_carro = carros.id_carro
INNER JOIN modelos ON carros.id_modelo = modelos.id_modelo
INNER JOIN marcas ON modelos.id_marca = marcas.id_marca
WHERE contrato_carros.id_contrato = ?
";

$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id_contrato);
$stmt2->execute();
$viaturas = $stmt2->get_result();

$totalGeral = 0;

?>
<!DOCTYPE html>
<html lang="pt-AO">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contrato de Serviço – Domingos Kapapelo</title>

<link rel="stylesheet" href="..\views\css\fatura_contrato.css?v=10">
 <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
</head>
<body>

<!-- TOOLBAR -->
<div class="toolbar">
    <button class="btn btn-print" onclick="window.print()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Imprimir
    </button><a href="contratos.php"> 
        <button class="btn btn-pdf" >
        
       Voltar
    </button></a>

     <a href="Atualizar_contratos.php?id=<?= $contrato['id_contrato'] ?>">
    <button class="btn btn-print">
        Editar
    </button>
</a>


<a href="pagamentos_contratos.php?id_contrato=<?= $contrato['id_contrato'] ?>">
  <button class="btn btn-pdf">Adicionar Pagamento</button>
</a>
    
   
</div>

<!-- PAPER -->
<div class="paper">
    <div class="watermark">DOMINGOS KAPAPELO</div>
    <div class="content">

        <!-- HEADER -->
        <div class="header">
            <div class="logo-row">
                <div><span><img src="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png" alt="" width="90px"></span></div>
                <div>
                    <div class="company-name">Domingos Kapapelo</div>
                    <div class="company-sub">Oficina &amp; Serviços Automóveis</div>
                    <div class="company-details">
                        NIF: <strong>5417045870</strong><br>
                        Capalanga Vila, Luanda – Angola
                    </div>
                </div>
            </div>
            <div class="meta-block">
                <div class="meta-num">Contrato Nº <?= htmlspecialchars($contrato['numero_transacao']) ?></div>
                <div class="meta-date"><?= date("d \d\e F \d\e Y") ?></div>
            </div>
        </div>

        <!-- TITLE -->
        <div class="title-section">
            <h2>Contrato de Prestação de Serviço</h2>
           
        </div>

       

        <!-- BODY -->
        <div class="contract-body">
            <p>
                Eu, <strong><?= htmlspecialchars($contrato['nome']) ?></strong>, portador do BI/NIF n.º
                <strong><?= htmlspecialchars($contrato['numero_bi']) ?></strong>, doravante designado
                <em>Cliente</em>, celebro o presente contrato de prestação de serviços com a oficina
                <strong>Domingos Kapapelo</strong>, com NIF n.º <strong>5417045870</strong>, sediada
                em <strong>Capalanga Vila, Luanda – Angola</strong>, doravante designada <em>Prestadora de Serviço</em>.
            </p>
            <p>
                O presente contrato tem por objeto a realização de serviços  de manutenção, reparação
                e intervenção técnica  e mecânica nas viaturas abaixo identificadas, de acordo com as
                condições estabelecidas nas cláusulas seguintes, aceites por ambas as partes.
            </p>
        </div>

        <!-- VEHICLES -->
        <div class="sec-header"><span>Viaturas Abrangidas</span></div>

        <table class="vehicle-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Matrícula</th>
                    <th>Marca / Modelo</th>
                    <th>Preço (Akz)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            while ($v = $viaturas->fetch_assoc()):
                $totalGeral += $v['preco_viatura'];
            ?>
                <tr>
                    <td class="num"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($v['matricula']) ?></td>
                    <td><?= htmlspecialchars($v['marca']) ?> / <?= htmlspecialchars($v['modelo']) ?></td>
                    <td class="price"><?= number_format($v['preco_viatura'], 2, ',', '.') ?> Akz</td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-row">
            <div class="total-box">
                <span class="total-label">Total Geral</span>
                <span class="total-amount"><?= number_format($totalGeral, 2, ',', '.') ?> Akz</span>
            </div>
        </div>

        <!-- CLAUSES -->
        <div class="sec-header"><span>Cláusulas Contratuais</span></div>

        <div class="clauses">
            <ol>
                <li><strong>Objeto:</strong> A oficina compromete-se a executar os serviços de manutenção e reparação identificados com qualidade, profissionalismo e dentro dos padrões técnicos aplicáveis.</li>
                <li><strong>Pagamento:</strong> O pagamento será efectuado conforme acordado entre as partes e discriminado na fatura correspondente. O incumprimento dos prazos de pagamento implica a aplicação de juros de mora nos termos da legislação angolana.</li>
                <li><strong>Confidencialidade:</strong> Ambas as partes comprometem-se a manter reserva e confidencialidade sobre as informações partilhadas no âmbito do presente contrato.</li>
         
              
            </ol>
        </div>

        <!-- SIGNATURES -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-space"></div>
                <div class="sig-name">Domingos Kapapelo</div>
                <div class="sig-role">Prestador de Serviço</div>
            </div>
            <div class="sig-block">
                <div class="sig-space"></div>
                <div class="sig-name"><?= htmlspecialchars($contrato['nome']) ?></div>
                <div class="sig-role">Cliente</div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="paper-footer">
            <div class="footer-text">
                Documento gerado em <?= date("d/m/Y \à\s H:i") ?><br>
                Domingos Kapapelo – Oficina &amp; Serviços Automóveis · Luanda, Angola
            </div>
            <div class="footer-emblem">DK</div>
        </div>

    </div><!-- /content -->
</div><!-- /paper -->

<script>
function savePDF() {
    const prev = document.title;
    document.title = 'Contrato_<?= addslashes(htmlspecialchars($contrato['nome'])) ?>_<?= date('Y') ?>';
    window.print();
    document.title = prev;
}
</script>

</body>
</html>