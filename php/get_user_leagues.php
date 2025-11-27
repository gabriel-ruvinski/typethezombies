<?php
session_start();
header("Content-Type: application/json");

require 'credentials.php';

//Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Você precisa estar logado para ver suas ligas."
    ]);
    exit;
}   

$user_id = $_SESSION['user_id'];

//Conexão com o banco
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao conectar ao banco."
    ]);
    exit;
}

//Busca todas as ligas do usuário no banco de dados
$sql = "
    SELECT 
        leagues.id,
        leagues.league_name,
        leagues.league_key,
        leagues.creator_id
    FROM users_leagues
    INNER JOIN leagues ON leagues.id = users_leagues.league_id
    WHERE users_leagues.user_id = ?
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$leagues = [];
while ($row = mysqli_fetch_assoc($result)) {
    $leagues[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

//Resposta
echo json_encode([
    "success" => true,
    "count" => count($leagues),
    "leagues" => $leagues
]);
?>
