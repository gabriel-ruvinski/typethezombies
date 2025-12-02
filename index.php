<?php
require "php/authenticate.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type the Zombies</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <?php if ($login): ?>
        <div class="tela tela-inicial">
            <h1>Type the Zombies</h1>
            <div class="menu">
                <button class="botao" id="btnJogar">Jogar</button>
                <button class="botao" id="btnLigas">Ligas</button>
                <button class="botao" id="btnHistorico">Histórico</button>
                <button class="botao" id="btnConfig">Configurações</button>
                <form action="php/logout.php" method="post" class="form-inline">
                    <button type="submit" class="botao">Logout</button>
                </form>
            </div>
            <p class="rodape">© 2025 Todos os direitos reservados.</p>
        </div>

    <?php else: ?>
        <div class="tela tela-auth">
            <div class="auth-header">
                <h2>Bem-vindo ao Type The Zombies</h2>
                <p>Escolha uma opção para continuar:</p>
            </div>
            <div class="auth-buttons">
                <a class="botao auth-btn" href="php/login.php">
                    <span>Entrar</span>
                    <small>Já tenho uma conta</small>
                </a>
                <a class="botao auth-btn" href="php/register.php">
                    <span>Cadastrar</span>
                    <small>Criar nova conta</small>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="tela tela-config" style="display: none;">
        <h2>Configurações</h2>

        <div class="config-opcao">
            <label for="musica">Volume da música</label>
            <input type="range" id="musica" min="0" max="100" value="70">
        </div>

        <div class="config-opcao">
            <label for="efeitos">Volume dos efeitos</label>
            <input type="range" id="efeitos" min="0" max="100" value="80">
        </div>

        <div class="config-opcao">
            <label for="dificuldade">Dificuldade</label>
            <select id="dificuldade">
                <option value="facil">Fácil</option>
                <option value="medio" selected>Médio</option>
                <option value="dificil">Difícil</option>
            </select>
        </div>

        <div class="config-opcao">
            <label for="tema">Tema</label>
            <select id="tema">
                <option value="padrao" selected>Padrão (Verde)</option>
                <option value="roxo">Roxo</option>
                <option value="vermelho">Vermelho</option>
            </select>
        </div>

        <div class="config-botoes">
            <button id="btnSalvar">Salvar</button>
            <button id="btnVoltar">Voltar</button>
        </div>
    </div>

    <div class="tela tela-jogo" style="display: none;">
        <div class="game-header">
            <div class="score">Pontuação: <span id="pontuacao">0</span></div>
            <div class="lives">Vidas: <span id="vidas">3</span></div>
            <div class="timer">Tempo: <span id="tempo">60</span>s</div>
        </div>

        <div class="game-area" id="gameArea">
        </div>

        <div class="input-area">
            <input type="text" id="inputPalavra" placeholder="Digite a palavra...">
            <div class="palavra-atual" id="palavraAtual"></div>
        </div>

        <button class="botao" id="btnVoltarJogo">Voltar ao Menu</button>
    </div>

    <div class="tela tela-historico" style="display: none;">
        <h2>Histórico de Partidas</h2>
        <table id="tabelaHistorico">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Pontuação</th>
                    <th>Duração</th>
                </tr>
            </thead>
            <tbody>
                <!--- Essa parte será preenchida com o main.js --->
            </tbody>
        </table>
        <button id="btnVoltarHistorico">Voltar</button>
    </div>

    <!-- TELA PRINCIPAL DAS LIGAS -->
    <div class="tela tela-ligas" style="display: none;">
        <div class="container-ligas">
            <h2>Ligas</h2>
            <div class="menu-ligas">
                <button class="botao" id="btnCriarLiga">Criar Liga</button>
                <button class="botao" id="btnEntrarLiga">Entrar em Liga</button>
                <button class="botao" id="btnMinhasLigas">Minhas Ligas</button>
                <button class="botao" id="btnIrParaRanking">Ranking</button>
                <button class="botao" id="btnVoltarLigas">Voltar</button>
            </div>
        </div>
    </div>

    <!-- TELA CRIAR LIGA -->
    <div class="tela tela-criar-liga" style="display: none;">
        <div class="container-config">
            <h2>Criar Nova Liga</h2>
            <form id="formCriarLiga" class="form-liga">
                <div class="form-group">
                    <label for="ligaNome">Nome da Liga:</label>
                    <input type="text" id="ligaNome" required placeholder="Digite o nome da liga">
                </div>
                <div class="form-group">
                    <label for="ligaSenha">Palavra-chave:</label>
                    <input type="password" id="ligaSenha" required placeholder="Crie uma senha para a liga">
                </div>
                <div class="form-botoes">
                    <button type="submit" class="botao">Criar Liga</button>
                    <button type="button" class="botao botao-voltar" id="btnVoltarCriarLiga">Voltar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TELA ENTRAR LIGA -->
    <div class="tela tela-entrar-liga" style="display: none;">
        <div class="container-config">
            <h2>Entrar em Liga</h2>
            <form id="formEntrarLiga" class="form-liga">
                <div class="form-group">
                    <label for="ligaID">ID da Liga:</label>
                    <input type="text" id="ligaID" required placeholder="Digite o ID da liga">
                </div>
                <div class="form-group">
                    <label for="ligaSenhaEntrada">Palavra-chave:</label>
                    <input type="password" id="ligaSenhaEntrada" required placeholder="Digite a senha da liga">
                </div>
                <div class="form-botoes">
                    <button type="submit" class="botao">Entrar na Liga</button>
                    <button type="button" class="botao botao-voltar" id="btnVoltarEntrarLiga">Voltar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TELA MINHAS LIGAS -->
    <div class="tela tela-minhas-ligas" style="display: none;">
        <div class="container-config">
            <h2>Minhas Ligas</h2>
            <div id="listaMinhasLigas" class="lista-ligas">
                <!-- As ligas serão carregadas aqui via JavaScript -->
                <p>Carregando suas ligas...</p>
            </div>
            <div class="form-botoes">
                <button class="botao botao-voltar" id="btnVoltarMinhasLigas">Voltar</button>
            </div>
        </div>
    </div>

    <div class="tela tela-ranking" style="display: none;">
        <div class="container-config">
            <h2>Sistema de Rankings</h2>
            <p>Escolha o tipo de ranking que deseja visualizar:</p>

            <div class="menu-ranking">
                <button class="botao ranking-opcao" id="btnRankingGeral">
                    Ranking Geral
                    <small>Todas as pontuações desde o início</small>
                </button>

                <button class="botao ranking-opcao" id="btnRankingSemanal">
                    Ranking Semanal
                    <small>Pontuações desta semana</small>
                </button>

                <button class="botao botao-voltar" id="btnVoltarRanking">Voltar</button>
            </div>
        </div>
    </div>

    <!-- TELA RANKING GERAL -->
    <div class="tela tela-ranking-geral" style="display: none;">
        <div class="container-config">
            <h2>Ranking Geral</h2>
            <table id="RankingGeralTabela" class="tabela-ranking">
                <thead>
                    <tr>
                        <th width="80">Posição</th>
                        <th>Jogador</th>
                        <th width="120">Pontos</th>
                        <th width="120">Partidas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">Carregando ranking...</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-botoes">
                <button class="botao botao-voltar" id="btnVoltarRankingGeral">Voltar</button>
            </div>
        </div>
    </div>

    <div class="tela tela-ranking-semanal" style="display: none;">
        <div class="container-config">
            <h2>Ranking Semanal</h2>
            <div class="periodo-info" id="periodoSemanal">
                <!-- Período será preenchido via JS -->
            </div>
            <table id="RankingSemanalTabela" class="tabela-ranking">
                <thead>
                    <tr>
                        <th width="80">Posição</th>
                        <th>Jogador</th>
                        <th width="120">Pontos</th>
                        <th width="120">Partidas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">Carregando ranking semanal...</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-botoes">
                <button class="botao botao-voltar" id="btnVoltarRankingSemanal">Voltar</button>
            </div>
        </div>
    </div>

    <!-- TELA RANKING LIGA -->
    <div class="tela tela-ranking-liga" style="display: none;">
        <div class="container-config">
            <h2>Ranking da Liga</h2>
            <div id="ligaNomeTitulo" class="liga-nome-titulo"></div>
            <table id="RankingLigaTabela" class="tabela-ranking">
                <thead>
                    <tr>
                        <th width="80">Posição</th>
                        <th>Jogador</th>
                        <th width="120">Pontos</th>
                        <th width="120">Partidas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">Selecione uma liga primeiro...</td>
                    </tr>
                </tbody>
            </table>
            <div class="form-botoes">
                <button class="botao botao-voltar" id="btnVoltarRankingLiga">Voltar</button>
            </div>
        </div>
    </div>

    <script src="scripts/main.js"></script>
    <script src="scripts/game.js"></script>
    <script src="scripts/leagues.js"></script>
</body>

</html>