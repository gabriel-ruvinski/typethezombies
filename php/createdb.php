<?php
require('credentials.php');

// ATIVAR ERROS PARA DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão
$conn = mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Criar banco de dados
$sql = "CREATE DATABASE IF NOT EXISTS $dbname 
            CHARACTER SET utf8mb4 
            COLLATE utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Database '$dbname' criado com sucesso<br>";
} else {
    echo "Erro criando database: " . mysqli_error($conn) . "<br>";
}

// Selecionar banco
$sql = "USE $dbname";
if (mysqli_query($conn, $sql)) {
    echo "Database selecionado com sucesso<br>";
} else {
    echo "Erro selecionando database: " . mysqli_error($conn) . "<br>";
}

echo "<hr>";
echo "<h3>CRIANDO TABELAS:</h3>";

// NOTA: TABELAS CONTÉM INFORMAÇÕES EXTRAS AINDA NÃO UTILIZADAS, PARA CASO A GENTE FOR FAZER ALGO NO FUTURO

// ============================================
// 1. TABELA: USUÁRIOS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        user_password VARCHAR(150) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_points INT DEFAULT 0,
        total_matches INT DEFAULT 0,
        INDEX idx_user_email (email),
        INDEX idx_user_points (total_points)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'users' criada com sucesso<br>";
} else {
    echo "Erro criando tabela users: " . mysqli_error($conn) . "<br>";
}

// ============================================
// 2. TABELA: LIGAS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS leagues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        league_name VARCHAR(100) NOT NULL,
        league_key VARCHAR(100) NOT NULL,
        creator_id INT NOT NULL,
        description TEXT,
        max_members INT DEFAULT 50,
        is_private BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_league_name (league_name),
        INDEX idx_league_creator (creator_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'leagues' criada com sucesso<br>";
} else {
    echo "Erro criando tabela leagues: " . mysqli_error($conn) . "<br>";
}

