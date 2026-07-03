<?php
function renderizarServicoCard($row) {
    $partes = explode(' ', trim($row['nome_cliente']));
    $iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));

    switch ($row['status']) {
        case 'andamento':
            $status_classe = 'status-andamento';
            $status_texto = 'Em Andamento';
            break;
        case 'concluido':
            $status_classe = 'status-concluido';
            $status_texto = 'Concluído';
            break;
        default:
            $status_classe = 'status-pendente';
            $status_texto = 'Pendente';
            break;
    }

    $html  = "<div class='servico-card'>";
    $html .= "<div class='servico-header'>";
    $html .= "<div class='avatar'>{$iniciais}</div>";
    $html .= "<div class='servico-info'>";
    $html .= "<h4>" . htmlspecialchars($row['nome_cliente']) . "</h4>";
    $html .= "<span class='placa'>" . htmlspecialchars($row['placa_carro']) . "</span><br>";
    $html .= "<span class='status-badge {$status_classe}'>{$status_texto}</span>";
    $html .= "</div></div>";

    $html .= "<div class='servico-detalhes'>";
    $html .= "<p><strong>Endereço:</strong> " . htmlspecialchars($row['endereco']) . "</p>";
    $html .= "<p><strong>Modelo:</strong> " . htmlspecialchars($row['modelo_carro']) . "</p>";
    $html .= "</div>";

    $html .= "<div class='servico-acoes'>";
    $html .= "<a class='btn-acao btn-editar' href='editar_servico.php?id={$row['id']}'>
                <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'>
                    <path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168z'/>
                </svg> Editar
              </a>";

    $html .= "<a class='btn-acao btn-factura' href='factura.php?id={$row['id']}' target='_blank'>Factura</a>";

    if ($_SESSION['nivel'] === 'admin') {
        $html .= "<a class='btn-acao btn-deletar' href='models/eliminar_servico.php?id={$row['id']}' onclick=\"return confirm('Tem certeza que deseja deletar?')\">
                    <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='currentColor' viewBox='0 0 16 16'>
                        <path d='M6.5 1h3a1 1 0 0 1 1 1V3h4v1H1V3h4V2a1 1 0 0 1 1-1z'/>
                        <path d='M2.5 5h11l-.5 8.5A2 2 0 0 1 11 15H5a2 2 0 0 1-2-1.5L2.5 5z'/>
                    </svg>
                  </a>";
    }

    $html .= "</div></div>"; // fecha servico-acoes e servico-card
    return $html;
}