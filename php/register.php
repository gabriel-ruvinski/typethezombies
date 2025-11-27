<?php
session_start();
require('credentials.php');

//Conexão com o banco
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Erro na conexão com o banco']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['senha'] ?? '';

    //Validação
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit;
    }

    //Verifica se o email já existe
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email já cadastrado']);
        exit;
    }
    mysqli_stmt_close($stmt);

    //Hash da senha
    $hash = password_hash($password, PASSWORD_DEFAULT);

    //Inserir usuário
    $stmt = mysqli_prepare($conn,
        "INSERT INTO users (username, email, user_password) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Usuário registrado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar usuário']);
    }

    mysqli_stmt_close($stmt);

} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}

mysqli_close($conn);
?>
