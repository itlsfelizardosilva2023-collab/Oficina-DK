


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

            // gráficos 
            const totalCarros = 20;   // TOTAL de carros
            const finalizados = 8;    // FINALIZADOS

            const percent = Math.round((finalizados / totalCarros) * 100);

            // atualizar texto
            document.getElementById("percent-carros").innerText = percent + "%";

            // calcular círculo
            const circle = document.getElementById("progress-carros");
            const radius = 30;
            const circumference = 2 * Math.PI * radius;

            const offset = circumference - (percent / 100) * circumference;

            circle.style.strokeDashoffset = offset;

            //------------------------assis----------------------------------------
            //----------------------------------------------------------------
            const knowledgeBase = {
                cadastro: {
                keywords: ['cadastr', 'registr', 'conta', 'criar conta', 'novo cliente', 'como me cadastr'],
                answer: `Para se cadastrar no sistema da <strong>Oficina DK</strong>, siga os passos:<br><br>
                1️⃣ Acesse o sistema e Navega pelo menu  a esquerda ate a opção desejada <strong>" clica na opção "</strong><br>
                2️⃣ cheando a tela desejada acima da tabela de registro e tem um botão de Adicionar <em>preencha os campos  </em><br>
                4️⃣ Clique em <strong>"cadastrar"</strong> para concluir<br><br>
                Pronto!`
                },
                orcamento: {
                keywords: ['orçamento', 'orcamento', 'preço', 'preco', 'valor', 'quanto custa', 'consultar orçamento', 'ver orçamento'],
                answer: `Para consultar seu <strong>orçamento</strong> na Oficina DK:<br><br>
                1️⃣ Faça login com seu email e senha<br>
                2️⃣ Acesse o menu <strong>" Serviços"</strong><br>
                3️⃣ Selecione a ordem de serviço desejada<br>
                4️⃣ Clique em <strong>"Ver Orçamento"</strong><br><br>
                O orçamento detalha peças, mão de obra e prazo estimado. ✅<br>
                Você também pode <strong>aprovar ou recusar</strong> diretamente pelo sistema.`
                },

                     desenvolver: {
                keywords: ['cria', 'desenvolve'],
                answer: `Fui Criado por <strong>Felizardo Silva </strong> Estudante Finalista do IPPLS:<br><br>
                 com o objetivo de ser um suporte tecnico ao sistema <br>
                   <br>`
                },
                status: {
                keywords: ['status', 'situação', 'onde está', 'meu carro', 'veículo', 'veiculo', 'andamento', 'pronto'],
                answer: `Para verificar o <strong>status do seu veículo</strong>:<br><br>
                1️⃣ Acesse o sistema com seu login<br>
                2️⃣ Vá em <strong>" Serviços"</strong> no menu principal<br>
                3️⃣ Você verá o status atual:<br>
                &nbsp;&nbsp;&nbsp;🔵 <em>Em manutenção</em><br>
                &nbsp;&nbsp;&nbsp;🟢 <em>Pronto para retirada</em><br><br>
                Você também recebe <strong>notificações automáticas</strong> por SMS ou e-mail a cada atualização!`
                },
                agendamento: {
                keywords: ['agend', 'marcar', 'marcação', 'agendar', 'horário', 'horario', 'reservar'],
                answer: `Para <strong>agendar um serviço</strong> na Oficina DK:<br><br>
                1️⃣ Faça login no sistema<br>
                2️⃣ Clique em <strong>"Add Cliente"</strong><br>
                3️⃣  Adiciona um carro <em>preenche o formuario do carro </em> desejado<br>
                4️⃣ em seguida vai em  <em>Serviços </em> preenche o o Formulario do serviço <br>
                5️⃣ Confirme seus dados e clique em <strong>"cadastrar "</strong><br><br>`
                },
                login: {
                keywords: ['login', 'senha', 'entrar', 'acessar', '', ''],
                answer: `Para acessar o sistema da <strong>Oficina DK</strong>:<br><br>
                1️⃣ Informe seu <strong>email</strong> e <strong>senha</strong> cadastrados<br>
                2️⃣ Clique em <strong>"Entrar"</strong><br><br>`
                },
       
            };
            
            const fallbacks = [
                'Não entendi muito bem sua dúvida. Pode reformular? 🤔',
                'Hmm, não tenho essa informação no momento. Tente perguntar sobre cadastro, orçamento, status do veículo ou agendamento!',
                'Essa pergunta está fora do meu alcance por aqui. Use os botões rápidos abaixo ou entre em contato com nossa equipe! 🛠️'
            ];
            
            let fallbackIdx = 0;
            
            function getAnswer(text) {
                const lower = text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                for (const key in knowledgeBase) {
                const entry = knowledgeBase[key];
                if (entry.keywords.some(kw => lower.includes(kw.normalize("NFD").replace(/[\u0300-\u036f]/g, "")))) {
                    return entry.answer;
                }
                }
                const resp = fallbacks[fallbackIdx % fallbacks.length];
                fallbackIdx++;
                return resp;
            }
            
            function addMessage(text, sender) {
                const body = document.getElementById('chatBody');
                const div = document.createElement('div');
                div.className = `message ${sender}`;
            
                if (sender === 'bot') {
                div.innerHTML = `
                    <div class="mini-avatar">DK</div>
                    <div class="bubble">${text}</div>`;
                } else {
                div.innerHTML = `<div class="bubble">${text}</div>`;
                }
            
                body.appendChild(div);
                body.scrollTop = body.scrollHeight;
            }
            
            function showTyping() {
                const body = document.getElementById('chatBody');
                const div = document.createElement('div');
                div.className = 'message bot';
                div.id = 'typingMsg';
                div.innerHTML = `
                <div class="mini-avatar">DK</div>
                <div class="bubble"><div class="typing"><span></span><span></span><span></span></div></div>`;
                body.appendChild(div);
                body.scrollTop = body.scrollHeight;
            }
            
            function removeTyping() {
                const el = document.getElementById('typingMsg');
                if (el) el.remove();
            }
            
            function sendMessage() {
                const input = document.getElementById('userInput');
                const text = input.value.trim();
                if (!text) return;
                input.value = '';
            
                addMessage(text, 'user');
            
                showTyping();
                setTimeout(() => {
                removeTyping();
                const answer = getAnswer(text);
                addMessage(answer, 'bot');
                }, 900);
            }
            
            function sendQuick(text) {
                document.getElementById('userInput').value = text;
                sendMessage();
            }
            
            function handleKey(e) {
                if (e.key === 'Enter') sendMessage();
            }
            
            // Welcome message
            window.addEventListener('load', () => {
                setTimeout(() => {
                addMessage('Olá! Bem-vindo ao sistema da <strong>Oficina DK</strong>.<br>Sou o assistente virtual e estou aqui para te ajudar!<br><br>Sobre o que você tem dúvidas?', 'bot');
                }, 400);
            });

            function abrirChat() {
            document.getElementById('chat-overlay').style.display = 'flex';
            }
            function fecharChat() {
            document.getElementById('chat-overlay').style.display = 'none';
            }
            // Fechar clicando fora do chat
            document.getElementById('chat-overlay').addEventListener('click', function(e) {
            if (e.target === this) fecharChat();
            });
            //----------------------------------------------------------------
            //----------------------------------------------------------------
            //----------------------------------------------------------------
            //----------------------------------------------------------------

