<?php
session_start();
require "db_functions.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = connect_db();

// Seleciona todas as ligas do usuário
$sql = "SELECT l.id, l.league_name, l.creator_id
        FROM leagues l
        JOIN users_leagues ul ON l.id = ul.league_id
        WHERE ul.user_id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$leagues = [];

while ($row = mysqli_fetch_assoc($result)) {
    $leagues[] = $row;
}

disconnect_db($conn);

echo json_encode($leagues);
?>
