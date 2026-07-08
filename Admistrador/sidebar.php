<?php
    include_once("permissoes.php");
$paginaAtual = basename($_SERVER['PHP_SELF']);
 
if (!isset($_SESSION['nivel']) || !in_array($_SESSION['nivel'], ['admin', 'usuario', 'tecnico'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
     <script>
        if (localStorage.getItem("tema") === "dark") {
            document.body
                ? document.body.classList.add("dark-theme-variables")
                : document.documentElement.setAttribute("data-theme", "dark");
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina D.K</title>
    <link rel="stylesheet" href="../views/css/sidebar.css?v=2.0">
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
</head>

<body>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../views/img/WhatsApp_Image_2026-02-27_at_12.49.27.1.png" width="63px" alt="">
                    <h3>Domingos Kapapelo</h3>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path
                                d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                           </svg>
                    </span>
                </div>
            </div>
      
            <nav class="nav-container">
                <div class="sidebar ul">
                    <?php if (temAcessoPagina('inicio_admin.php')): ?>
                    <a href="inicio_admin.php"  class="<?= ($paginaAtual == 'inicio_admin.php') ? 'active' : '' ?>"> 
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-house" viewBox="0 0 16 16">
                                <path
                                    d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
                            </svg>
                        </span>
                        <h3>Início</h3>
                    </a>
                    <?php endif; ?>
                  <?php if (temAcessoPagina('stok.php')): ?>
                <a href="funcionarios.php" class="<?= ($paginaAtual == 'funcionarios.php') ? 'active' : '' ?>">
                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                        </svg>
                                    </span>
                                        <h3>Funcionarios</h3>
                                    </a>
            
                <?php endif; ?>
                   
                    <?php if (temAcessoPagina('clientes.php')): ?>
<a href="clientes.php?i@misou38276@dshjkkk2dshs545h%%hdfldnmklpkyhdjhbdhn@kjkkkjnncj47hsdjjghdfn%mnbnhdf7,mnkncihio@knkl.-çkknlenojo89u36484hrf9v&jkfidf@jklsdf" class="<?= ($paginaAtual == 'clientes.php') ? 'active' : '' ?>"> 
    <span>
        <svg xmlns=" http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
        class="bi bi-person-vcard" viewBox="0 0 16 16">
        <path
            d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
        <path
            d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
        </svg>
        </span>
        <h3>Clientes</h3>
    </a>
<?php endif; ?>

<?php if (temAcessoPagina('servicos.php')): ?>
<a href="servicos.php"  class="<?= ($paginaAtual == 'servicos.php') ? 'active' : '' ?>" >
  <span> 
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-wrench-adjustable-circle-fill" viewBox="0 0 16 16">
    <path d="M6.705 8.139a.25.25 0 0 0-.288-.376l-1.5.5.159.474.808-.27-.595.894a.25.25 0 0 0 .287.376l.808-.27-.595.894a.25.25 0 0 0 .287.376l1.5-.5-.159-.474-.808.27.596-.894a.25.25 0 0 0-.288-.376l-.808.27z"/>
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m-6.202-4.751 1.988-1.657a4.5 4.5 0 0 1 7.537-4.623L7.497 6.5l1 2.5 1.333 3.11c-.56.251-1.18.39-1.833.39a4.5 4.5 0 0 1-1.592-.29L4.747 14.2a7.03 7.03 0 0 1-2.949-2.951M12.496 8a4.5 4.5 0 0 1-1.703 3.526L9.497 8.5l2.959-1.11q.04.3.04.61"/>
  </svg>
  </span>
        <h3>serviços</h3>
    </a>
<?php endif; ?>

<?php if (temAcessoPagina('carros.php')): ?>
<a href="carros.php"  class="<?= ($paginaAtual == 'carros.php') ? 'active' : '' ?>">
    <span>
        <svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
        class="bi bi-car-front-fill" viewBox="0 0 16 16">
        <path
            d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z" />
        </svg>
        </span>
        <h3>Automóveis</h3>
    </a>
<?php endif; ?>

<?php if (temAcessoPagina('stok.php')): ?>
<a href="contas.php" class="<?= ($paginaAtual == 'contas.php') ? 'active' : '' ?>">
    <span>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
            class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd"
                d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
        </svg>
    </span>
    <h3>Contas</h3>
</a>
<?php endif; ?>

<?php if (temAcessoPagina('contratos.php')): ?>
<a href="contratos.php" class="<?= ($paginaAtual == 'contratos.php') ? 'active' : '' ?>"  >
    <span>
        <svg xmlns=" http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
        class="bi bi-file-earmark-medical-fill" viewBox="0 0 16 16">
        <path
            d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-3 2v.634l.549-.317a.5.5 0 1 1 .5.866L7 7l.549.317a.5.5 0 1 1-.5.866L6.5 7.866V8.5a.5.5 0 0 1-1 0v-.634l-.549.317a.5.5 0 1 1-.5-.866L5 7l-.549-.317a.5.5 0 0 1 .5-.866l.549.317V5.5a.5.5 0 1 1 1 0m-2 4.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1m0 2h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1" />
        </svg>
        </span>
        <h3>contratos</h3>
    </a>
<?php endif; ?>

<?php if (temAcessoPagina('stok.php')): ?>
<a href="estatistica.php" class="<?= ($paginaAtual == 'estatistica.php') ? 'active' : '' ?>">
    <span>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
            class="bi bi-bar-chart-line-fill" viewBox="0 0 16 16">
            <path
                d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1z" />
        </svg>
    </span>
    <h3>Estatística</h3>
</a>
<?php endif; ?>

<?php if (temAcessoPagina('stok.php')): ?>
<a href="stok.php?isou38276@dshjkkk2dshs545h%%hdfldnmklpkyhdjhbdhn@kjkkkjnncj47hsdjjghdfn%mnbnhdf7,mnkncihio@knkl.-çkknlenojo89u36484hrf9v&jkfidf@jklsdf"  class="<?= ($paginaAtual == 'stok.php') ? 'active' : '' ?>" >
    <span>
        <svg xmlns=" http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
        class="bi bi-stack" viewBox="0 0 16 16">
        <path
            d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
        <path
            d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
        </svg>
        </span>
        <h3>Stock</h3>
    </a>
<?php endif; ?>

<?php if (temAcessoPagina('stok.php')): ?>
<a href="saldo.php" class="<?= ($paginaAtual == 'saldo.php') ? 'active' : '' ?>">
    <span>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
            <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
            <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
        </svg>
    </span>
    <h3>Capital Empresarial</h3>
</a>
<?php endif; ?>
                </div>
            </nav>
        </aside>
   