<?php
session_start();
if($_SESSION['nivel'] != "admin"){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Oficina Capapelo</title>

</head>
<body>

<div class="navbar"></div>

<div class="container">

    <aside class="sidebar">
        <h2 class="logo"><img src="" width="40" alt="Logo"></h2>
        <nav>
            <a href="ini.html"><img src="home.png" width="20" alt="">Início</a>
            <a href="#"><img src="car.png" width="25" alt="">Veículos</a>
            <a href="#"><img src="services.png" width="25" alt="">Serviços</a>
            <a href="funcio.html"><img src="employees.png" width="25" alt="">Funcionários</a>
            <a href="cliente.html"><img src="target.png" width="25" alt="">Clientes</a>
            <a href="gestaoconta.html"><img src="user.png" width="25" alt="">Gestão de contas</a>
            <a href="#"><img src="line-chart.png" width="25" alt="">Estatística</a>
        </nav>
    </aside>
            
    <!-- Assistente flutuante -->
<div id="assistente">
  <img src="home.png" width="100px" alt="">
</div>





    <main class="content">
        <header class="topbar">
            <h1>Oficina Capapelo</h1>
         <p> seja bem vindo  <?php echo htmlspecialchars($_SESSION['nome']); ?>
</p>
            <div class="dropdown">
                <button onclick="toggleDropdown()" class="dropbtn"><img src="blogger.png" width="26" alt=""><?php echo htmlspecialchars($_SESSION['nome']); ?></button>
                <div id="myDropdown" class="dropdown-content">
                    
                  
                   <a href="logout.php" style="margin-left:20px;">terminar sessão</a>
                </div>
            </div>

           
        </header>

        <section class="cards">
            <div class="card"><h4>Veículos</h4><p>24 registrados</p></div>
            <div class="card"><h4>Serviços Ativos</h4><p>8 em andamento</p></div>
            <div class="card"><h4>Faturamento</h4><p></p></div>
            <div class="card"><h4>Funcionários</h4><p>6 ativos</p></div>
        </section>
    </main>
</div>

<script>
function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i=0;i<dropdowns.length;i++){
            if(dropdowns[i].classList.contains('show')){
                dropdowns[i].classList.remove('show');
            }
        }
    }
}
// Clique no assistente abre alerta (pode substituir por chat real)
document.getElementById('assistente').addEventListener('click', function(){
    alert('Olá! Como posso te ajudar? 😎');
});
</script>
</body>
</html>