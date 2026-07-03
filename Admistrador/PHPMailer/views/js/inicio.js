
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

           

            //------------------------assis----------------------------------------
            //----------------------------------------------------------------
                const knowledgeBase = {

    desenvolvedor: {
        keywords: ['quem te criou', 'quem te desenvolveu', 'criador', 'desenvolvedor', 'quem fez o sistema'],
        answer: `Fui desenvolvido por <strong>Felizardo Rodrigues de Sousa e Silva</strong>, estudante finalista do Instituto Técnico Privado Lucrécia dos Santos.<br><br>
        O objetivo do sistema é servir como suporte técnico para gestão da oficina e controlo interno.`
    },

                cadastro: {
                    keywords: ['cadastro', 'registrar', 'conta', 'criar conta', 'novo utilizador', 'novo cliente'],
                    answer: `Para criar uma conta no sistema da <strong>Oficina DK</strong>:<br><br>
                    1️⃣ Acesse o sistema com conta de administrador<br>
                    2️⃣ No menu lateral esquerdo, clique em <strong>"Contas"</strong><br>
                    3️⃣ Clique no botão <strong>"Adicionar"</strong><br>
                    4️⃣ Preencha os campos obrigatórios<br>
                    5️⃣ Insira a senha do administrador<br>
                    6️⃣ Clique em <strong>"Salvar"</strong><br><br>
                    ✔ Apenas administradores podem criar contas.`
                },

                login: {
                    keywords: ['login', 'entrar', 'acesso', 'senha', 'iniciar sessão'],
                    answer: `Para iniciar sessão no sistema:<br><br>
                    1️⃣ Acesse a página de login<br>
                    2️⃣ Insira seu email e senha<br>
                    3️⃣ Clique em <strong>"Entrar"</strong><br><br>
                    ✔ Se os dados estiverem corretos, será direcionado ao sistema.`
                },

                logout: {
                    keywords: ['sair', 'logout', 'terminar sessão', 'encerrar sessão'],
                    answer: `Para terminar a sessão:<br><br>
                    1️⃣ Vá ao canto superior direito<br>
                    2️⃣ Clique em <strong>"Sair"</strong><br><br>
                    ✔ A sessão será encerrada com segurança.`
                },

                recuperar_conta: {
                    keywords: ['esqueci senha', 'recuperar conta', 'reset senha', 'senha esquecida'],
                    answer: `Para recuperar a sua conta:<br><br>
                    1️⃣ Vá à tela de login<br>
                    2️⃣ Clique em <strong>"Esqueci a minha senha"</strong><br>
                    3️⃣ Insira o seu email<br>
                    4️⃣ Receberá um código válido por 30 minutos<br>
                    5️⃣ Insira o código e defina uma nova senha<br><br>
                    ✔ Se não tiver acesso ao email, contacte um administrador.`
                },

                editar_registo: {
                    keywords: ['editar', 'atualizar', 'alterar', 'modificar registo'],
                    answer: `Para editar ou atualizar um registo:<br><br>
                    1️⃣ Vá ao menu lateral esquerdo<br>
                    2️⃣ Acesse a tabela desejada<br>
                    3️⃣ Clique no ícone no registo que deseja editar<br><br>
                    ⚠️ Em serviços e contratos, use o botão "Ver" antes de editar.`
                },

                apagar_registo: {
                    keywords: ['apagar', 'eliminar', 'deletar', 'remover registo'],
                    answer: `Apenas administradores podem eliminar registos.<br><br>
                    1️⃣ Clique no ícone no registo desejado<br>
                    2️⃣ Confirme a ação`
                },

                status: {
                    keywords: ['status', 'situação', 'andamento', 'meu carro', 'serviço'],
                    answer: `Para verificar o status do seu veículo:<br><br>
                    1️⃣ Acesse o sistema<br>
                    2️⃣ Vá ao menu <strong>"Serviços"</strong><br>
                    3️⃣ Veja o status do veículo:<br>
                    🔵 Em manutenção<br>
                    🟢 Pronto para entrega<br><br>
                    ✔ O status é atualizado automaticamente.`
                },

                relatorio: {
                    keywords: ['relatório', 'imprimir relatório', 'estatística', 'empresa'],
                    answer: `Para gerar relatório da empresa:<br><br>
                    1️⃣ Acesse a tela de estatísticas<br>
                    2️⃣ Clique no botão <strong>"Imprimir relatório"</strong><br><br>
                    ✔ O sistema gera um relatório completo da empresa.`
                },

                fatura: {
                    keywords: ['fatura', 'imprimir fatura', 'recibo'],
                    answer: `Para imprimir uma fatura:<br><br>
                     Em Serviços:<br>
                    1️⃣ Acesse "Serviços"<br>
                    2️⃣ Clique em "Gerar fatura"<br><br>
                     Em Contratos:<br>
                    1️⃣ Acesse "Contratos"<br>
                    2️⃣ Clique em "Ver"<br>
                    3️⃣ Use o botão de impressão<br><br>
                    ✔ A fatura será gerada automaticamente.`
                },

                agendamento: {
                    keywords: ['agendar', 'marcar', 'agendamento', 'horário'],
                    answer: `Para agendar um serviço:<br><br>
                    1️⃣ Faça login no sistema<br>
                    2️⃣ Adicione o cliente<br>
                    3️⃣ Registe o veículo<br>
                    4️⃣ Vá em "Serviços"<br>
                    5️⃣ Preencha o formulário<br>
                    6️⃣ Clique em <strong>"Cadastrar"</strong>`
                },

                orcamento: {
                    keywords: ['orçamento', 'preço', 'valor', 'quanto custa'],
                    answer: `Para consultar um orçamento:<br><br>
                    1️⃣ Faça login no sistema<br>
                    2️⃣ Vá em "Serviços"<br>
                    3️⃣ Clique em "Ver orçamento"<br><br>
                    ✔ O orçamento inclui peças, mão de obra e valores totais.`
                }
            };
            
            const fallbacks = [
                'Não entendi muito bem sua dúvida. Pode reformular? ',
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

