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

            this.carregandoRankingGeral = false;
            this.carregandoRankingSemanal = false;
            this.carregandoLigas = false;

            this.inicializarElementos();
            this.inicializarEventos();
            this.carregarDadosIniciais();
        }

        async definirLigaAtiva(ligaId) {
            try {
                const formData = new FormData();
                formData.append('liga_id', ligaId);

                const response = await fetch('php/set_active_league.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.mostrarMensagem('Liga selecionada! Suas próximas partidas contarão aqui.', 'sucesso');
                } else {
                    this.mostrarMensagem(data.message, 'erro');
                }
            } catch (error) {
                console.error('Erro ao definir liga ativa:', error);
                this.mostrarMensagem('Erro de conexão', 'erro');
            }
        }

        // Carregar ranking semanal
        async carregarRankingSemanal() {
            try {
                const data = await this.apiRequest('get_ranking_semanal');

                if (data.success) {
                    this.rankingSemanal = data.ranking || [];
                    this.periodoSemanal = data.periodo || {};
                    this.renderizarRankingSemanal();
                    mostrarTela('rankingSemanal');
                } else {
                    this.mostrarMensagem(data.message || 'Erro ao carregar ranking semanal', 'erro');
                }
            } catch (error) {
                console.error('Erro ao carregar ranking semanal:', error);
                this.mostrarMensagem('Erro de conexão', 'erro');
            }
        }

        // Renderizar ranking semanal
        renderizarRankingSemanal() {
            const tbody = document.getElementById('RankingSemanalTabela')?.querySelector('tbody');
            const periodoInfo = document.getElementById('periodoSemanal');

            if (!tbody) return;

            // Mostrar período
            if (periodoInfo && this.periodoSemanal) {
                periodoInfo.innerHTML = `
                Período: <strong>${this.periodoSemanal.inicio}</strong> 
                até <strong>${this.periodoSemanal.fim}</strong>
            `;
            }

            if (this.rankingSemanal.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px;">
                        Nenhuma pontuação esta semana. Seja o primeiro!
                    </td>
                </tr>
            `;
                return;
            }

            const html = this.rankingSemanal.map((jogador, index) => {
                const classePosicao = index === 0 ? 'posicao-1' :
                    index === 1 ? 'posicao-2' :
                        index === 2 ? 'posicao-3' : '';

                return `
                <tr class="${classePosicao}">
                    <td>
                        <div class="posicao-badge">${index + 1}</div>
                    </td>
                    <td>${jogador.nome}</td>
                    <td>${jogador.pontuacao}</td>
                    <td>${jogador.partidas}</td>
                </tr>
            `;
            }).join('');

            tbody.innerHTML = html;
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
            this.formCriarLiga = document.getElementById('formCriarLiga');
            this.formEntrarLiga = document.getElementById('formEntrarLiga');
            this.listaMinhasLigas = document.getElementById('listaMinhasLigas');
            this.tabelaRankingGeral = document.getElementById('RankingGeralTabela');
            this.tabelaRankingLiga = document.getElementById('RankingLigaTabela');
        }

        inicializarEventos() {
            // Formulário de criar liga
            if (this.formCriarLiga) {
                this.formCriarLiga.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.criarLiga();
                });
            }

            // Formulário de entrar na liga
            if (this.formEntrarLiga) {
                this.formEntrarLiga.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.entrarLiga();
                });
            }
        }

        mostrarSelecaoLigaRanking(ligas) {
            let mensagem = 'Selecione uma liga para ver o ranking:\n\n';

            ligas.forEach((liga, index) => {
                mensagem += `${index + 1}. ${liga.nome} (ID: ${liga.id})\n`;
            });

            const ligaId = prompt(mensagem + '\nDigite o número da liga:');

            if (ligaId && !isNaN(ligaId)) {
                const index = parseInt(ligaId) - 1;
                if (index >= 0 && index < ligas.length) {
                    this.carregarRankingLiga(ligas[index].id);
                }
            }
        }

        // Navegação
        mostrarCriarLiga() {
            mostrarTela('criarLiga');
        }

        mostrarEntrarLiga() {
            mostrarTela('entrarLiga');
        }

        // API
        async carregarDadosIniciais() {
            try {
                // Carrega ligas do usuário ao iniciar
                const data = await this.apiRequest('get_minhas_ligas');
                if (data.success) {
                    this.ligasUsuario = data.ligas || [];
                }
            } catch (error) {
                console.error('Erro ao carregar dados iniciais:', error);
            }
        }

        async criarLiga() {
            const nome = document.getElementById('ligaNome')?.value;
            const palavraChave = document.getElementById('ligaSenha')?.value;

            if (!nome || !palavraChave) {
                this.mostrarMensagem('Preencha todos os campos!', 'erro');
                return;
            }

            try {
                const data = await this.apiRequest('criar', {
                    nome: nome,
                    palavra_chave: palavraChave
                });

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

            if (!ligaId || !palavraChave) {
                this.mostrarMensagem('Preencha todos os campos!', 'erro');
                return;
            }

            try {
                const data = await this.apiRequest('entrar', {
                    liga_id: ligaId,
                    palavra_chave: palavraChave
                });

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
            try {
                const data = await this.apiRequest('get_minhas_ligas');

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
            try {
                const data = await this.apiRequest('get_ranking_geral');

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
                    Jogar Nesta Liga
                </button>
                
                <button onclick="window.leagueSystem.verLiga(${liga.id})" 
                        class="botao-ranking-liga">
                    Ver Ranking
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

            try {
                const response = await fetch('php/leagues.php', {
                    method: 'POST',
                    body: formData
                });

                // PRIMEIRO pegue o texto bruto
                const textoBruto = await response.text();

                // Depois tente parsear como JSON
                try {
                    const jsonData = JSON.parse(textoBruto);
                    return jsonData;
                } catch (jsonError) {
                    console.error(`Erro ao parsear JSON para "${action}":`, jsonError.message);
                    console.error('Texto recebido:', textoBruto);

                    // Retorna erro padrão se não conseguir parsear
                    return {
                        success: false,
                        message: 'Resposta inválida do servidor'
                    };
                }

            } catch (error) {
                console.error(`Erro de rede/fetch para "${action}":`, error);
                return {
                    success: false,
                    message: 'Erro de conexão: ' + error.message
                };
            }
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
        }
    }

    // Inicia o sistema quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', () => {
        window.leagueSystem = new LeagueSystem();
    });
})();