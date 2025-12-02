// Todas as funções referentes ao sistema de ligas vão dentro dessa função principal
(function () {
    'use strict';

    console.log('Inicializando leagues.js...');

    class LeagueSystem {
        constructor() {
            this.ligasUsuario = [];
            this.rankingGeral = [];
            this.rankingLigaAtual = [];
            this.ligaSelecionada = null;

            console.log('LeagueSystem constructor chamado');

            // Verifica se os elementos necessários existem
            if (!this.elementosExistem()) {
                console.log('Elementos das ligas não encontrados, sistema não inicializado');
                return;
            }

            this.inicializarElementos();
            this.inicializarEventos();
            this.carregarDadosIniciais();


            console.log('LeagueSystem inicializado com sucesso');
        }

        async definirLigaAtiva(ligaId) {
            console.log('Definindo liga ativa:', ligaId);

            try {
                const formData = new FormData();
                formData.append('liga_id', ligaId);

                const response = await fetch('php/set_active_league.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.mostrarMensagem('✅ Liga selecionada! Suas próximas partidas contarão aqui.', 'sucesso');
                } else {
                    this.mostrarMensagem('❌ ' + data.message, 'erro');
                }
            } catch (error) {
                console.error('Erro ao definir liga ativa:', error);
                this.mostrarMensagem('❌ Erro de conexão', 'erro');
            }
        }

        elementosExistem() {
            // Verifica se pelo menos um elemento das ligas existe
            const elementos = [
                '.tela-ligas',
                '#btnCriarLiga',
                '#btnEntrarLiga',
                '#btnMinhasLigas',
                '#btnRankingGeral'
            ];

            return elementos.some(selector => document.querySelector(selector));
        }

        inicializarElementos() {
            console.log('Inicializando elementos...');
            // Elementos de formulário
            this.formCriarLiga = document.getElementById('formCriarLiga');
            this.formEntrarLiga = document.getElementById('formEntrarLiga');
            this.listaMinhasLigas = document.getElementById('listaMinhasLigas');
            this.tabelaRankingGeral = document.getElementById('RankingGeralTabela');
            this.tabelaRankingLiga = document.getElementById('RankingLigaTabela');

            console.log('Elementos encontrados:', {
                formCriarLiga: !!this.formCriarLiga,
                formEntrarLiga: !!this.formEntrarLiga,
                listaMinhasLigas: !!this.listaMinhasLigas,
                tabelaRankingGeral: !!this.tabelaRankingGeral,
                tabelaRankingLiga: !!this.tabelaRankingLiga
            });
        }

        inicializarEventos() {
            console.log('Inicializando eventos...');

            // Formulário de criar liga
            if (this.formCriarLiga) {
                this.formCriarLiga.addEventListener('submit', (e) => {
                    e.preventDefault();
                    console.log('Formulário criar liga enviado');
                    this.criarLiga();
                });
            }

            // Formulário de entrar na liga
            if (this.formEntrarLiga) {
                this.formEntrarLiga.addEventListener('submit', (e) => {
                    e.preventDefault();
                    console.log('Formulário entrar liga enviado');
                    this.entrarLiga();
                });
            }
        }

        // Navegação
        mostrarCriarLiga() {
            console.log('Mostrando tela criar liga');
            mostrarTela('criarLiga');
        }

        mostrarEntrarLiga() {
            console.log('Mostrando tela entrar liga');
            mostrarTela('entrarLiga');
        }

        // API
        async carregarDadosIniciais() {
            console.log('Carregando dados iniciais...');
            try {
                // Carrega ligas do usuário ao iniciar
                const data = await this.apiRequest('get_minhas_ligas');
                if (data.success) {
                    this.ligasUsuario = data.ligas || [];
                    console.log('Ligas do usuário carregadas:', this.ligasUsuario);
                }
            } catch (error) {
                console.error('Erro ao carregar dados iniciais:', error);
            }
        }

        async criarLiga() {
            const nome = document.getElementById('ligaNome')?.value;
            const palavraChave = document.getElementById('ligaSenha')?.value;

            console.log('Criando liga:', { nome, palavraChave });

            if (!nome || !palavraChave) {
                this.mostrarMensagem('Preencha todos os campos!', 'erro');
                return;
            }

            try {
                const data = await this.apiRequest('criar', {
                    nome: nome,
                    palavra_chave: palavraChave
                });

                console.log('Resposta criar liga:', data);

                if (data.success) {
                    this.mostrarMensagem('Liga criada com sucesso! ID: ' + data.liga_id, 'sucesso');
                    mostrarTela('ligas');
                    this.carregarMinhasLigas(); // Atualiza a lista
                } else {
                    this.mostrarMensagem(data.message || 'Erro ao criar liga', 'erro');
                }
            } catch (error) {
                console.error('Erro ao criar liga:', error);
                this.mostrarMensagem('Erro de conexão com o servidor', 'erro');
            }
        }

        async entrarLiga() {
            const ligaId = document.getElementById('ligaID')?.value;
            const palavraChave = document.getElementById('ligaSenhaEntrada')?.value;

            console.log('Entrando na liga:', { ligaId, palavraChave });

            if (!ligaId || !palavraChave) {
                this.mostrarMensagem('Preencha todos os campos!', 'erro');
                return;
            }

            try {
                const data = await this.apiRequest('entrar', {
                    liga_id: ligaId,
                    palavra_chave: palavraChave
                });

                console.log('Resposta entrar liga:', data);

                if (data.success) {
                    this.mostrarMensagem('Entrou na liga com sucesso!', 'sucesso');
                    mostrarTela('ligas');
                } else {
                    this.mostrarMensagem(data.message || 'Palavra-chave incorreta', 'erro');
                }
            } catch (error) {
                console.error('Erro ao entrar na liga:', error);
                this.mostrarMensagem('Liga não encontrada ou erro de conexão', 'erro');
            }
        }

        async carregarMinhasLigas() {
            console.log('Carregando minhas ligas...');
            try {
                const data = await this.apiRequest('get_minhas_ligas');
                console.log('Dados minhas ligas:', data);

                if (data.success) {
                    this.ligasUsuario = data.ligas || [];
                    this.renderizarMinhasLigas();
                    mostrarTela('minhasLigas');
                } else {
                    this.mostrarMensagem(data.message || 'Erro ao carregar ligas', 'erro');
                }
            } catch (error) {
                console.error('Erro ao carregar ligas:', error);
                this.mostrarMensagem('Erro de conexão', 'erro');
            }
        }

        async carregarRankingGeral() {
            console.log('Carregando ranking geral...');
            try {
                const data = await this.apiRequest('get_ranking_geral');
                console.log('Dados ranking geral:', data);

                if (data.success) {
                    this.rankingGeral = data.ranking || [];
                    this.renderizarRankingGeral();
                    mostrarTela('rankingGeral');
                } else {
                    this.mostrarMensagem(data.message || 'Erro ao carregar ranking', 'erro');
                }
            } catch (error) {
                console.error('Erro ao carregar ranking geral:', error);
                this.mostrarMensagem('Erro de conexão', 'erro');
            }
        }

        async carregarRankingLiga(ligaId) {
            if (!ligaId) {
                this.mostrarMensagem('Selecione uma liga primeiro', 'erro');
                return;
            }

            try {
                const data = await this.apiRequest('get_ranking_liga', { liga_id: ligaId });

                if (data.success) {
                    this.rankingLigaAtual = data.ranking || [];
                    this.ligaSelecionada = ligaId;
                    this.renderizarRankingLiga();
                    mostrarTela('rankingLiga');
                }
            } catch (error) {
                console.error('Erro ao carregar ranking da liga:', error);
            }
        }

        async verLiga(ligaId) {
            try {
                const data = await this.apiRequest('get_liga_info', { liga_id: ligaId });

                if (data.success) {
                    // Mostrar informações da liga em um modal ou página
                    alert(`Liga: ${data.liga.league_name}\nCriador: ${data.liga.criador_nome}\nMembros: ${data.liga.total_membros}`);

                    // Opcional: carregar ranking desta liga
                    this.carregarRankingLiga(ligaId);
                }
            } catch (error) {
                this.mostrarMensagem('Erro ao carregar informações da liga', 'erro');
            }
        }

        // Renderização
        renderizarMinhasLigas() {
            const container = document.getElementById('listaMinhasLigas');
            if (!container) return;

            if (this.ligasUsuario.length === 0) {
                container.innerHTML = `
            <div class="liga-vazia">
                <p>Você não está em nenhuma liga ainda.</p>
                <p>Crie uma nova liga ou entre em uma existente!</p>
            </div>
        `;
                return;
            }

            const html = this.ligasUsuario.map(liga => `
        <div class="liga-card">
            <h4>${liga.nome}</h4>
            <p><strong>Criador:</strong> ${liga.criador}</p>
            <p><strong>Membros:</strong> ${liga.membros}</p>
            <p><strong>ID da Liga:</strong> ${liga.id}</p>
            
            <div class="liga-card-botoes">
                <button onclick="window.leagueSystem.definirLigaAtiva(${liga.id})" 
                        class="botao-ver-liga">
                    🎯 Jogar Nesta Liga
                </button>
                
                <button onclick="window.leagueSystem.verLiga(${liga.id})" 
                        class="botao-ranking-liga">
                    📊 Ver Ranking
                </button>
            </div>
        </div>
    `).join('');

            container.innerHTML = html;
        }

        renderizarRankingGeral() {
            const tbody = this.tabelaRankingGeral?.querySelector('tbody');
            if (!tbody) return;

            if (this.rankingGeral.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 30px;">Nenhum jogador encontrado</td></tr>';
                return;
            }

            const html = this.rankingGeral.map((jogador, index) => {
                const classePosicao = index === 0 ? 'posicao-1' :
                    index === 1 ? 'posicao-2' :
                        index === 2 ? 'posicao-3' : '';

                return `
            <tr class="${classePosicao}">
                <td><strong>${index + 1}º</strong></td>
                <td>${jogador.nome}</td>
                <td>${jogador.pontuacao}</td>
                <td>${jogador.partidas}</td>
            </tr>
        `;
            }).join('');

            tbody.innerHTML = html;
        }

        renderizarRankingLiga() {
            const tbody = this.tabelaRankingLiga?.querySelector('tbody');
            const titulo = document.getElementById('ligaNomeTitulo');

            if (!tbody) return;

            if (this.rankingLigaAtual.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4">Nenhum jogador nesta liga</td></tr>';
                return;
            }

            const html = this.rankingLigaAtual.map((jogador, index) => `
                <tr>
                    <td><strong>${index + 1}º</strong></td>
                    <td>${jogador.nome}</td>
                    <td>${jogador.pontuacao}</td>
                    <td>${jogador.partidas}</td>
                </tr>
            `).join('');

            tbody.innerHTML = html;

            // Atualizar título se existir
            if (titulo) {
                titulo.textContent = `Liga: ${this.ligaSelecionada}`;
            }
        }

        // API Request
        async apiRequest(action, dados = {}) {
            const formData = new FormData();
            formData.append('action', action);

            // Adiciona dados ao FormData
            Object.keys(dados).forEach(key => {
                formData.append(key, dados[key]);
            });

            console.log(`Enviando requisição para: php/leagues.php, ação: ${action}`, dados);

            const response = await fetch('php/leagues.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }

        // Sistema de mensagens
        mostrarMensagem(texto, tipo = 'info') {
            // Remove mensagem anterior
            const mensagemAnterior = document.getElementById('mensagem-flutuante');
            if (mensagemAnterior) {
                mensagemAnterior.remove();
            }

            // Cores baseadas no tipo
            const cores = {
                'sucesso': 'rgba(0, 255, 102, 0.9)',
                'erro': 'rgba(255, 0, 0, 0.9)',
                'info': 'rgba(0, 150, 255, 0.9)'
            };

            const cor = cores[tipo] || cores['info'];

            // Cria nova mensagem
            const mensagem = document.createElement('div');
            mensagem.id = 'mensagem-flutuante';
            mensagem.textContent = texto;
            mensagem.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 10px;
                background: ${cor};
                color: white;
                z-index: 1000;
                font-weight: bold;
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                animation: fadeInOut 3s ease-in-out;
            `;

            document.body.appendChild(mensagem);

            // Remove após 3 segundos
            setTimeout(() => {
                if (mensagem.parentNode) {
                    mensagem.remove();
                }
            }, 3000);

            console.log(`Mensagem ${tipo}: ${texto}`);
        }
    }

    // Inicia o sistema quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM carregado, inicializando LeagueSystem...');
        window.leagueSystem = new LeagueSystem();
        console.log('LeagueSystem criado:', window.leagueSystem);
    });


})();