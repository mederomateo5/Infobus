<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    // Incluir la base de datos
    require_once __DIR__ . '/../config/database.php';

    if (!isset($pdo)) {
        throw new Exception("La variable \$pdo no está definida en database.php.");
    }

    // Consulta adaptada con los nombres exactos de tu phpMyAdmin: 'latitud' y 'longitud'
    $stmt = $pdo->query("SELECT id, nombre, latitud AS lat, longitud AS lng, tipo FROM paradas");
    $paradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$paradas) {
        $paradas = [];
    }

    echo json_encode($paradas);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}
?>