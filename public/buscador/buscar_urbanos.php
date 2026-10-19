<?php
require_once '../../private/config/config.php';
// Configurar la respuesta como JSON
header('Content-Type: application/json; charset=utf-8');
require_once '../../private/config/database.php';

try {
    // Consulta SQL adaptada para traer los días de la semana desde 'viaje_dia' en lugar de la columna 'fecha'
    $query = "SELECT 
                po.nombre AS origen,
                pd.nombre AS destino,
                v.hora,
                v.hora_llegada,
                v.asientos_disponibles AS asientosLibres,
                c.nombre AS agencia,
                v.precio,
                o.ruta_key,
                GROUP_CONCAT(ds.nombre ORDER BY ds.id SEPARATOR ', ') AS frecuencia
            FROM viajes v
            JOIN paradas po ON v.origen_id = po.id
            JOIN paradas pd ON v.destino_id = pd.id
            JOIN omnibus o ON v.omnibus_id = o.id
            JOIN compania c ON o.compania_id = c.id
            JOIN viaje_dia vd ON v.id = vd.viaje_id
            JOIN dias_semana ds ON vd.dia_id = ds.id
            WHERE c.nombre = 'URBANO' AND v.ya_salio = 0
            GROUP BY v.id
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitizar y tipar datos para JavaScript
    foreach ($viajes as &$v) {
        $v['asientosLibres'] = (int)$v['asientosLibres'];
        $v['precio'] = (float)$v['precio'];
        $v['ruta_key'] = empty($v['ruta_key']) ? 'urbano' : $v['ruta_key'];
        // Si por alguna razón no viniera frecuencia, asignamos un respaldo
        $v['frecuencia'] = empty($v['frecuencia']) ? 'Diario' : $v['frecuencia'];
    }

    echo json_encode($viajes);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>