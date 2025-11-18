<?php
    require('credentials.php');

    // Conexão
    $conn = mysqli_connect($servername, $username, $password);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Criar banco
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (mysqli_query($conn, $sql)) {
        echo "Database '$dbname' created successfully<br>";
    } else {
        echo "Error creating database: " . mysqli_error($conn) . "<br>";
    }

    // Selecionar banco
    $sql = "USE $dbname";
    if (mysqli_query($conn, $sql)) {
        echo "Database selected successfully<br>";
    } else {
        echo "Error selecting database: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: usuarios
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        senha VARCHAR(150) NOT NULL
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'usuarios' created successfully<br>";
    } else {
        echo "Error creating table usuarios: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: ligas
    $sql = "CREATE TABLE IF NOT EXISTS ligas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        palavra_chave VARCHAR(100) NOT NULL,
        id_criador INT NOT NULL,
        FOREIGN KEY (id_criador) REFERENCES usuarios(id) ON DELETE CASCADE
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'ligas' created successfully<br>";
    } else {
        echo "Error creating table ligas: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: usuarios_ligas
    $sql = "CREATE TABLE IF NOT EXISTS usuarios_ligas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        id_liga INT NOT NULL,
        UNIQUE KEY unico_usuario_liga (id_usuario, id_liga),
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (id_liga) REFERENCES ligas(id) ON DELETE CASCADE
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'usuarios_ligas' created successfully<br>";
    } else {
        echo "Error creating table usuarios_ligas: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: partidas
    $sql = "CREATE TABLE IF NOT EXISTS partidas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        pontuacao INT NOT NULL DEFAULT 0,
        data_partida DATETIME DEFAULT CURRENT_TIMESTAMP,
        duracao_partida INT DEFAULT NULL,
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'partidas' created successfully<br>";
    } else {
        echo "Error creating table partidas: " . mysqli_error($conn) . "<br>";
    }

    mysqli_close($conn);
?>
