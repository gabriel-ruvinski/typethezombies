<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado']);
    exit();
}

$liga_id = intval($_POST['liga_id'] ?? 0);

if ($liga_id > 0) {
    $_SESSION['liga_ativa'] = $liga_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Liga definida como ativa! Suas próximas partidas contarão aqui.'
    ]);
} else {
    // Se liga_id for 0, remove a liga ativa
    unset($_SESSION['liga_ativa']);
    echo json_encode([
        'success' => true, 
        'message' => 'Modo livre ativado. Partidas não contarão em nenhuma liga.'
    ]);
}
?>