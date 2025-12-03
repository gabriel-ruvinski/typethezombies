# Relatório de Uso de Inteligência Artificial Generativa

Este documento registra todas as interações significativas com ferramentas de IA generativa (como Gemini, ChatGPT, Copilot, etc.) durante o desenvolvimento deste projeto. O objetivo é promover o uso ético e transparente da IA como ferramenta de apoio, e não como substituta para a compreensão dos conceitos fundamentais.

## Política de Uso
O uso de IA foi permitido para as seguintes finalidades:
- Geração de ideias e brainstorming de algoritmos.
- Explicação de conceitos complexos.
- Geração de código boilerplate (ex: estrutura de classes, leitura de arquivos).
- Sugestões de refatoração e otimização de código.
- Debugging e identificação de causas de erros.
- Geração de casos de teste.

É proibido submeter código gerado por IA sem compreendê-lo completamente e sem adaptá-lo ao projeto. Todo trecho de código influenciado pela IA deve ser referenciado neste log.

---

## Registro de Interações

1) **Data:** 20/11/2025

**Etapa do Projeto:** Estruturação dos arquivos do trabalho

**Ferramenta de IA Utilizada:** ChatGPT

**Objetivo da Consulta:** Procurar a melhor estrutura para o trabalho, com todos os scripts necessários

**Prompt(s) Utilizado(s):** Para a construção de um jogo de digitação, com um sistema inicial de login com vários usuários, que podem acessar seu histórico, participar de ligas e podem ser rankeados, qual estrutura de arquivos poderia ser interessante?
Pensei em uma estrutura do tipo:
index.php -> para a estrutura do trabalho
styles.css -> para estilização
javascripts -> pode ser um único ou você acha que separar um para cada etapa seria melhor (exemplo: um para o jogo em si, outro para navegação)?
credentials.php -> php necessário para acessar o banco
db_functions.php -> php com funções que podem ser úteis na conexão
createdb.php -> php para criação do banco
register.php -> php para o registro de uma conta
login.php -> php para login do usuário
logout.php -> php para logout do usuário
save_match.php -> para salvamento das partidas no banco
get_user_history.php -> para histórico do usuário
phps de liga -> para criação ou entrada em uma liga
phps de ranking -> acesso aos rankings

**Resumo da Resposta da IA:** A IA, após analisar a situação, elogiou a estruturação pensada pela equipe, reforçou a importância dos PHPs para cada etapa, como login, ligas e rankings. Em seguida, ela citou alguns scripts que poderiam ser importantes:
-2 javascripts: o primeiro para tratar da animação do jogo em si, e outro para a parte técnica do projeto, como conexões com o banco.
-3 PHPs de liga: o primeiro para criação da liga, o segundo para entrar em uma liga e por último, um que apresenta as ligas que o usuário participa.
-PHPs de ranking: a IA disse que dependia de quantos rankings a gente iria fazer, mas recomendou 1 para cada tipo de ranking.  

**Análise e Aplicação:** A resposta da IA auxiliou a equipe na idealização da árvore de arquivos do trabalho. Após a resposta, o grupo discutiu acerca das ideias trazidas pela IA e conseguiu ter um ponto de partida na criação dos arquivos.

**Referência no Código:** As ideias trazidas pela IA foram implementadas no trabalho como um todo, representadas na estrutura do mesmo. A referência dessa interação está neste mesmo arquivo.


2) **Data:** 22/11/2025
   
**Etapa do Projeto:** Criação do banco de dados

**Ferramenta de IA Utilizada:** ChatGPT

**Objetivo da Consulta:** Revisão no banco de dados e conferir se as tabelas estão com falhas.

**Prompt(s) Utilizado(s):** Foi explicado à IA quais eram os atributos que cada tabela criada pela equipe tinha e perguntado se seria necessário mais algum atributo (inicialmente, foram citadas as tabelas iniciais do projeto -> users, matches, users_leagues).

**Resumo da Resposta da IA:** A IA disse que as tabelas criadas eram suficientes, mas recomendou duas estruturas importantes dentro do código createdb.php (que foi comentado), chamadas ON DELETE CASCADE e ON DELETE SET NULL para as FOREIGN KEY de cada tabela. Após perguntarmos qual a função dessa estrutura, ela explicou o funcionamento e pra que servia.

**Análise e Aplicação:** Após a recomendação da IA, o grupo manteve as tabelas e os atributos que já haviam sido definidos por nós, mas incluiu as estruturas ON DELETE CASCADE e ON DELETE SET NULL nas chaves estrangeiras da tabela.

