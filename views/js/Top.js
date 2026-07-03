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
