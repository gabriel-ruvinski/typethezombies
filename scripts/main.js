document.addEventListener('DOMContentLoaded', function() {
    // Botão Salvar das configurações
    const btnSalvar = document.getElementById("btnSalvar");

    if (btnSalvar) {
        btnSalvar.addEventListener("click", function() {
            const musica = document.getElementById("musica").value;
            const efeitos = document.getElementById("efeitos").value;
            const dificuldade = document.getElementById("dificuldade").value;
            const tema = document.getElementById("tema").value;

            document.body.classList.remove("tema-padrao", "tema-roxo", "tema-vermelho");
            switch(tema) {
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
    estatisticas: document.querySelector(".tela-estatisticas"),
    historico: document.querySelector(".tela-historico"),
    ligas: document.querySelector(".tela-ligas"),
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

/* SISTEMA DE NAVEGAÇÃO */
document.addEventListener('click', function(e) {
    const botao = e.target;
    
    // 🔹 BOTÕES DA TELA INICIAL
    if (botao.id === 'btnConfig') mostrarTela('config');
    if (botao.id === 'btnStats') mostrarTela('estatisticas');
    if (botao.id === 'btnLogin') mostrarTela('login');
    if (botao.id === 'btnRegistro') mostrarTela('registro');
    if (botao.id === 'btnJogar') mostrarTela('jogo');
    
    // BOTÕES DE VOLTAR (todos voltam para inicial)
    if (botao.id.includes('btnVoltar')) {
        mostrarTela('inicial');
    }
    
    // BOTÕES DAS LIGAS
    if (botao.id === 'btnCriarLiga') mostrarTela('criarLiga');
    if (botao.id === 'btnEntrarLiga') mostrarTela('entrarLiga');
    if (botao.id === 'btnMinhasLigas') mostrarTela('minhasLigas');
    
    // BOTÕES DE VOLTAR DAS SUBTELAS (volta para ligas)
    if (botao.id === 'btnVoltarCriarLiga') mostrarTela('ligas');
    if (botao.id === 'btnVoltarEntrarLiga') mostrarTela('ligas');
    if (botao.id === 'btnVoltarMinhasLigas') mostrarTela('ligas');
});

// TELA DE HISTÓRICO
document.getElementById("btnHistorico").addEventListener("click", function () {

    fetch("get_history.php")
        .then(response => response.json())
        .then(data => {

            let tabela = document.querySelector("#tabelaHistorico tbody");
            tabela.innerHTML = "";

            if (data.length === 0) {
                tabela.innerHTML = `
                    <tr>
                        <td colspan="3">Nenhuma partida encontrada.</td>
                    </tr>
                `;
                trocarTela("tela-historico");
                return;
            }

            data.forEach(match => {
                let dataFormatada = new Date(match.match_date).toLocaleString("pt-BR");
                let linha = document.createElement("tr");
                linha.innerHTML = `
                    <td>${dataFormatada}</td>
                    <td>${match.points}</td>
                    <td>${match.match_time}s</td>
                `;
                tabela.appendChild(linha);
            });

            trocarTela("tela-historico");
        });
});

// BOTÃO VOLTAR
document.getElementById("btnVoltarHistorico").addEventListener("click", function () {
    trocarTela('inicial');
});