// ============================================
// 3. TABELA: USUÁRIOS_LIGAS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS users_leagues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        league_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_admin BOOLEAN DEFAULT FALSE,
        user_points_in_league INT DEFAULT 0,
        UNIQUE KEY unique_user_league (user_id, league_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
        INDEX idx_user_league (user_id, league_id),
        INDEX idx_league_user (league_id, user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'users_leagues' criada com sucesso<br>";
} else {
    echo "Erro criando tabela users_leagues: " . mysqli_error($conn) . "<br>";
}

// ============================================
// 4. TABELA: PARTIDAS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points INT NOT NULL DEFAULT 0,
    match_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    match_time INT DEFAULT NULL,
    words_typed INT DEFAULT 0,
    zombies_killed INT DEFAULT 0,
    accuracy DECIMAL(5,2) DEFAULT 0.00,
    league_id INT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE SET NULL, 
    INDEX idx_match_user (user_id),
    INDEX idx_match_date (match_date),
    INDEX idx_match_league (league_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'matches' criada com sucesso<br>";
} else {
    echo "Erro criando tabela matches: " . mysqli_error($conn) . "<br>";
}

// ============================================
// 5. TABELA: PONTUAÇÕES NAS LIGAS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS league_scores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        league_id INT NOT NULL,
        total_points INT DEFAULT 0,
        matches_played INT DEFAULT 0,
        average_score DECIMAL(10,2) DEFAULT 0.00,
        last_played TIMESTAMP NULL,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_league_score (user_id, league_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
        INDEX idx_league_scores (league_id, total_points DESC),
        INDEX idx_user_scores (user_id, league_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'league_scores' criada com sucesso<br>";
} else {
    echo "Erro criando tabela league_scores: " . mysqli_error($conn) . "<br>";
}

// ============================================
// 6. TABELA: CONVITES PARA LIGAS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS league_invites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        league_id INT NOT NULL,
        invited_email VARCHAR(120) NOT NULL,
        invite_code VARCHAR(50) NOT NULL,
        invited_by INT NOT NULL,
        status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 7 DAY),
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
        FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_invite_code (invite_code),
        INDEX idx_invite_status (status),
        INDEX idx_invite_email (invited_email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'league_invites' criada com sucesso<br>";
} else {
    echo "Erro criando tabela league_invites: " . mysqli_error($conn) . "<br>";
}

// ============================================
// 7. TABELA: HISTÓRICO DE ATIVIDADES DAS LIGAS
// ============================================
$sql = "CREATE TABLE IF NOT EXISTS league_activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        league_id INT NOT NULL,
        user_id INT NOT NULL,
        activity_type ENUM('join', 'leave', 'match', 'promotion', 'demotion') NOT NULL,
        activity_description TEXT,
        points_change INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (league_id) REFERENCES leagues(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_league_activities (league_id, created_at DESC),
        INDEX idx_user_activities (user_id, league_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($conn, $sql)) {
    echo "Tabela 'league_activities' criada com sucesso<br>";
} else {
    echo "Erro criando tabela league_activities: " . mysqli_error($conn) . "<br>";
}

echo "<hr>";
echo "<h3>PROCEDURES E TRIGGERS:</h3>";

// ============================================
// PROCEDURE: Atualizar pontuação total do usuário
// ============================================
$sql = "DROP PROCEDURE IF EXISTS update_user_total_points";
mysqli_query($conn, $sql);

$sql = "CREATE PROCEDURE update_user_total_points(IN user_id_param INT)
    BEGIN
        UPDATE users u
        SET u.total_points = (
            SELECT COALESCE(SUM(points), 0) 
            FROM matches 
            WHERE user_id = user_id_param
        ),
        u.total_matches = (
            SELECT COUNT(*) 
            FROM matches 
            WHERE user_id = user_id_param
        )
        WHERE u.id = user_id_param;
    END";

if (mysqli_query($conn, $sql)) {
    echo "Procedure 'update_user_total_points' criada com sucesso<br>";
} else {
    echo "Erro criando procedure: " . mysqli_error($conn) . "<br>";
}

// ============================================
// TRIGGER: Atualizar pontuação após nova partida
// ============================================
$sql = "DROP TRIGGER IF EXISTS after_match_insert";
mysqli_query($conn, $sql);

$sql = "CREATE TRIGGER after_match_insert 
    AFTER INSERT ON matches
    FOR EACH ROW
    BEGIN
        CALL update_user_total_points(NEW.user_id);
            IF NEW.league_id IS NOT NULL THEN
            INSERT INTO league_scores (user_id, league_id, total_points, matches_played, last_played)
            VALUES (NEW.user_id, NEW.league_id, NEW.points, 1, NEW.match_date)
            ON DUPLICATE KEY UPDATE
                total_points = total_points + NEW.points,
                matches_played = matches_played + 1,
                average_score = total_points / matches_played,
                last_played = NEW.match_date,
                last_updated = CURRENT_TIMESTAMP;
                
            INSERT INTO league_activities (league_id, user_id, activity_type, activity_description, points_change)
            VALUES (NEW.league_id, NEW.user_id, 'match', 
                    CONCAT('Partida realizada: ', NEW.points, ' pontos'), 
                    NEW.points);
        END IF;
    END";

if (mysqli_query($conn, $sql)) {
    echo "Trigger 'after_match_insert' criado com sucesso<br>";
} else {
    echo "Erro criando trigger: " . mysqli_error($conn) . "<br>";
}

// ============================================
// TRIGGER: Atualizar quando usuário entra na liga
// ============================================
$sql = "DROP TRIGGER IF EXISTS after_user_league_join";
mysqli_query($conn, $sql);

$sql = "CREATE TRIGGER after_user_league_join
    AFTER INSERT ON users_leagues
    FOR EACH ROW
    BEGIN
        INSERT INTO league_activities (league_id, user_id, activity_type, activity_description)
        VALUES (NEW.league_id, NEW.user_id, 'join', 'Usuário entrou na liga');
    END";

if (mysqli_query($conn, $sql)) {
    echo "Trigger 'after_user_league_join' criado com sucesso<br>";
} else {
    echo "Erro criando trigger: " . mysqli_error($conn) . "<br>";
}

// ============================================
// VIEW: Ranking geral de jogadores
// ============================================
$sql = "CREATE OR REPLACE VIEW view_ranking_geral AS
    SELECT 
        u.id,
        u.username,
        u.email,
        COALESCE(SUM(m.points), 0) as total_points,
        COUNT(m.id) as total_matches,
        COALESCE(AVG(m.points), 0) as average_score,
        MAX(m.match_date) as last_played
    FROM users u
    LEFT JOIN matches m ON u.id = m.user_id
    GROUP BY u.id, u.username, u.email
    ORDER BY total_points DESC, average_score DESC";

if (mysqli_query($conn, $sql)) {
    echo "View 'view_ranking_geral' criada com sucesso<br>";
} else {
    echo "Erro criando view: " . mysqli_error($conn) . "<br>";
}

// ============================================
// VIEW: Ranking por liga
// ============================================
$sql = "CREATE OR REPLACE VIEW view_ranking_liga AS
    SELECT 
        l.id as league_id,
        l.league_name,
        u.id as user_id,
        u.username,
        ls.total_points,
        ls.matches_played,
        ls.average_score,
        ls.last_played,
        RANK() OVER (PARTITION BY l.id ORDER BY ls.total_points DESC) as position_in_league
    FROM leagues l
    JOIN users_leagues ul ON l.id = ul.league_id
    JOIN users u ON ul.user_id = u.id
    LEFT JOIN league_scores ls ON u.id = ls.user_id AND l.id = ls.league_id
    ORDER BY l.id, ls.total_points DESC";

if (mysqli_query($conn, $sql)) {
    echo "View 'view_ranking_liga' criada com sucesso<br>";
} else {
    echo "Erro criando view: " . mysqli_error($conn) . "<br>";
}

echo "<hr>";
echo "<h3>DADOS DE EXEMPLO:</h3>";

$insertSampleData = false; // Mude para true se quiser dados de exemplo

if ($insertSampleData) {
    echo "<p>Inserindo dados de exemplo...</p>";

    // Inserir usuário admin
    $password = md5('admin123');
    $sql = "INSERT IGNORE INTO users (username, email, user_password) 
                VALUES ('Admin', 'admin@example.com', '$password')";
    mysqli_query($conn, $sql);

    // Inserir alguns usuários de exemplo
    $users = [
        ['Jogador1', 'jogador1@example.com', md5('senha123')],
        ['Jogador2', 'jogador2@example.com', md5('senha123')],
        ['Jogador3', 'jogador3@example.com', md5('senha123')]
    ];

    foreach ($users as $user) {
        $sql = "INSERT IGNORE INTO users (username, email, user_password) 
                    VALUES ('{$user[0]}', '{$user[1]}', '{$user[2]}')";
        mysqli_query($conn, $sql);
    }

    echo "Dados de exemplo inseridos<br>";
}

mysqli_close($conn);

echo "<hr>";
echo "<h3 style='color: green;'>BANCO DE DADOS CONFIGURADO COM SUCESSO!</h3>";