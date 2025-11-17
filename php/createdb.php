-- Criar o banco
DROP DATABASE IF EXISTS typezombies;
CREATE DATABASE typezombies;
USE typezombies;

-- Tabela dos Usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(150) NOT NULL
);


-- Tabela das Ligas
CREATE TABLE ligas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    palavra_chave VARCHAR(100) NOT NULL,
    id_criador INT NOT NULL,

    FOREIGN KEY (id_criador) REFERENCES usuarios(id)
);


-- Tabela Usuário na Liga
CREATE TABLE usuarios_ligas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_liga INT NOT NULL,

    -- usuário só pode entrar 1 vez na mesma liga
    UNIQUE KEY unico_usuario_liga (id_usuario, id_liga),

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
    FOREIGN KEY (id_liga) REFERENCES ligas(id)
);


-- Tabela das Partidas
CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    pontuacao INT NOT NULL DEFAULT 0,
    data_partida DATETIME DEFAULT CURRENT_TIMESTAMP,
    duracao_partida INT DEFAULT NULL,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);