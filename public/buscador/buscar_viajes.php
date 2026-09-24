<?php
require_once '../../config/config.php';
// Configurar la respuesta como JSON
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/database.php';

// Obtener parámetros de la URL
$origen    = $_GET['origen'] ?? '';
$destino   = $_GET['destino'] ?? '';
$fecha     = $_GET['fecha'] ?? '';
$pasajeros = filter_input(INPUT_GET, 'pasajeros', FILTER_VALIDATE_INT);

// Validar parámetros requeridos
if (empty($origen) || empty($destino) || empty($fecha) || !$pasajeros) {
    echo json_encode([]);
    exit();
}

try {
    // Consulta SQL corregida según schema.sql
    $query = "SELECT
                po.nombre AS origen,
                pd.nombre AS destino,
                v.fecha,
                v.asientos_disponibles AS asientosLibres,
                c.nombre AS agencia,
                v.precio,
                o.ruta_key
            FROM viajes v
            JOIN paradas po ON v.origen_id = po.id
            JOIN paradas pd ON v.destino_id = pd.id
            JOIN omnibus o ON v.omnibus_id = o.id
            JOIN compania c ON o.compania_id = c.id
            WHERE po.nombre = :origen
                AND pd.nombre = :destino
                AND v.fecha = :fecha
                AND v.asientos_disponibles >= :pasajeros
                AND v.ya_salio = 0
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':origen'    => $origen,
        ':destino'   => $destino,
        ':fecha'     => $fecha,
        ':pasajeros' => $pasajeros
    ]);

    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar y tipar datos antes de enviarlos a Javascript
    foreach ($viajes as &$v) {
        $v['asientosLibres'] = (int)$v['asientosLibres'];
        $v['precio'] = (float)$v['precio'];
        // Respaldo por si el registro no tiene una ruta asignada en la BD
        $v['ruta_key'] = empty($v['ruta_key']) ? 'urbano' : $v['ruta_key'];
    }

    echo json_encode($viajes);

} catch (PDOException $e) {
    // Devuelve el mensaje real si falla para facilitar la depuración
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>