
<?php

include("./configuracao/conexao.php");
 include_once("./models/clientes _models.php");
$pesquisa = $_GET['pesquisa'] ?? '';
$pesquisa = mysqli_real_escape_string($conn, $pesquisa);

$sql = "SELECT * FROM clientes
        WHERE nome LIKE '%$pesquisa%'
        OR telefone LIKE '%$pesquisa%'
        OR numero_bi LIKE '%$pesquisa%'
        OR endereco LIKE '%$pesquisa%'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<tr>";

        echo "<td>".$row['nome']."</td>";
        echo "<td>".$row['telefone']."</td>";
        echo "<td>".$row['numero_bi']."</td>";
        echo "<td>".$row['endereco']."</td>";
        echo "<td>".$row['criado_em']."</td>";

        echo "<td class='actions'>
            <a class='btn view' href='Atualizacao_do_Cliente.php?id=".$row['id_cliente']."'>
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil' viewBox='0 0 16 16'>
                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                </svg>
            </a>";

        if ($_SESSION['nivel'] === "admin") {
            echo "<a class='btn delete' href='clientes.php?excluir_id=".$row['id_cliente']."' onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
                    <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                    <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                </svg>
            </a>";
        }

        echo "</td>";

        echo "</tr>";
    }

} else {

    echo "<tr>
            <td colspan='6' style='text-align:center;'>
                Nenhum cliente encontrado
            </td>
          </tr>";
}
?>