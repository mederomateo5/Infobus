<?php
require_once '../../private/config/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'redirect' => BASE_URL . 'auth/login.php',
        'mensaje' => 'Debe iniciar sesión para marcar favoritos.'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
require_once '../../private/config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$compania_id = isset($data['compania_id']) ? intval($data['compania_id']) : 0;

if ($compania_id <= 0) {
    echo json_encode(['success' => false, 'mensaje' => 'ID de compañía inválido']);
    exit;
}

try {
    // Verificar si ya existe como favorita
    $stmt = $pdo->prepare("SELECT * FROM usuario_compania_favorita WHERE user_id = ? AND compania_id = ?");
    $stmt->execute([$user_id, $compania_id]);
    $existe = $stmt->fetch();

    if ($existe) {
        // Si ya existe, la eliminamos
        $del = $pdo->prepare("DELETE FROM usuario_compania_favorita WHERE user_id = ? AND compania_id = ?");
        $del->execute([$user_id, $compania_id]);
        echo json_encode(['success' => true, 'favorito' => false, 'mensaje' => 'Compañía eliminada de favoritos']);
    } else {
        // Si no existe, la agregamos
        $ins = $pdo->prepare("INSERT INTO usuario_compania_favorita (user_id, compania_id) VALUES (?, ?)");
        $ins->execute([$user_id, $compania_id]);
        echo json_encode(['success' => true, 'favorito' => true, 'mensaje' => 'Compañía agregada a favoritos']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
}
?>