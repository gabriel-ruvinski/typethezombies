<?php
session_start();
require_once "db_functions.php";

// Verifica login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = connect_db();

$sql = "SELECT match_date, points, match_time 
        FROM matches 
        WHERE user_id = ?
        ORDER BY match_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$matches = [];

while ($row = mysqli_fetch_assoc($result)) {
    $matches[] = $row;
}

disconnect_db($conn);

echo json_encode($matches);
?>
