<?php

include("./configuracao/conexao.php");

$pesquisa = trim($_GET['pesquisa'] ?? '');
$like = "%$pesquisa%";
$mesAtual = (int) date('n');
$anoAtual = (int) date('Y');

// O status apresentado é o do pagamento do mês corrente (pagamentos_contrato),
// não o contratos.status (que só reflete o pagamento no ato da assinatura).
$sql = "
SELECT 
    contratos.id_contrato,
    contratos.numero_contrato,
    contratos.numero_transacao,
    contratos.data_inicio,
    contratos.data_fim,
    contratos.total_geral,
    pagamentos_contrato.status AS status_mes,
    clientes.nome
FROM contratos
INNER JOIN clientes
    ON contratos.id_cliente = clientes.id_cliente
LEFT JOIN pagamentos_contrato
    ON pagamentos_contrato.contrato_id = contratos.id_contrato
    AND pagamentos_contrato.mes = ?
    AND pagamentos_contrato.ano = ?
WHERE contratos.numero_contrato LIKE ?
OR clientes.nome LIKE ?
ORDER BY contratos.id_contrato DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $mesAtual, $anoAtual, $like, $like);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {

    while ($c = $res->fetch_assoc()) {


    
?>

<tr>

    <td>
        <span class="badge"><?= htmlspecialchars($c['numero_contrato'] ?? 'N/D') ?></span>
    </td>

    <td class="client">
        <?= htmlspecialchars($c['nome']) ?>
    </td>

    <td>
        <?= date('d/m/Y', strtotime($c['data_inicio'])) ?>
    </td>

    <td>
        <?= date('d/m/Y', strtotime($c['data_fim'])) ?>
    </td>

    <td class="price">
        <?= number_format($c['total_geral'],2,",",".") ?> Akz
    </td>

    <td>
        <?php
            // Se não houver registo de pagamento para o mês atual, trata como pendente
            $statusMes = $c['status_mes'] ?? 'pendente';

            $statusClasses = [
                'pago'      => 'badge-pago',
                'pendente'  => 'badge-pendente',
                'cancelado' => 'badge-cancelado'
            ];
            $statusLabels = [
                'pago'      => 'Pago',
                'pendente'  => 'Pendente',
                'cancelado' => 'Cancelado'
            ];
            $classeStatus = $statusClasses[$statusMes] ?? 'badge-pendente';
            $labelStatus  = $statusLabels[$statusMes] ?? ucfirst($statusMes);
        ?>
        <span class="badges <?= $classeStatus ?>"><?= $labelStatus ?></span>
    </td>

    <td class="actions">
        <a class="btn view" href="fatura_contrato.php?id=<?= $c['id_contrato'] ?>">
            Ver
        </a>

        <a class="btn delete"
           href="eliminar_contrato.php?id=<?= $c['id_contrato'] ?>"
           onclick="return confirm('Eliminar contrato?')">
           Eliminar
        </a>
    </td>

</tr>

<?php
    }

    $stmt->close();

} else {
?>

<tr>
    <td colspan="7" style="text-align:center; padding:20px;">
        Nenhum contrato encontrado
    </td>
</tr>

<?php } ?>