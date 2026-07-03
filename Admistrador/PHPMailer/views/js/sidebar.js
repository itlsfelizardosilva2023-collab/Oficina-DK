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