// Todas as funções referentes ao jogo vão dentro dessa função principal
(function () {
    if (!document.querySelector('.tela-jogo')) return;

    console.log('Inicializando game.js...');
    class WordsVsZombies {
        constructor() {
            this.pontuacao = 0;
            this.vidas = 3;
            this.tempo = 60;
            this.palavras = [];
            this.zumbisAtivos = [];
            this.jogoAtivo = false;
            this.timerInterval = null;

            this.carregarPalavras();
            this.inicializarElementos();
            this.inicializarEventos();
        }

        async carregarPalavras() {
        try {
            const response = await fetch('data/words.json');
            if (!response.ok) {
                throw new Error('Arquivo não encontrado');
            }
            
            const dados = await response.json();
            
            this.palavras = [
                ...dados.facil,
                ...dados.medio, 
                ...dados.dificil
            ];
            
            console.log('Palavras carregadas:', this.palavras.length);
        } catch (error) {
            console.error('Erro ao carregar palavras:', error);
            
            // Se der erro ao carregar...
            this.palavras = ['zumbi', 'cerebro', 'medo', 'morte', 'apocalipse'];
        }
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

            // Cálculos para fazer o zumbi caber na teka
            const larguraZumbi = Math.max(90, palavra.length * 14);

            const maxLeft = this.gameArea.offsetWidth - larguraZumbi - 20;

            const posicaoLeft = Math.max(15, Math.random() * maxLeft); // Garante que não fique negativo

            zumbi.style.left = posicaoLeft + 'px';
            zumbi.style.top = '0px';

            // Largura fixa suficiente
            zumbi.style.minWidth = larguraZumbi + 'px';
            zumbi.style.textAlign = 'center';

            this.gameArea.appendChild(zumbi);
            this.zumbisAtivos.push(zumbi);

            this.moverZumbi(zumbi);

            const proximoZumbi = Math.random() * 2000 + 1000;
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
                    zumbi.remove();
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

            zumbi.classList.add('zumbi-eliminado');

            // Remove após 500ms (tempo da animação)
            setTimeout(() => {
                if (zumbi.parentNode) {
                    zumbi.remove();
                }
            }, 500);
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
    const jogo = new WordsVsZombies();
})();