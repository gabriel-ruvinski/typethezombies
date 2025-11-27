<?php
session_start();
header("Content-Type: application/json");

require 'credentials.php';

//Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Você precisa estar logado para criar uma liga."
    ]);
    exit;
}

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

$league_name = trim($_POST['league_name'] ?? '');
$league_key = trim($_POST['league_key'] ?? '');
$creator_id = $_SESSION['user_id'];

//Validação dos campos
if (empty($league_name) || empty($league_key)) {
    echo json_encode([
        "success" => false,
        "message" => "O nome e a palavra-chave são obrigatórios."
    ]);
    exit;
}

//Verifica se a chave já existe
$stmt = mysqli_prepare($conn, "SELECT id FROM leagues WHERE league_key = ?");
mysqli_stmt_bind_param($stmt, "s", $league_key);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Essa palavra-chave já está em uso."
    ]);
    exit;
}

mysqli_stmt_close($stmt);

//Inserir liga
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO leagues (league_name, league_key, creator_id) VALUES (?, ?, ?)"
);
mysqli_stmt_bind_param($stmt, "ssi", $league_name, $league_key, $creator_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "success" => true,
        "message" => "Liga criada com sucesso!",
        "league" => [
            "id" => mysqli_insert_id($conn),
            "league_name" => $league_name,
            "league_key" => $league_key,
            "creator_id" => $creator_id
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao criar liga."
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
