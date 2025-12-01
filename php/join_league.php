<?php
session_start();
require "db_functions.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
    exit;
}

$user_id = $_SESSION['user_id'];

if (empty($_POST['league_id']) || empty($_POST['league_key'])) {
    echo json_encode(["status" => "empty", "message" => "Você precisa preencher esse campo."]);
    exit;
}

$league_id = intval($_POST['league_id']);
$league_key = trim($_POST['league_key']);

$conn = connect_db();

// Verifica se liga existe e a chave confere
$sql = "SELECT * FROM leagues WHERE id = ? AND league_key = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $league_id, $league_key);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode(["status" => "notfound", "message" => "Erro. Liga não existe."]);
    disconnect_db($conn);
    exit;
}

// Verifica se o usuário já está na liga
$sql_check = "SELECT * FROM users_leagues WHERE user_id = ? AND league_id = ?";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $league_id);
mysqli_stmt_execute($stmt);
$result_check = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result_check) === 0) {
    $sql_insert = "INSERT INTO users_leagues (user_id, league_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $league_id);
    mysqli_stmt_execute($stmt);
}

echo json_encode(["status" => "success", "message" => "Você entrou na liga!"]);

disconnect_db($conn);
?>

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
