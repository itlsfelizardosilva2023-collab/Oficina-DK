
<?php

include("./configuracao/conexao.php");

$pesquisa = $_GET['pesquisa'] ?? '';
$pesquisa = mysqli_real_escape_string($conn, $pesquisa);

$sql = "
SELECT 
    contratos.id_contrato,
    contratos.numero_transacao,
    contratos.data_inicio,
    contratos.data_fim,
    contratos.total_geral,
    clientes.nome
FROM contratos
INNER JOIN clientes
ON contratos.id_cliente = clientes.id_cliente
WHERE contratos.numero_transacao LIKE '%$pesquisa%'
OR clientes.nome LIKE '%$pesquisa%'
ORDER BY contratos.id_contrato DESC
";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {

    while ($c = $res->fetch_assoc()) {
?>

<tr>

    <td>
        <span class="badge"><?= htmlspecialchars($c['numero_transacao']) ?></span>
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

} else {
?>

<tr>
    <td colspan="6" style="text-align:center; padding:20px;">
        Nenhum contrato encontrado
    </td>
</tr>

<?php } ?>