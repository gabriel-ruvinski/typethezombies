<?php
session_start();

//Caso exista sessão, destrói
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();

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
