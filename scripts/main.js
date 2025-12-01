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
    if (botao.id === 'btnHistorico') mostrarTela('historico');
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
    if (botao.id === 'btnMinhasLigas') {
        mostrarTela('minhasLigas');
        carregarMinhasLigas();
    }
    
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

/* TELA DE LIGAS */
    //SUBTELA "CRIAR LIGA"
    const formCriarLiga = document.getElementById("formCriarLiga");
    const ligaMsg = document.createElement('p');
    ligaMsg.style.color = 'red'; 
    formCriarLiga.appendChild(ligaMsg); // mensagem abaixo do formulário

    formCriarLiga.addEventListener("submit", async function(e) {
    e.preventDefault();
    ligaMsg.textContent = "Carregando...";

    const league_name = document.getElementById("ligaNome").value.trim();
    const league_key = document.getElementById("ligaSenha").value.trim();

    const formData = new FormData();
    formData.append("league_name", league_name);
    formData.append("league_key", league_key);

    try {
        const response = await fetch("php/create_league.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (data.status === "empty" || data.status === "exists" || data.status === "error") {
            ligaMsg.style.color = 'red';
            ligaMsg.textContent = data.message;
        } else if (data.status === "success") {
            ligaMsg.style.color = 'green';
            ligaMsg.textContent = data.message;

            // Atualiza a lista de Minhas Ligas
            carregarMinhasLigas();

            // Limpa campos
            formCriarLiga.reset();
        }

    } catch (error) {
        ligaMsg.style.color = 'red';
        ligaMsg.textContent = "Erro ao conectar ao servidor.";
        console.error(error);
        }
    });

    //SUBTELA "ENTRAR EM UMA LIGA"
    const formEntrarLiga = document.getElementById("formEntrarLiga");
    const joinMsg = document.createElement('p');
    joinMsg.style.color = 'red';
    formEntrarLiga.appendChild(joinMsg);

    formEntrarLiga.addEventListener("submit", async function(e) {
    e.preventDefault();
    joinMsg.textContent = "Carregando...";

    const league_id = document.getElementById("ligaID").value.trim();
    const league_key = document.getElementById("ligaSenhaEntrada").value.trim();

    const formData = new FormData();
    formData.append("league_id", league_id);
    formData.append("league_key", league_key);

    try {
        const response = await fetch("php/join_league.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (data.status === "empty" || data.status === "notfound" || data.status === "error") {
            joinMsg.style.color = 'red';
            joinMsg.textContent = data.message;
        } else if (data.status === "success") {
            joinMsg.style.color = 'green';
            joinMsg.textContent = data.message;

            // Atualiza lista de Minhas Ligas
            carregarMinhasLigas();

            formEntrarLiga.reset();
        }

    } catch (err) {
        joinMsg.style.color = 'red';
        joinMsg.textContent = "Erro ao conectar ao servidor.";
        console.error(err);
        }
    });

    //SUBTELA "MINHAS LIGAS"
    async function carregarMinhasLigas() {
    const lista = document.getElementById('listaMinhasLigas');
    lista.innerHTML = '<li>Carregando ligas...</li>';

    try {
        const response = await fetch('php/get_user_leagues.php');
        if (!response.ok) throw new Error('Erro ao acessar o servidor');

        const ligas = await response.json();

        if (ligas.length === 0) {
            lista.innerHTML = '<li>Você ainda não participa de nenhuma liga.</li>';
            return;
        }

        lista.innerHTML = '';
        ligas.forEach(liga => {
            const li = document.createElement('li');
            li.textContent = liga.league_name;

            // Botão "Classificações" que podemos implementar depois
            const btnRanking = document.createElement('button');
            btnRanking.textContent = 'Classificações';
            btnRanking.style.marginLeft = '10px';
            btnRanking.addEventListener('click', () => {
                // Aqui você pode chamar a função que mostra o ranking da liga
                alert(`Aqui irá abrir o ranking da liga "${liga.league_name}"`);
            });

            li.appendChild(btnRanking);
            lista.appendChild(li);
        });

    } catch (error) {
        console.error(error);
        lista.innerHTML = '<li>Erro ao carregar ligas.</li>';
    }
}
