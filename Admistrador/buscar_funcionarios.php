
<?php

include("./configuracao/conexao.php");


$pesquisa = trim($_GET['pesquisa'] ?? '');

$sql = "
    SELECT 
        f.id_funcionario,
        f.nome,
        f.email,
        f.endereco,
        f.telefone,
        c.nome_cargo,
        s.nome_setor
    FROM funcionarios f
    LEFT JOIN cargos c ON c.id_cargo = f.id_cargo
    LEFT JOIN setores s ON s.id_setor = c.id_setor
    WHERE f.nome LIKE ?
       OR f.email LIKE ?
       OR c.nome_cargo LIKE ?
       OR s.nome_setor LIKE ?
";

$stmt = $conn->prepare($sql);
$like = "%{$pesquisa}%";
$stmt->bind_param("ssss", $like, $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['endereco']) . "</td>";
        echo "<td>" . htmlspecialchars($row['telefone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nome_cargo'] ?? '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['nome_setor'] ?? '—') . "</td>";

        echo "<td class='acoes'>
           <a class='btn-editar' href='Atualizacao_funcionario.php?id=" . (int)$row['id_funcionario'] . "'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                        <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                    </svg>
                </a>

                <a class='btn-deletar' href='funcionarios.php?excluir_id=" . (int)$row['id_funcionario'] . "' onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                        <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                        <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                    </svg>
                </a>
        </td>";

        echo "</tr>";
    }

} else {

    echo "<tr>
            <td colspan='7' style='text-align:center;'>
                Nenhum funcionário encontrado
            </td>
          </tr>";
}
?>
