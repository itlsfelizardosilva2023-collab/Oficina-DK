

 const sideMenu = document.querySelector("aside");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");
const themeToggler = document.querySelector(".theme-toggler");

menuBtn.addEventListener('click', ()=>{

    sideMenu.style.display = 'block';
})

closeBtn.addEventListener('click', ()=>{

    sideMenu.style.display = 'none';
})


if(localStorage.getItem("tema") === "dark"){
    document.body.classList.add("dark-theme-variables");

    
    themeToggler.querySelector('span:nth-child(1)').classList.remove('active'); 
    themeToggler.querySelector('span:nth-child(2)').classList.add('active'); 
}else{
   
    themeToggler.querySelector('span:nth-child(1)').classList.add('active'); 
    themeToggler.querySelector('span:nth-child(2)').classList.remove('active'); 
}


themeToggler.addEventListener('click', () => {

    document.body.classList.toggle('dark-theme-variables');

    
    if(document.body.classList.contains('dark-theme-variables')){
        themeToggler.querySelector('span:nth-child(1)').classList.remove('active'); 
        themeToggler.querySelector('span:nth-child(2)').classList.add('active'); 
        localStorage.setItem('tema', 'dark');
    }else{
        themeToggler.querySelector('span:nth-child(1)').classList.add('active'); 
        themeToggler.querySelector('span:nth-child(2)').classList.remove('active'); 
        localStorage.setItem('tema', 'light');
    }

    
});

 // loading--------------------------------
            const container = document.getElementById("container");
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
                container.appendChild(el);

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