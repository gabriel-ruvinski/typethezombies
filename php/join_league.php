<?php
session_start();
header("Content-Type: application/json");

require 'credentials.php';

//Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Você precisa estar logado para entrar em uma liga."
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

//Apenas POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Método inválido."
    ]);
    exit;
}

$league_key = trim($_POST['league_key'] ?? '');

//Verificação do campo palavra-chave
if (empty($league_key)) {
    echo json_encode([
        "success" => false,
        "message" => "A palavra-chave é obrigatória."
    ]);
    exit;
}

//1.Busca liga pela palavra-chave
$stmt = mysqli_prepare($conn, "SELECT id, league_name FROM leagues WHERE league_key = ?");
mysqli_stmt_bind_param($stmt, "s", $league_key);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$league = mysqli_fetch_assoc($result);

if (!$league) {
    echo json_encode([
        "success" => false,
        "message" => "Liga não encontrada."
    ]);
    exit;
}

$league_id = $league['id'];
mysqli_stmt_close($stmt);

//2.Verifica se o usuário já participa da liga
$stmt = mysqli_prepare($conn, "SELECT id FROM users_leagues WHERE user_id = ? AND league_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $user_id, $league_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Você já está nessa liga."
    ]);
    exit;
}

mysqli_stmt_close($stmt);

//3.Insere o usuário na liga
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users_leagues (user_id, league_id) VALUES (?, ?)"
);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $league_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "success" => true,
        "message" => "Você entrou na liga com sucesso!",
        "league" => [
            "id" => $league_id,
            "name" => $league['league_name'],
            "key" => $league_key
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao entrar na liga."
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
