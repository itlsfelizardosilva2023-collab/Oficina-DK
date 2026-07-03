

<?php
            session_start();
            include_once('./configuracao/config.php');
            include_once('./configuracao/conexao.php');
            require_once "./funcoes/elimina.php";

            ## Tratar exclusão se existir 
                    if(isset($_GET['excluir_id'])){
            $id = $_GET['excluir_id'];
            if(eliminarRegistro($conn, 'clientes', 'id_cliente', $id)){
                $_SESSION['msg'] = "Registro eliminado com sucesso!";
            } else {
                $_SESSION['msg'] = "Erro ao eliminar registro!";
            }
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }

           
          

            

            $pesquisa = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

            if($pesquisa == ''){
                $sql = "SELECT * FROM clientes";
                $stmt = $conn->prepare($sql);
            } else {
                $sql = "SELECT * FROM clientes
                        WHERE nome LIKE ?
                        OR telefone LIKE ?
                        OR endereco LIKE ?
                        OR criado_em LIKE ?
                        OR numero_bi LIKE ?";

                $stmt = $conn->prepare($sql);

                $pesquisa = "%".$pesquisa."%";

                $stmt->bind_param("sssss", 
                    $pesquisa, 
                    $pesquisa, 
                    $pesquisa, 
                    $pesquisa, 
                    $pesquisa 
                    
                );
            }

     
            $stmt->execute();
            $result = $stmt->get_result();


?>