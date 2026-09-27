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

// Transformar la fecha ingresada (YYYY-MM-DD) al ID del día de la semana (1 = Lunes, ..., 7 = Domingo)
$timestamp = strtotime($fecha);
if ($timestamp === false) {
    echo json_encode(['error' => 'Fecha inválida.']);
    exit();
}

// date('N') devuelve un número del 1 (Lunes) al 7 (Domingo) que coincide con tu tabla 'dias_semana'
$dia_id = (int)date('N', $timestamp);

try {
    // Consulta SQL adaptada para buscar por día de la semana asociado en 'viaje_dia'
    $query = "SELECT
                po.nombre AS origen,
                pd.nombre AS destino,
                v.asientos_disponibles AS asientosLibres,
                c.nombre AS agencia,
                v.precio,
                o.ruta_key,
                v.hora,
                v.hora_llegada,
                v.es_directo
            FROM viajes v
            JOIN paradas po ON v.origen_id = po.id
            JOIN paradas pd ON v.destino_id = pd.id
            JOIN omnibus o ON v.omnibus_id = o.id
            JOIN compania c ON o.compania_id = c.id
            JOIN viaje_dia vd ON v.id = vd.viaje_id
            WHERE po.nombre = :origen
                AND pd.nombre = :destino
                AND vd.dia_id = :dia_id
                AND v.asientos_disponibles >= :pasajeros
                AND v.ya_salio = 0
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':origen'    => $origen,
        ':destino'   => $destino,
        ':dia_id'    => $dia_id,
        ':pasajeros' => $pasajeros
    ]);

    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar y tipar datos antes de enviarlos a Javascript (incluyendo la fecha que consultó el usuario para mostrarla en la tabla)
    foreach ($viajes as &$v) {
        $v['asientosLibres'] = (int)$v['asientosLibres'];
        $v['precio'] = (float)$v['precio'];
        $v['fecha'] = $fecha; // Devolvemos la fecha seleccionada para que se renderice en la tabla
        $v['ruta_key'] = empty($v['ruta_key']) ? 'urbano' : $v['ruta_key'];
    }

    echo json_encode($viajes);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>