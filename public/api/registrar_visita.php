<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

require_once '../../private/config/database.php';

// Aceptar solamente solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

// Leer y validar el ID del viaje.
$viajeId = filter_input(
    INPUT_POST,
    'viaje_id',
    FILTER_VALIDATE_INT
);

if (!$viajeId || $viajeId < 1) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

// Obtener el usuario de la sesión, si inició sesión.
$userId = $_SESSION['user_id'] ?? null;

try {
    // Comprobar que el viaje exista.
    $consulta = $pdo->prepare(
        'SELECT id FROM viajes WHERE id = :id'
    );

    $consulta->execute(['id' => $viajeId]);

    if (!$consulta->fetch()) {
        http_response_code(404);
        echo json_encode(['ok' => false]);
        exit;
    }

    // Registrar el clic.
    $sql = 'INSERT INTO viaje_visita (viaje_id, user_id)
            VALUES (:viaje_id, :user_id)';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'viaje_id' => $viajeId,
        'user_id' => $userId
    ]);

    echo json_encode(['ok' => true]);

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode(['ok' => false]);
}
