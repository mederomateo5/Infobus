<?php
require_once '../../config/config.php';
// Configurar la respuesta como JSON
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/database.php';

try {
    // Consulta SQL filtrando por el nombre de la compañía "URBANO"
    $query = "SELECT 
                po.nombre AS origen,
                pd.nombre AS destino,
                v.fecha,
                v.hora,
                v.hora_llegada,
                v.asientos_disponibles AS asientosLibres,
                c.nombre AS agencia,
                v.precio,
                o.ruta_key
            FROM viajes v
            JOIN paradas po ON v.origen_id = po.id
            JOIN paradas pd ON v.destino_id = pd.id
            JOIN omnibus o ON v.omnibus_id = o.id
            JOIN compania c ON o.compania_id = c.id
            WHERE c.nombre = 'URBANO' AND v.ya_salio = 0
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar y tipar datos para JavaScript
    foreach ($viajes as &$v) {
        $v['asientosLibres'] = (int)$v['asientosLibres'];
        $v['precio'] = (float)$v['precio'];
        $v['ruta_key'] = empty($v['ruta_key']) ? 'urbano' : $v['ruta_key'];
    }

    echo json_encode($viajes);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>