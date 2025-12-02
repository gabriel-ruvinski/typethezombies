<?php
// save_match.php - ARQUIVO ÚNICO COMPLETO
error_reporting(0); // Desativa erros para não quebrar o JSON
ob_start(); // Controla saída

session_start();

// 1. CONFIGURAÇÃO DO BANCO (copie do credentials.php)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "typezombies";

// 2. CONEXÃO COM BANCO
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com banco']);
    exit();
}

// 3. VERIFICAR SE ESTÁ LOGADO
if (!isset($_SESSION["user_id"])) {
    mysqli_close($conn);
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado']);
    exit();
}

$user_id = $_SESSION["user_id"];
$pontuacao = intval($_POST['pontuacao'] ?? 0);

// 4. VERIFICAR LIGA ATIVA (da sessão)
$liga_id = isset($_SESSION['liga_ativa']) ? intval($_SESSION['liga_ativa']) : null;

// 5. SALVAR PARTIDA NA TABELA matches
$sql_matches = "INSERT INTO matches (user_id, points, league_id) 
                VALUES ($user_id, $pontuacao, " . ($liga_id ?: 'NULL') . ")";

if (!mysqli_query($conn, $sql_matches)) {
    mysqli_close($conn);
    ob_end_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao salvar partida: ' . mysqli_error($conn)
    ]);
    exit();
}

// 6. SE TEM LIGA ATIVA, ATUALIZAR LEAGUE_SCORES
if ($liga_id) {
    // Verificar se usuário pertence à liga
    $check_liga = "SELECT id FROM users_leagues 
                   WHERE user_id = $user_id AND league_id = $liga_id";
    
    $result = mysqli_query($conn, $check_liga);
    
    if (mysqli_num_rows($result) > 0) {
        // Atualizar ou inserir pontuação na liga
        $sql_liga = "INSERT INTO league_scores 
                     (user_id, league_id, total_points, matches_played) 
                     VALUES ($user_id, $liga_id, $pontuacao, 1)
                     ON DUPLICATE KEY UPDATE 
                     total_points = total_points + $pontuacao,
                     matches_played = matches_played + 1,
                     last_updated = NOW()";
        
        mysqli_query($conn, $sql_liga);
    }
}

// 7. ATUALIZAR PONTUAÇÃO TOTAL DO USUÁRIO
$sql_update_user = "UPDATE users 
                    SET total_points = total_points + $pontuacao,
                        total_matches = total_matches + 1
                    WHERE id = $user_id";
mysqli_query($conn, $sql_update_user);

// 8. RESPOSTA DE SUCESSO
mysqli_close($conn);
ob_end_clean();
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Partida salva com sucesso!'
]);

exit();
?>