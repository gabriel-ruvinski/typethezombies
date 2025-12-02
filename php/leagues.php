<?php
// leagues.php - VERSÃO CORRIGIDA
error_reporting(E_ERROR | E_PARSE); // Só mostra erros críticos, não warnings

// Iniciar sessão uma única vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpar qualquer saída anterior
if (ob_get_length()) ob_end_clean();
ob_start();

require "db_functions.php";

// Verificar login diretamente (sem require authenticate.php)
$login = false;
$user_id = 0;
if (isset($_SESSION["user_id"]) && isset($_SESSION["user_name"]) && isset($_SESSION["user_email"])) {
    $login = true;
    $user_id = $_SESSION["user_id"];
    $user_name = $_SESSION["user_name"];
    $user_email = $_SESSION["user_email"];
}

header('Content-Type: application/json');

if (!$login) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

function getRankingSemanal($conn) {
    error_reporting(0);
    
    // Data de início da semana
    $segunda = date('Y-m-d', strtotime('monday this week'));
    $domingo = date('Y-m-d', strtotime('sunday this week'));
    
    $sql = "SELECT 
                u.username as nome,
                SUM(m.points) as pontuacao,
                COUNT(m.id) as partidas
            FROM matches m
            JOIN users u ON m.user_id = u.id
            WHERE DATE(m.match_date) BETWEEN '$segunda' AND '$domingo'
            GROUP BY u.id
            ORDER BY pontuacao DESC
            LIMIT 20";
    
    $result = mysqli_query($conn, $sql);
    $ranking = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $ranking[] = [
            'nome' => $row['nome'],
            'pontuacao' => intval($row['pontuacao']),
            'partidas' => intval($row['partidas'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'ranking' => $ranking,
        'periodo' => "Semana: " . date('d/m', strtotime($segunda)) . " a " . date('d/m', strtotime($domingo))
    ]);
}

$conn = connect_db();
$action = $_POST['action'] ?? '';

try {
    ob_end_clean(); // Limpa buffer antes de processar
    
    switch ($action) {
        case 'criar':
            criarLiga($conn, $user_id);
            break;
            
        case 'entrar':
            entrarLiga($conn, $user_id);
            break;
            
        case 'get_minhas_ligas':
            getMinhasLigas($conn, $user_id);
            break;
            
        case 'get_ranking_geral':
            getRankingGeral($conn);
            break;
            
        case 'get_ranking_liga':
            getRankingLiga($conn);
            break;
            
        case 'get_liga_info':
            getLigaInfo($conn);
            break;
            
        case 'sair_liga':
            sairLiga($conn, $user_id);
            break;

        case 'get_ranking_semanal':
            getRankingSemanal($conn);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}

disconnect_db($conn);
exit(); // Garante que nada mais será enviado

// ============================================
// FUNÇÕES DO SISTEMA DE LIGAS
// ============================================

/**
 * Criar uma nova liga
 */
function criarLiga($conn, $user_id) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome'] ?? '');
    $palavra_chave = mysqli_real_escape_string($conn, $_POST['palavra_chave'] ?? '');
    
    // Validações
    if (empty($nome) || empty($palavra_chave)) {
        echo json_encode(['success' => false, 'message' => 'Preencha todos os campos']);
        return;
    }
    
    if (strlen($nome) < 3 || strlen($nome) > 50) {
        echo json_encode(['success' => false, 'message' => 'Nome deve ter entre 3 e 50 caracteres']);
        return;
    }
    
    // Verificar se já existe liga com este nome
    $sql = "SELECT id FROM leagues WHERE league_name = '$nome'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Já existe uma liga com este nome']);
        return;
    }
    
    // Criar a liga
    $sql = "INSERT INTO leagues (league_name, league_key, creator_id) 
            VALUES ('$nome', '$palavra_chave', $user_id)";
    
    if (mysqli_query($conn, $sql)) {
        $liga_id = mysqli_insert_id($conn);
        
        // Adicionar criador como membro da liga
        $sql = "INSERT INTO users_leagues (user_id, league_id) 
                VALUES ($user_id, $liga_id)";
        mysqli_query($conn, $sql);

        $_SESSION['liga_ativa'] = $liga_id;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Liga criada com sucesso!',
            'liga_id' => $liga_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar liga']);
    }
}

/**
 * Entrar em uma liga existente
 */
