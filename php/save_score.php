<?php
session_start();
require "db_functions.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Verifica se dados foram enviados
if (!isset($_POST['points']) || !isset($_POST['time'])) {
    echo json_encode(["status" => "error", "message" => "Dados incompletos"]);
    exit;
}

$points = intval($_POST['points']);
$time = intval($_POST['time']);

$conn = connect_db();

$sql = "INSERT INTO matches (user_id, points, match_time) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $points, $time);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success", "message" => "Pontuação salva com sucesso"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao salvar pontuação"]);
}

disconnect_db($conn);
?>
