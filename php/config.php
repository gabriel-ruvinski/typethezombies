<?php
$host = "localhost";
$db = "typezombies";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro ao conectar ao banco: " . $conn->connect_error);
}
?>