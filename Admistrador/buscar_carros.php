<?php
session_start();
include("./configuracao/conexao.php");

$pesquisa = $_GET['pesquisa'] ?? '';
$termo = "%$pesquisa%";

$sql = "SELECT 
            c.id_carro,
            cli.nome AS cliente,
            m.nome AS modelo,
            c.matricula,
            c.cor,
            c.criado_em,
            s.status AS status_servico
        FROM carros c
        JOIN clientes cli ON c.id_cliente = cli.id_cliente
        JOIN modelos m ON c.id_modelo = m.id_modelo
        LEFT JOIN servicos s ON s.placa_carro = c.matricula
        WHERE cli.nome LIKE ? 
           OR c.matricula LIKE ? 
           OR m.nome LIKE ?
        ORDER BY c.criado_em DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $termo, $termo, $termo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $status = $row['status_servico'] ?? 'Sem serviço';
        $statusClass = '';
        if ($status !== 'Sem serviço') {
            $statusClass = match ($status) {
                'Cancelado' => 'status-vermelho',
                'Pendente' => 'status-castanho',
                'Em andamento' => 'status-amarelo',
                'Concluído' => 'status-verde',
                default => 'status-default'
            };
        }
?>
<tr>
    <td><?= htmlspecialchars($row['cliente']) ?></td>
    <td>
        <?php if ($status === 'Sem serviço'): ?>
            <?= htmlspecialchars($status) ?>
        <?php else: ?>
            <span class="status <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
        <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row['modelo']) ?></td>
    <td><?= htmlspecialchars($row['matricula']) ?></td>
    <td><?= htmlspecialchars($row['cor']) ?></td>
    <td><?= htmlspecialchars($row['criado_em']) ?></td>
    <td class="actions">
        <a class='btn view' href='atualizar_carro.php?id=<?= $row["id_carro"] ?>'>
            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
            </svg>
        </a>
        <?php if ($_SESSION['nivel'] === "admin") { ?>
        <a class='btn delete' href='carros.php?excluir_id=<?= $row["id_carro"] ?>' onclick="return confirm('Tem certeza que deseja deletar?')">
            <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
            </svg>
        </a>
        <?php } ?>
    </td>
</tr>
<?php
    endwhile;
else:
?>
<tr>
    <td colspan="7" style="text-align:center; color:red;">Nenhum carro encontrado</td>
</tr>
<?php endif; ?>