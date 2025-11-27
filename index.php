<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type the Zombies</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div class="tela tela-inicial">
        <h1>Type the Zombies</h1>
        <div class="menu">
            <button class="botao" id="btnJogar">Jogar</button>
            <button class="botao" id="btnStats">Estatísticas</button>
            <button class="botao" id="btnConfig">Configurações</button>
            <div class="auth-container">
                <button class="auth" id="btnLogin">Entrar</button>
                <button class="auth" id="btnRegistro">Registrar-se</button>
            </div>    
        </div>
        <p class="rodape">© 2025 Todos os direitos reservados.</p>
    </div>

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

    <div class="tela tela-login" style="display: none;">
        <h2>Login</h2>
        <form id="formLogin">
            <label>Email:</label>
            <input type="email" id="loginEmail" required>

            <label>Senha:</label>
            <input type="password" id="loginSenha" required>

            <button type="submit" id="btnEntrarLogin">Entrar</button>
            <button type="button" id="btnVoltarLogin">Voltar</button>
        </form>
    </div>

    <div class="tela tela-registro" style="display: none;">
        <h2>Registrar-se</h2>
        <form id="formRegistro">
            <label>Nome de Usuário:</label>
            <input type="text" id="regUsuario" required>
            
            <label>Email:</label>
            <input type="email" id="regEmail" required>

            <label>Senha:</label>
            <input type="password" id="regSenha" required>

            <button type="submit" id="btnCriarConta">Criar Conta</button>
            <button type="button" id="btnVoltarRegistro">Voltar</button>
        </form>
    </div>

    <div class="tela tela-estatisticas" style="display: none;">
        <h2>Estatísticas Gerais</h2>

        <div id="estatisticasConteudo">
            <p>Total de partidas: 0</p>
            <p>Maior pontuação: 0</p>
            <p>Média de pontos: 0</p>
        </div>

        <button id="btnVoltarStats">Voltar</button>
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
                <!--- Essa parte vai ser preenchida utilizando PHP --->
            </tbody>
        </table>
        <button id="btnVoltarHistorico">Voltar</button>
    </div>

    <div class="tela tela-ligas" style="display: none;">
        <h2>Ligas</h2>
        <div class="menu-ligas">
            <button id="btnCriarLiga">Criar liga</button>
            <button id="btnEntrarLiga">Entrar em liga</button>
            <button id="btnMinhasLigas">Minhas ligas</button>
            <button id="btnVoltarLigas">Voltar</button>
        </div>
        
        <div class="tela-criar-liga" style="display: none;">
            <h3>Criar liga</h3>
            <form id="formCriarLiga">
                <label>Nome da liga</label>
                <input type="text" id="ligaNome" required>

                <label>Palavra-chave</label>
                <input type="password" id="ligaSenha" required>
                
                <button type="submit">Criar</button>
                <button type="button" id="btnVoltarCriarLiga">Voltar</button>
            </form>
        </div>

        <div class="tela-entrar-liga" style="display: none;">
            <h3>Entrar em Liga</h3>
            <form id="formEntrarLiga">
                <label>ID da Liga</label>
                <input type="text" id="ligaID" required>

                <label>Palavra-chave</label>
                <input type="password" id="ligaSenhaEntrada" required>

            <button type="submit">Entrar</button>
            <button type="button" id="btnVoltarEntrarLiga">Voltar</button>
            </form>
        </div>

        <div class="tela-minhas-ligas" style="display: none;">
            <h3>Minhas Ligas</h3>

            <ul id="listaMinhasLigas">
            <!-- Ligas do usuário aparecem aqui -->
            </ul>

            <button id="btnVoltarMinhasLigas">Voltar</button>
        </div>
    </div>

    <div class="tela tela-ranking-geral" style="display: none;">
        <h2>Ranking Geral</h2>
        <table id="RankingGeralTabela">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Jogador</th>
                    <th>Pontos</th>
                </tr>
            </thead>
            <tbody>
                <!--Essa parte será preenchida com o PHP-->
            </tbody>
        </table>
        <button id="btnVoltarRankingGeral">Voltar</button>
    </div>

    <div class="tela tela-ranking-liga" style="display: none;">
        <h2>Ranking da Liga</h2>
        <table id="RankingLigaTabela">
            <thead>
                <tr>
                    <th>Posição</th>
                    <th>Jogador</th>
                    <th>Pontos</th>
                </tr>
            </thead>
            <tbody>
                <!---Essa parte será preenchida com o PHP-->
            </tbody>
        </table>
        <button id="btnVoltarRankingLiga">Voltar</button>
    </div>

    <script src="scripts/main.js"></script>
</body>
</html>
