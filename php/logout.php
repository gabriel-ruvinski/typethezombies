<?php
session_start();
header('Content-Type: application/json');

//Caso exista sessão, destrói
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();

    //Limpa cookie da sessão
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    echo json_encode([
        "status" => "success",
        "message" => "Logout realizado com sucesso."
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Nenhum usuário logado."
]);
?>
