<?php
require_once '../config/config.php';

require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {

    $sql = "SELECT
                id,
                nombre,
                latitud AS lat,
                longitud AS lng
            FROM paradas
            ORDER BY id ASC";

    $stmt = $pdo->query($sql);

    $paradas = $stmt->fetchAll();

    echo json_encode(
        $paradas,
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'error' => 'No se pudieron obtener las paradas'
    ]);

}
?>