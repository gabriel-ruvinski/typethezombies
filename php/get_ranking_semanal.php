<?php
// get_ranking_semanal.php - ARQUIVO SIMPLES
error_reporting(0);
ob_start();

session_start();
require "db_functions.php";

$conn = connect_db();

// Data de início da semana (segunda-feira)
$segunda = date('Y-m-d', strtotime('monday this week'));
$domingo = date('Y-m-d', strtotime('sunday this week'));

// Consulta SIMPLES para ranking semanal
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

disconnect_db($conn);
ob_end_clean();

echo json_encode([
    'success' => true,
    'ranking' => $ranking,
    'periodo' => [
        'texto' => "Semana: " . date('d/m', strtotime($segunda)) . " a " . date('d/m', strtotime($domingo)),
        'inicio' => date('d/m', strtotime($segunda)),
        'fim' => date('d/m', strtotime($domingo))
    ]
]);
?>