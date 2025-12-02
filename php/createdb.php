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
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        user_password VARCHAR(150) NOT NULL
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'users' created successfully<br>";
    } else {
        echo "Error creating table users: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: ligas
    $sql = "CREATE TABLE IF NOT EXISTS leagues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        league_name VARCHAR(100) NOT NULL,
        league_key VARCHAR(100) NOT NULL,
        creator_id INT NOT NULL,
        FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'leagues' created successfully<br>";
    } else {
        echo "Error creating table leagues: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: usuarios_ligas
    $sql = "CREATE TABLE IF NOT EXISTS users_leagues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        league_id INT NOT NULL,
        UNIQUE KEY unique_user_league (user_id, league_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'users_leagues' created successfully<br>";
    } else {
        echo "Error creating table users_leagues: " . mysqli_error($conn) . "<br>";
    }

    // Tabela: partidas
    $sql = "CREATE TABLE IF NOT EXISTS matches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        points INT NOT NULL DEFAULT 0,
        match_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        match_time INT DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";

    if (mysqli_query($conn, $sql)) {
        echo "Table 'matches' created successfully<br>";
    } else {
        echo "Error creating table matches: " . mysqli_error($conn) . "<br>";
    }

    mysqli_close($conn);
?>
