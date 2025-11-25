<?php
session_start();
require 'credentials.php';
require 'sanitize.php';

//Conexão com o banco
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode([
        "status" => "error",
        "message" => "Erro ao conectar ao banco."
    ]));
}

$email = sanitize($_POST['email'] ?? '');
$senha = sanitize($_POST['senha'] ?? '');

//Verifica os campos
if (empty($email) || empty($senha)) {
    echo json_encode([
        "status" => "error",
        "message" => "Preencha todos os campos!"
    ]);
    exit;
}

//Busca usuário no banco
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Usuário não encontrado."
    ]);
    exit;
}

$user = mysqli_fetch_assoc($result);

//Verifica senha
if (!password_verify($senha, $user['senha'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Senha incorreta."
    ]);
    exit;
}

//Cria sessão
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['nome'];
$_SESSION['user_email'] = $user['email'];

echo json_encode([
    "status" => "success",
    "message": "Login realizado com sucesso!",
    "user" => [
        "id" => $user['id'],
        "nome" => $user['nome'],
        "email" => $user['email']
    ]
]);
?>
