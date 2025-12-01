<?php
session_start();
require "db_functions.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Verifica se campos foram enviados
if (empty($_POST['league_name']) || empty($_POST['league_key'])) {
    echo json_encode(["status" => "empty", "message" => "Você precisa preencher esse campo."]);
    exit;
}

$league_name = trim($_POST['league_name']);
$league_key = trim($_POST['league_key']);

$conn = connect_db();

// Verifica se a liga já existe
$sql_check = "SELECT id FROM leagues WHERE league_name = ?";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt, "s", $league_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(["status" => "exists", "message" => "Liga já cadastrada"]);
    disconnect_db($conn);
    exit;
}

// Insere a liga
$sql_insert = "INSERT INTO leagues (league_name, league_key, creator_id) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql_insert);
mysqli_stmt_bind_param($stmt, "ssi", $league_name, $league_key, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $league_id = mysqli_insert_id($conn);

    // Adiciona automaticamente o criador na tabela users_leagues
    $sql_user_league = "INSERT INTO users_leagues (user_id, league_id) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($conn, $sql_user_league);
    mysqli_stmt_bind_param($stmt2, "ii", $user_id, $league_id);
    mysqli_stmt_execute($stmt2);

    echo json_encode(["status" => "success", "message" => "Liga criada com sucesso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao criar liga"]);
}

disconnect_db($conn);
?>
