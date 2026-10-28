<?php
session_start();
require_once '../../private/config/config.php';
// Configurar la respuesta como JSON
header('Content-Type: application/json; charset=utf-8');
require_once '../../private/config/database.php';

// Obtener ID del usuario logueado (para verificar favoritos)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

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

$dia_id = (int)date('N', $timestamp);

try {
    // Consulta SQL modificada para incluir v.id y un LEFT JOIN con favoritos
    $query = "SELECT
                v.id AS id,
                po.nombre AS origen,
                pd.nombre AS destino,
                v.asientos_disponibles AS asientosLibres,
                c.nombre AS agencia,
                v.precio,
                o.ruta_key,
                v.hora,
                v.hora_llegada,
                v.es_directo,
                CASE WHEN uf.viaje_id IS NOT NULL THEN 1 ELSE 0 END AS es_favorito
            FROM viajes v
            JOIN paradas po ON v.origen_id = po.id
            JOIN paradas pd ON v.destino_id = pd.id
            JOIN omnibus o ON v.omnibus_id = o.id
            JOIN compania c ON o.compania_id = c.id
            JOIN viaje_dia vd ON v.id = vd.viaje_id
            LEFT JOIN usuario_viaje_favorito uf ON v.id = uf.viaje_id AND uf.user_id = :user_id
            WHERE po.nombre = :origen
                AND pd.nombre = :destino
                AND vd.dia_id = :dia_id
                AND v.asientos_disponibles >= :pasajeros
                AND v.ya_salio = 0
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id'   => $user_id,
        ':origen'    => $origen,
        ':destino'   => $destino,
        ':dia_id'    => $dia_id,
        ':pasajeros' => $pasajeros
    ]);

    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar y tipar datos antes de enviarlos a Javascript
    foreach ($viajes as &$v) {
        $v['id'] = (int)$v['id'];
        $v['asientosLibres'] = (int)$v['asientosLibres'];
        $v['precio'] = (float)$v['precio'];
        $v['es_favorito'] = (bool)$v['es_favorito'];
        $v['fecha'] = $fecha; 
        $v['ruta_key'] = empty($v['ruta_key']) ? 'urbano' : $v['ruta_key'];
    }

    echo json_encode($viajes);

} catch (PDOException $e) {
    // Registrar internamente el error real de la base de datos
    error_log("Error en buscar_viajes.php: " . $e->getMessage());
    
    // Devolver mensaje genérico y código de error HTTP adecuado
    http_response_code(500);
    echo json_encode(['error' => 'Ocurrió un error al procesar la búsqueda.']);
}
?>