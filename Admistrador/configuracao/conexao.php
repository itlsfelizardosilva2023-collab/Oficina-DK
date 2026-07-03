<?php

$conn = new mysqli("localhost","root","","oficina_capapelo");

if($conn->connect_error){
die("Erro de conexão: ".$conn->connect_error);
}

?>