document.addEventListener('DOMContentLoaded', function () {
    // Botão Salvar das configurações
    const btnSalvar = document.getElementById("btnSalvar");

    if (btnSalvar) {
        btnSalvar.addEventListener("click", function () {
            const musica = document.getElementById("musica").value;
            const efeitos = document.getElementById("efeitos").value;
            const dificuldade = document.getElementById("dificuldade").value;
            const tema = document.getElementById("tema").value;

            document.body.classList.remove("tema-padrao", "tema-roxo", "tema-vermelho");
            switch (tema) {
                case "roxo": document.body.classList.add("tema-roxo"); break;
                case "vermelho": document.body.classList.add("tema-vermelho"); break;
                default: document.body.classList.add("tema-padrao"); break;
            }
        });
    }
});

/* REFERENCIANDO AS TELAS */
const telas = {
    inicial: document.querySelector(".tela-inicial"),
    config: document.querySelector(".tela-config"),
    jogo: document.querySelector(".tela-jogo"),
    login: document.querySelector(".tela-login"),
    registro: document.querySelector(".tela-registro"),
    historico: document.querySelector(".tela-historico"),
    ligas: document.querySelector(".tela-ligas"),
    ranking: document.querySelector(".tela-ranking"),
    rankingSemanal: document.querySelector(".tela-ranking-semanal"),
    rankingGeral: document.querySelector(".tela-ranking-geral"),
    rankingLiga: document.querySelector(".tela-ranking-liga"),
    criarLiga: document.querySelector(".tela-criar-liga"),
    entrarLiga: document.querySelector(".tela-entrar-liga"),
    minhasLigas: document.querySelector(".tela-minhas-ligas")
};

/* FUNÇÃO PARA TROCA DE TELAS */
function mostrarTela(nomeTela) {
    document.querySelectorAll(".tela").forEach(t => t.style.display = "none");
    if (telas[nomeTela]) {
        telas[nomeTela].style.display = "block";
    } else {
        console.error("Tela não encontrada: ", nomeTela);
    }
}

/* SISTEMA DE NAVEGAÇÃO - VERSÃO CORRIGIDA */
document.addEventListener('click', function (e) {
    const botao = e.target;

    // BOTÕES DA TELA INICIAL
    if (botao.id === 'btnConfig') mostrarTela('config');
    if (botao.id === 'btnHistorico') mostrarTela('historico');
    if (botao.id === 'btnLigas') mostrarTela('ligas');
    if (botao.id === 'btnJogar') {
        // Inicia o jogo
        if (typeof window.gameIniciar === 'function') {
            window.gameIniciar();
        } else {
            mostrarTela('jogo');
        }
    }

    // BOTÕES DE VOLTAR ESPECÍFICOS
    if (botao.id === 'btnVoltarLigas') mostrarTela('inicial');
    if (botao.id === 'btnVoltarRankingGeral') mostrarTela('ligas');
    if (botao.id === 'btnVoltarRankingLiga') mostrarTela('ligas');
    if (botao.id === 'btnVoltarCriarLiga') mostrarTela('ligas');
    if (botao.id === 'btnVoltarEntrarLiga') mostrarTela('ligas');
    if (botao.id === 'btnVoltarMinhasLigas') mostrarTela('ligas');

    // BOTÕES DE VOLTAR GENÉRICOS (voltar para inicial)
    if (botao.id === 'btnVoltar' ||
        botao.id === 'btnVoltarJogo' ||
        botao.id === 'btnVoltarHistorico' ||
        botao.id === 'btnVoltarConfig') {
        mostrarTela('inicial');
    }

    // BOTÕES DAS LIGAS (serão tratados pelo leagues.js)
    if (botao.id === 'btnCriarLiga') {
        if (window.leagueSystem && typeof window.leagueSystem.mostrarCriarLiga === 'function') {
            window.leagueSystem.mostrarCriarLiga();
        } else {
            mostrarTela('criarLiga');
        }
    }

    if (botao.id === 'btnEntrarLiga') {
        if (window.leagueSystem && typeof window.leagueSystem.mostrarEntrarLiga === 'function') {
            window.leagueSystem.mostrarEntrarLiga();
        } else {
            mostrarTela('entrarLiga');
        }
    }

    if (botao.id === 'btnMinhasLigas') {
        if (window.leagueSystem && typeof window.leagueSystem.carregarMinhasLigas === 'function') {
            window.leagueSystem.carregarMinhasLigas();
        } else {
            mostrarTela('minhasLigas');
        }
    }

    if (botao.id === 'btnRanking') {
        mostrarTela('ranking');
    }

    if (botao.id === 'btnRankingSemanal') {
        if (window.leagueSystem) {
            window.leagueSystem.carregarRankingSemanal();
        }
    }

    if (botao.id === 'btnRankingGeral') {
        if (window.leagueSystem) {
            window.leagueSystem.carregarRankingGeral();
        }
    }

    // Botões de voltar
    if (botao.id === 'btnVoltarRanking') {
        mostrarTela('ligas');
    }

    if (botao.id === 'btnVoltarRankingSemanal') {
        mostrarTela('ranking');
    }

    if (botao.id === 'btnVoltarRankingGeral') {
        mostrarTela('ranking');
    }

    if (botao.id === 'btnVoltarRankingLiga') {
        mostrarTela('ranking');
    }

    if (botao.id === 'btnIrParaRanking') {
        mostrarTela('ranking');
    }
});