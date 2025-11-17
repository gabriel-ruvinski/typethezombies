class WordsVsZombies {
    constructor() {
        this.pontuacao = 0;
        this.vidas = 3;
        this.tempo = 60;
        this.palavras = ['zumbi', 'cerebro', 'infectado', 'apocalipse', 'sobrevivencia', 'epidemia', 'morte', 'noite', 'medo'];
        this.zumbisAtivos = [];
        this.jogoAtivo = false;
        this.timerInterval = null;
        
        this.inicializarElementos();
        this.inicializarEventos();
    }
    
    inicializarElementos() {
        this.gameArea = document.getElementById('gameArea');
        this.inputPalavra = document.getElementById('inputPalavra');
        this.palavraAtual = document.getElementById('palavraAtual');
        this.pontuacaoElement = document.getElementById('pontuacao');
        this.vidasElement = document.getElementById('vidas');
        this.tempoElement = document.getElementById('tempo');
        this.btnJogar = document.getElementById('btnJogar');
        this.btnVoltarJogo = document.getElementById('btnVoltarJogo');
    }
    
    inicializarEventos() {
        this.btnJogar.addEventListener('click', () => this.iniciarJogo());
        this.btnVoltarJogo.addEventListener('click', () => this.voltarMenu());
        this.inputPalavra.addEventListener('input', (e) => this.verificarPalavra(e));
    }
    
    iniciarJogo() {
        // Esconder menu, mostrar jogo
        document.querySelector('.tela-inicial').style.display = 'none';
        document.querySelector('.tela-jogo').style.display = 'block';
        
        // Resetar valores
        this.pontuacao = 0;
        this.vidas = 3;
        this.tempo = 60;
        this.jogoAtivo = true;
        this.zumbisAtivos = [];
        
        // Limpar área de jogo
        this.gameArea.innerHTML = '';
        
        this.atualizarUI();
        this.iniciarTimer();
        this.gerarZumbi();
        
        // Focar no input para começar a digitar
        this.inputPalavra.focus();
    }
    
    gerarZumbi() {
        if (!this.jogoAtivo) return;
        
        const palavra = this.palavras[Math.floor(Math.random() * this.palavras.length)];
        const zumbi = document.createElement('div');
        zumbi.className = 'zumbi';
        zumbi.textContent = palavra;
        zumbi.dataset.palavra = palavra;
        
        // Posição aleatória no topo (considerando largura do zumbi)
        const maxLeft = this.gameArea.offsetWidth - 100;
        zumbi.style.left = Math.random() * maxLeft + 'px';
        zumbi.style.top = '0px';
        
        this.gameArea.appendChild(zumbi);
        this.zumbisAtivos.push(zumbi);
        
        // Mover zumbi para baixo
        this.moverZumbi(zumbi);
        
        // Gerar próximo zumbi após tempo aleatório
        const proximoZumbi = Math.random() * 2000 + 1000; // 1-3 segundos
        setTimeout(() => this.gerarZumbi(), proximoZumbi);
    }
    
    moverZumbi(zumbi) {
        const velocidade = 0.5; // pixels por frame
        let posicaoTop = 0;
        
        const mover = () => {
            if (!this.jogoAtivo || !zumbi.parentNode) return;
            
            posicaoTop += velocidade;
            zumbi.style.top = posicaoTop + 'px';
            
            // Se chegou no final (altura da gameArea - altura do zumbi)
            if (posicaoTop > this.gameArea.offsetHeight - 30) {
                this.perderVida();
                this.removerZumbi(zumbi);
                return;
            }
            
            requestAnimationFrame(mover);
        };
        mover();
    }
    
    verificarPalavra(e) {
        const texto = e.target.value.toLowerCase().trim();
        
        for (let zumbi of this.zumbisAtivos) {
            if (zumbi.dataset.palavra === texto) {
                this.eliminarZumbi(zumbi);
                e.target.value = '';
                this.palavraAtual.textContent = '';
                return;
            }
        }
        
        // Mostrar palavra sendo digitada
        this.palavraAtual.textContent = texto;
    }
    
    eliminarZumbi(zumbi) {
        this.pontuacao += 10;
        this.removerZumbi(zumbi);
        this.atualizarUI();
        
        // Efeito visual de eliminação
        zumbi.style.color = '#ff0000';
        zumbi.style.transform = 'scale(1.5)';
        zumbi.style.opacity = '0.7';
        
        setTimeout(() => {
            if (zumbi.parentNode) {
                zumbi.remove();
            }
        }, 200);
    }
    
    removerZumbi(zumbi) {
        const index = this.zumbisAtivos.indexOf(zumbi);
        if (index > -1) {
            this.zumbisAtivos.splice(index, 1);
        }
    }
    
    perderVida() {
        this.vidas--;
        this.atualizarUI();
        
        if (this.vidas <= 0) {
            this.fimDeJogo();
        }
    }
    
    atualizarUI() {
        this.pontuacaoElement.textContent = this.pontuacao;
        this.vidasElement.textContent = this.vidas;
        this.tempoElement.textContent = this.tempo;
    }
    
    iniciarTimer() {
        // Limpar timer anterior se existir
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
        
        this.timerInterval = setInterval(() => {
            this.tempo--;
            this.tempoElement.textContent = this.tempo;
            
            if (this.tempo <= 0) {
                this.fimDeJogo();
            }
        }, 1000);
    }
    
    fimDeJogo() {
        this.jogoAtivo = false;
        clearInterval(this.timerInterval);
        
        // Limpar zumbis
        this.zumbisAtivos.forEach(zumbi => {
            if (zumbi.parentNode) {
                zumbi.remove();
            }
        });
        this.zumbisAtivos = [];
        
        setTimeout(() => {
            alert(`Fim de Jogo!\nPontuação: ${this.pontuacao}`);
            this.voltarMenu();
        }, 500);
    }
    
    voltarMenu() {
        document.querySelector('.tela-jogo').style.display = 'none';
        document.querySelector('.tela-inicial').style.display = 'block';
        this.jogoAtivo = false;
        clearInterval(this.timerInterval);
        
        // Limpar input
        this.inputPalavra.value = '';
        this.palavraAtual.textContent = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const jogo = new WordsVsZombies();
    
    // Botões principais
    const btnConfig = document.getElementById("btnConfig");
    const btnVoltar = document.getElementById("btnVoltar");
    const btnSalvar = document.getElementById("btnSalvar");

    // Telas
    const telaInicial = document.querySelector(".tela-inicial");
    const telaConfig = document.querySelector(".tela-config");

    // Quando clicar em "Configurações"
    if (btnConfig) {
        btnConfig.addEventListener("click", function() {
            telaInicial.style.display = "none";
            telaConfig.style.display = "block";
        });
    }

    // Quando clicar em "Voltar"
    if (btnVoltar) {
        btnVoltar.addEventListener("click", function() {
            telaConfig.style.display = "none";
            telaInicial.style.display = "block";
        });
    }

    // Quando clicar em "Salvar"
    if (btnSalvar) {
        btnSalvar.addEventListener("click", function() {
            const musica = document.getElementById("musica").value;
            const efeitos = document.getElementById("efeitos").value;
            const dificuldade = document.getElementById("dificuldade").value;
            const tema = document.getElementById("tema").value;

            // Remover o tema anterior
            document.body.classList.remove("tema-padrao", "tema-roxo", "tema-vermelho");

            // Aplicar o novo tema
            if (tema === "padrao") {
                document.body.classList.add("tema-padrao");
            } else if (tema === "roxo") {
                document.body.classList.add("tema-roxo");
            } else if (tema === "vermelho") {
                document.body.classList.add("tema-vermelho");
            }

            alert(`Configurações salvas!\n\n Música: ${musica}%\n Efeitos: ${efeitos}%\n Dificuldade: ${dificuldade}\n Tema: ${tema}`);
        });
    }
});
