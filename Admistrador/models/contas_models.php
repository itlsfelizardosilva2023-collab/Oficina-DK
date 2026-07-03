
<?php
 session_start();
 include_once('./configuracao/config.php');


  # Verifica se está logado e é admin
   if(!isset($_SESSION['nivel']) || $_SESSION['nivel'] != "admin"){
    header("Location: login.php");
    exit();
  }

    $sql = "SELECT * FROM usuarios ORDER BY id";

    $result = $conexao->query($sql);

?>