function entrarLiga($conn, $user_id) {
    $liga_id = intval($_POST['liga_id'] ?? 0);
    $palavra_chave = mysqli_real_escape_string($conn, $_POST['palavra_chave'] ?? '');
    
    if ($liga_id <= 0 || empty($palavra_chave)) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
        return;
    }
    
    // Verificar se a liga existe e a palavra-chave está correta
    $sql = "SELECT id, league_name FROM leagues 
            WHERE id = $liga_id AND league_key = '$palavra_chave'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Liga não encontrada ou palavra-chave incorreta']);
        return;
    }
    
    // Verificar se o usuário já está na liga
    $sql = "SELECT id FROM users_leagues 
            WHERE user_id = $user_id AND league_id = $liga_id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => 'Você já está nesta liga']);
        return;
    }
    
    // Adicionar usuário à liga
    $sql = "INSERT INTO users_leagues (user_id, league_id) 
            VALUES ($user_id, $liga_id)";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['liga_ativa'] = $liga_id;
        echo json_encode([
            'success' => true, 
            'message' => 'Entrou na liga com sucesso!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao entrar na liga']);
    }
}

/**
 * Obter todas as ligas do usuário
 */
function getMinhasLigas($conn, $user_id) {
    $sql = "SELECT 
                l.id, 
                l.league_name as nome,
                u.username as criador,
                COUNT(ul.user_id) as membros
            FROM users_leagues ul
            JOIN leagues l ON ul.league_id = l.id
            JOIN users u ON l.creator_id = u.id
            WHERE ul.user_id = $user_id
            GROUP BY l.id
            ORDER BY l.league_name";
    
    $result = mysqli_query($conn, $sql);
    $ligas = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $ligas[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'criador' => $row['criador'],
            'membros' => $row['membros']
        ];
    }
    
    echo json_encode(['success' => true, 'ligas' => $ligas]);
}

/**
 * Obter ranking geral de todos os jogadores
 */
function getRankingGeral($conn) {
    $sql = "SELECT 
                u.id,
                u.username as nome,
                COALESCE(SUM(m.points), 0) as pontuacao,
                COUNT(m.id) as partidas
            FROM users u
            LEFT JOIN matches m ON u.id = m.user_id
            GROUP BY u.id
            ORDER BY pontuacao DESC
            LIMIT 50";
    
    $result = mysqli_query($conn, $sql);
    $ranking = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $ranking[] = [
            'nome' => $row['nome'],
            'pontuacao' => intval($row['pontuacao']),
            'partidas' => intval($row['partidas'])
        ];
    }
    
    echo json_encode(['success' => true, 'ranking' => $ranking]);
}

/**
 * Obter ranking de uma liga específica
 */
function getRankingLiga($conn) {
    $liga_id = intval($_POST['liga_id'] ?? 0);
    
    if ($liga_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID da liga inválido']);
        return;
    }
    
    $sql = "SELECT 
                u.id,
                u.username as nome,
                COALESCE(SUM(m.points), 0) as pontuacao,
                COUNT(m.id) as partidas
            FROM users u
            JOIN users_leagues ul ON u.id = ul.user_id
            LEFT JOIN matches m ON u.id = m.user_id
            WHERE ul.league_id = $liga_id
            GROUP BY u.id
            ORDER BY pontuacao DESC";
    
    $result = mysqli_query($conn, $sql);
    $ranking = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $ranking[] = [
            'nome' => $row['nome'],
            'pontuacao' => intval($row['pontuacao']),
            'partidas' => intval($row['partidas'])
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'ranking' => $ranking,
        'liga_id' => $liga_id
    ]);
}

/**
 * Obter informações de uma liga específica
 */
function getLigaInfo($conn) {
    $liga_id = intval($_POST['liga_id'] ?? 0);
    
    $sql = "SELECT 
                l.*,
                u.username as criador_nome,
                COUNT(ul.user_id) as total_membros
            FROM leagues l
            JOIN users u ON l.creator_id = u.id
            LEFT JOIN users_leagues ul ON l.id = ul.league_id
            WHERE l.id = $liga_id
            GROUP BY l.id";
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Liga não encontrada']);
        return;
    }
    
    $liga = mysqli_fetch_assoc($result);
    
    // Não retornar a palavra-chave por segurança
    unset($liga['league_key']);
    
    echo json_encode(['success' => true, 'liga' => $liga]);
}

/**
 * Sair de uma liga
 */
function sairLiga($conn, $user_id) {
    $liga_id = intval($_POST['liga_id'] ?? 0);
    
    // Verificar se o usuário é o criador da liga
    $sql = "SELECT creator_id FROM leagues WHERE id = $liga_id";
    $result = mysqli_query($conn, $sql);
    $liga = mysqli_fetch_assoc($result);
    
    if ($liga['creator_id'] == $user_id) {
        echo json_encode(['success' => false, 'message' => 'O criador não pode sair da liga']);
        return;
    }
    
    // Remover usuário da liga
    $sql = "DELETE FROM users_leagues 
            WHERE user_id = $user_id AND league_id = $liga_id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Saiu da liga com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao sair da liga']);
    }
}
?>