// Todas as funções referentes ao jogo vão dentro dessa função principal
(function () {
    if (window.jogo) {
        console.log('Jogo já inicializado, reutilizando instância...');
        return; // Não cria nova instância
    }

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
            this.dificuldade = 'medio';
            this.velocidadeZumbi = 0.5;
            this.intervaloGeracao = 2000;
            this.gerandoZumbi = false;
            this.palavrasPorDificuldade = {
                facil: [],
                medio: [],
                dificil: []
            };
            this.carregarPalavras();
            this.inicializarElementos();
            this.inicializarEventos();
            this.carregarDificuldadeSalva();

            setTimeout(() => this.iniciar(), 1000);
        }

        iniciar() {
            console.log('Game iniciado com dificuldade:', this.dificuldade);
            console.log('Palavras fáceis:', this.palavrasPorDificuldade.facil.length);
            console.log('Palavras médias:', this.palavrasPorDificuldade.medio.length);
            console.log('Palavras difíceis:', this.palavrasPorDificuldade.dificil.length);
            console.log('Palavras ativas:', this.palavras.length);
        }

        carregarDificuldadeSalva() {
            const dificuldadeSalva = localStorage.getItem('dificuldade');
            if (dificuldadeSalva) {
                this.dificuldade = dificuldadeSalva;
                this.configurarDificuldade();
            }
        }

        configurarDificuldade() {
            switch (this.dificuldade) {
                case 'facil':
                    this.velocidadeZumbi = 0.3;
                    this.intervaloGeracao = 3000;
                    break;
                case 'medio':
                    this.velocidadeZumbi = 0.5;
                    this.intervaloGeracao = 2000;
                    break;
                case 'dificil':
                    this.velocidadeZumbi = 0.8;
                    this.intervaloGeracao = 1500;
                    break;
            }
            console.log(`Dificuldade: ${this.dificuldade}, Velocidade: ${this.velocidadeZumbi}, Intervalo: ${this.intervaloGeracao}`);
        }

        async carregarPalavras() {
            try {
                const response = await fetch('data/words.json');
                if (!response.ok) {
                    throw new Error('Arquivo não encontrado');
                }

                const dados = await response.json();

                this.palavrasPorDificuldade.facil = dados.facil || [];
                this.palavrasPorDificuldade.medio = dados.medio || [];
                this.palavrasPorDificuldade.dificil = dados.dificil || [];

                this.atualizarPalavrasAtivas();
            } catch (error) {
                console.error('Erro ao carregar palavras:', error);

                // Se der erro ao carregar...
                this.palavrasPorDificuldade = {
                    facil: ['erro', 'medo', 'noite', 'lua', 'sangue'],
                    medio: ['erro', 'cerebro', 'sobrevivencia', 'infectado', 'pandemia'],
                    dificil: ['erro', 'apocaliptico', 'necromancia', 'catastrofe', 'extincao']
                };
                this.atualizarPalavrasAtivas();
            }
        }

        atualizarPalavrasAtivas() {
            this.palavras = this.palavrasPorDificuldade[this.dificuldade];

            // Se a dificuldade for médio ou difícil, adiciona algumas palavras mais fáceis também
            if (this.dificuldade === 'medio') {
                this.palavras = [...this.palavrasPorDificuldade.facil, ...this.palavras];
            } else if (this.dificuldade === 'dificil') {
                this.palavras = [
                    ...this.palavrasPorDificuldade.facil,
                    ...this.palavrasPorDificuldade.medio,
                    ...this.palavrasPorDificuldade.dificil
                ];
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
            console.log('Iniciando jogo com dificuldade:', this.dificuldade);

            this.configurarDificuldade();
            this.atualizarPalavrasAtivas();
            this.mostrarIndicadorDificuldade();
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
            if (this.gameArea) {
                this.gameArea.innerHTML = '';
            }

            this.atualizarUI();
            this.iniciarTimer();
            this.gerarZumbi();

            // Focar no input para começar a digitar
            this.inputPalavra.focus();
        }

        gerarZumbi() {
            if (!this.jogoAtivo || this.gerandoZumbi) return;
            this.gerandoZumbi = true;

            // Escolher palavra baseado na dificuldade
            let palavrasPool;
            switch (this.dificuldade) {
                case 'facil':
                    palavrasPool = this.palavrasPorDificuldade.facil;
                    break;
                case 'medio':
                    // 70% médio, 30% fácil
                    palavrasPool = Math.random() < 0.7 ?
                        this.palavrasPorDificuldade.medio :
                        this.palavrasPorDificuldade.facil;
                    break;
                case 'dificil':
                    // 60% difícil, 30% médio, 10% fácil
                    const random = Math.random();
                    if (random < 0.6) {
                        palavrasPool = this.palavrasPorDificuldade.dificil;
                    } else if (random < 0.9) {
                        palavrasPool = this.palavrasPorDificuldade.medio;
                    } else {
                        palavrasPool = this.palavrasPorDificuldade.facil;
                    }
                    break;
            }

            if (!palavrasPool || palavrasPool.length === 0) {
                console.error('Palavras pool vazio para dificuldade:', this.dificuldade);
                palavrasPool = ['erro']; // Fallback
            }
            const palavra = palavrasPool[Math.floor(Math.random() * palavrasPool.length)];
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

            zumbi.classList.add(`zumbi-${this.dificuldade}`);

            this.gameArea.appendChild(zumbi);
            this.zumbisAtivos.push(zumbi);

            this.moverZumbi(zumbi, this.velocidadeZumbi);

            const proximoZumbi = Math.random() * this.intervaloGeracao + 500;
            setTimeout(() => {
                this.gerandoZumbi = false; // Libera para próxima geração
                this.gerarZumbi();
            }, proximoZumbi);
        }

        moverZumbi(zumbi, velocidade) {
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
            this.salvarPartida();

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

        async salvarPartida() {
            try {
                const formData = new FormData();
                formData.append('pontuacao', this.pontuacao);
                formData.append('duracao', 60 - this.tempo); 
                const response = await fetch('php/save_match.php', {
                    method: 'POST',
                    body: formData
                });

                const texto = await response.text();

                try {
                    const data = JSON.parse(texto);
                    if (data.success) {
                        console.log('Partida salva com sucesso');
                    } else {
                        console.error('Erro ao salvar:', data.message);
                    }
                } catch (e) {
                    console.error('Resposta não é JSON válido:', texto);
                }
            } catch (error) {
                console.error('Erro na requisição:', error);
            }
        }

        mostrarIndicadorDificuldade() {
            const indicator = document.getElementById('dificuldadeIndicator');
            if (!indicator) return;

            let texto, classe;
            switch (this.dificuldade) {
                case 'facil':
                    texto = 'FÁCIL';
                    classe = 'dificuldade-facil';
                    break;
                case 'medio':
                    texto = 'MÉDIO';
                    classe = 'dificuldade-medio';
                    break;
                case 'dificil':
                    texto = 'DIFÍCIL';
                    classe = 'dificuldade-dificil';
                    break;
            }

            indicator.textContent = texto;
            indicator.className = 'dificuldade-indicator ' + classe;
        }

        recarregarDificuldade() {
            console.log('=== RECARREGANDO DIFICULDADE ===');

            // 1. Pegar do localStorage (caso tenha sido mudado em outra aba)
            const dificuldadeSalva = localStorage.getItem('dificuldade');
            if (dificuldadeSalva && dificuldadeSalva !== this.dificuldade) {
                console.log('Dificuldade mudou de', this.dificuldade, 'para', dificuldadeSalva);
                this.dificuldade = dificuldadeSalva;
            }

            // 2. Aplicar configurações
            this.configurarDificuldade();
            this.atualizarPalavrasAtivas();

            // 3. Atualizar indicador se estiver em jogo
            if (this.jogoAtivo) {
                this.mostrarIndicadorDificuldade();
            }

            // 4. DEBUG: Mostrar informações
            console.log('Dificuldade atual:', this.dificuldade);
            console.log('Velocidade:', this.velocidadeZumbi);
            console.log('Intervalo:', this.intervaloGeracao);
            console.log('Palavras ativas:', this.palavras.length);

            return this.dificuldade;
        }

    }
    const jogo = new WordsVsZombies();
    window.jogo = jogo;
    console.log('Instância do jogo criada e disponível em window.jogo');
})();