**Referência no Código:** Os comandos foram implementados no script createdb.php, nas tabelas:
leagues (linha 70);
users_leagues (linhas 92 e 93);
matches (linhas 117 e 118);

3) **Data:** 26/11/2025
   
**Etapa do projeto:** Animação do jogo

**Ferramenta de IA Utilizada**: ChatGPT

**Objetivo da Consulta:** Buscar maneiras de sistematizar nosso jogo de forma com que, quando o usuário clica em um botão, a tela é ocultada e outra aparece, de forma com que o jogo todo funcione em uma só aba.

**Prompt(s) Utilizado(s):** O sistema de telas que o grupo pensou para o trabalho foi um que funciona da seguinte maneira: enquanto uma tela aparece, todas as outras estão ocultas, e as telas vão surgindo conforme as interações do usuário (exemplo: usuário clica no botão configurações da página inicial -> a caixa inicial "some" e aparece a caixa de configurações).

**Resumo da Resposta da IA:** A IA sugeriu e explicou uma forma de criar esse sistema utilizando uma função criada no JavaScript, que transformaria cada tela em um elemento, que seria ocultada ou mostrada dependendo da interação.

**Análise e Aplicação:** Após o auxílio da IA, foi discutida a melhor forma de construir a função do sistema de telas, aplicando o conceito de transformar as telas em elementos, como a IA citou.

**Referência no Código:** Toda a aplicação do sistema foi implementada no script main.js (linha 22 a 144). Essa parte do script contém o conjunto telas (descreve todas as telas), a função mostrarTela (troca as telas) e a animação de todos os botões e telas (página inicial, páginas secundárias, e botões de "Voltar")

4) **Data:** 27/11/2025
   
**Etapa do projeto:** Estilização do jogo

**Ferramenta de IA Utilizada:** ChatGPT

**Objetivo da Consulta:** Buscar melhorias e revisar os estilos do jogo

**Prompt(s) Utilizado(s):** Pensamos em estilizar os elementos dessa forma: (breve descrição dos botões e das telas, além de uma imagem da página inicial). 

**Resumo da Resposta da IA:** A IA primeiramente reforçou que a estilização estava muito boa e combinava com a temática do jogo. Depois, sugeriu pequenas melhorias, como tamanho da borda, sombras e temas de cores diferentes.

**Análise e Aplicação:** As pequenas melhorias foram aceitas rapidamente pelo grupo e implementadas na estilização. A opção de diferentes temáticas foi discutida e depois implementada também, vista como um adicional importante na parte visual do jogo.

**Referência no Código:**
1)Melhorias: as melhorias solicitadas pela IA estão dispostas por todo o styles.css, alguns exemplos são a sombra padrão (linha 26), transition dos botões (linha 80), pequenos ajustes em witdh e padding (linhas 142 e 143, por exemplo).
2)Diferentes temáticas (cores): a ideia de inserir temas diferentes foi implementada tanto no styles.css, no índice "1.TEMA E VARIÁVEIS" quanto no main.js, dentro da estrutura inicial do script "document.addEventListener('DOMContentLoaded', function ()", no comando switch case (linhas 13 a 16).

5) **Data:** 30/11/2025
    
**Etapa do projeto:** Funcionamento do jogo

**Ferramenta de IA Utilizada:** ChatGPT

**Objetivo da Consulta:** Buscar uma otimização no sistema de carregamento de palavras

**Prompt(s) Utilizado(s):** Atualmente, o sistema de palavras consiste em uma função básica de carregar palavras que estão em 3 vetores (por níveis de dificuldade). Além disso, em casos de erro, não aparecem palavras na tela. Existe alguma maneira de garantir maior otimização?

**Resumo da Resposta da IA:** A IA sugeriu a criação de um .json que contenha as palavras (ainda organizadas em vetores por dificuldade), uma função async para carregamento das palavras e uma estrutura que envia algumas palavras como "backup" caso exista erro no carregamento.

**Análise e Aplicação:** O grupo analisou e decidiu por implementar todas as melhorias e mudanças que a IA. A criação de um novo arquivo e as mudanças na função de carregamento das palavras.

**Referência no Código:** A criação do script words.json (dentro da pasta "data") foi feita exatamente de acordo com a sugestão da IA. Além disso, a função carregarPalavras (linha 21 a 41 do game.js) sofreu mudanças, com a adição das linhas 38 e 39, para as palavras de "emergência".
