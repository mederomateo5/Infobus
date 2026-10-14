<?php
require_once '../../config/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'redirect' => BASE_URL . 'login.php',
        'mensaje' => 'Debe iniciar sesión para marcar favoritos.'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];

require_once '../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$viaje_id = isset($data['viaje_id']) ? intval($data['viaje_id']) : 0;

if ($viaje_id <= 0) {
    echo json_encode(['success' => false, 'mensaje' => 'ID de viaje inválido']);
    exit;
}

try {
    // Verificar si ya existe como favorito
    $stmt = $pdo->prepare("SELECT * FROM usuario_viaje_favorito WHERE user_id = ? AND viaje_id = ?");
    $stmt->execute([$user_id, $viaje_id]);
    $existe = $stmt->fetch();

    if ($existe) {
        // Si ya existe, lo eliminamos (quitar favorito)
        $del = $pdo->prepare("DELETE FROM usuario_viaje_favorito WHERE user_id = ? AND viaje_id = ?");
        $del->execute([$user_id, $viaje_id]);
        echo json_encode(['success' => true, 'favorito' => false, 'mensaje' => 'Eliminado de favoritos']);
    } else {
        // Si no existe, lo agregamos
        $ins = $pdo->prepare("INSERT INTO usuario_viaje_favorito (user_id, viaje_id) VALUES (?, ?)");
        $ins->execute([$user_id, $viaje_id]);
        echo json_encode(['success' => true, 'favorito' => true, 'mensaje' => 'Agregado a favoritos']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
}
?>