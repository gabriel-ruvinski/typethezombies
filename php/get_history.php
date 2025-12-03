<?php
session_start();
header('Content-Type: application/json');

// Configuração do banco (use suas credenciais)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "typezombies";

// Verificar login
if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit();
}

// Conexão
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão']);
    exit();
}

$user_id = $_SESSION["user_id"];

// Buscar apenas DATA, PONTUAÇÃO e DURAÇÃO
$sql = "SELECT 
            DATE_FORMAT(match_date, '%d/%m/%Y %H:%i') as data,
            points as pontuacao,
            match_time as duracao
        FROM matches 
        WHERE user_id = $user_id
        ORDER BY match_date DESC
        LIMIT 20";

$result = mysqli_query($conn, $sql);

$historico = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $historico[] = [
            'data' => $row['data'],
            'pontuacao' => $row['pontuacao'],
            'duracao' => $row['duracao'] ? $row['duracao'] . 's' : '60s' // Default
        ];
    }
}

mysqli_close($conn);

echo json_encode([
    'success' => true,
    'total' => count($historico),
    'historico' => $historico
]);
?>