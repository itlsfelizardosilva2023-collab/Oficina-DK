
<?php
include("login_models.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Oficina D.K</title>
    <link rel="icon" type="image/jpeg"  href="..\views\img\WhatsApp_Image_2026-02-27_at_12.49.27.1.png">
  </head>
    <link rel="stylesheet" href=".\views\css\login.css?v=1.0">
  </head>

  <body>

<div class="bg1"></div>
<div class="bg2"></div>

  <div class="container">



 <div class="left">

   
 </div>

 <div class="right">

 <form action="" method="POST"> <!-- action vazio = mesma página -->
    
    <h3>Seja bem vindo de volta!</h3>
    <h2>Iniciar sessão</h2>

 
 <input type="email" name="email" placeholder="Email" required>
  
 <input type="password" name="senha" placeholder="Senha" required>
 <button type="submit">Entrar</button>
     <a  class="recuperar"  href="Admistrador\recuperacao_conta.php"> Esquecia a Senha </a>
 </form>

 <?php
 if($erro != ""){
    echo "<p class='erro'>$erro</p>";
 }
 ?>

 </div>
 </div>
      <!------------------loanding--------------------->
        <div id="loading">
            <div class="containerl" id="containerl"></div>
        </div>
 </div>
 </body>
 <script>

       document.addEventListener("click", function(){

       let erro = document.querySelector(".erro");

          
     if(erro){
         erro.style.display = "none";
      }


     });

      // loading--------------------------------
const containerl = document.getElementById("containerl");
const loading = document.getElementById("loading");

const vines = [];
const total = 60;

// curva infinito
function infinito(t) {
    const a = 140;
    const x = (a * Math.cos(t)) / (1 + Math.sin(t) * Math.sin(t));
    const y = (a * Math.sin(t) * Math.cos(t)) / (1 + Math.sin(t) * Math.sin(t));
    return { x, y };
}

// criar vinhas
for (let i = 0; i < total; i++) {
    const el = document.createElement("div");
    el.className = "vinha";
    containerl.appendChild(el);

    vines.push({
        el,
        t: (i / total) * Math.PI * 2
    });
}

let tempo = 0;

// animação
function animar() {
    tempo += 0.06;

    const deslocamentoY = Math.sin(tempo) * 40;

    vines.forEach(v => {
        v.t += 0.015;

        const p = infinito(v.t);

        const cx = 200;
        const cy = 100 + deslocamentoY;

        v.el.style.left = (cx + p.x) + "px";
        v.el.style.top = (cy + p.y) + "px";

        const scale = 1 + Math.sin(v.t * 3) * 0.6;
        v.el.style.transform = `scale(${scale})`;
    });

    requestAnimationFrame(animar);
}

animar();

/* esconder loading ao carregar */
window.addEventListener("load", () => {
    setTimeout(() => {
        loading.classList.add("hidden");
    }, 1000);
});

/* mostrar loading ao clicar em links */
const links = document.querySelectorAll("a[href]");

links.forEach(link => {
    link.addEventListener("click", function(e) {
        const href = link.getAttribute("href");

        if (href.startsWith("http") || href.startsWith("#") || href.startsWith("javascript")) {
            return;
        }

        e.preventDefault();

        loading.classList.remove("hidden");

        setTimeout(() => {
            window.location.href = href;
        }, 800);
    });
});






const imagens1 = [
     "./views/img/img3.jpg",
   "./views/img/img2.avif",
   "./views/img/img1.webp",
   "./views/img/img44.jpg",
   "./views/img/img5.jpg",
];

let indice1 = 0;

const bg1 = document.querySelector(".bg1");
const bg2 = document.querySelector(".bg2");

let ativa = true;

setInterval(() => {

    indice1 = (indice1 + 1) % imagens1.length;

    if (ativa) {
        bg2.style.backgroundImage = `url('${imagens1[indice1]}')`;
        bg2.style.opacity = "1";
        bg1.style.opacity = "0";
    } else {
        bg1.style.backgroundImage = `url('${imagens1[indice1]}')`;
        bg1.style.opacity = "1";
        bg2.style.opacity = "0";
    }

    ativa = !ativa;

}, 3000);

            </script>
            </body>
</html>
