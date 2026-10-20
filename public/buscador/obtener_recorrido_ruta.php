<?php
// Configurar cabecera para devolver la respuesta siempre en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Desactivar impresión de errores en HTML para no romper el parseo JSON en JS
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Incluir la conexión a la base de datos (database.php está en private/config/)
    require_once '../../private/config/database.php';

    // Verificar que la variable $pdo existe
    if (!isset($pdo)) {
        throw new Exception("No se pudo conectar a la base de datos (\$pdo no definida)");
    }

    // Obtener el parámetro enviado por el JavaScript (?ruta=urbano o ?omnibus_id=4)
    $parametro = $_GET['ruta'] ?? $_GET['omnibus_id'] ?? null;

    if (!$parametro) {
        echo json_encode(['error' => 'Falta el parámetro de ruta u ómnibus']);
        exit;
    }

    // Si es un número busca por omnibus_id, si es texto busca por ruta_key (ej. 'urbano')
    if (is_numeric($parametro)) {
        $stmt = $pdo->prepare("
            SELECT p.id, p.latitud, p.longitud, p.nombre, p.tipo, r.orden
            FROM recorre r
            JOIN paradas p ON r.parada_id = p.id
            WHERE r.omnibus_id = ?
            ORDER BY r.orden ASC
        ");
        $stmt->execute([$parametro]);
    } else {
        $stmt = $pdo->prepare("
            SELECT p.id, p.latitud, p.longitud, p.nombre, p.tipo, r.orden
            FROM omnibus o
            JOIN recorre r ON o.id = r.omnibus_id
            JOIN paradas p ON r.parada_id = p.id
            WHERE o.ruta_key = ?
            ORDER BY r.orden ASC
        ");
        $stmt->execute([$parametro]);
    }

    $paradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar las paradas encontradas en formato JSON
    echo json_encode($paradas);

} catch (Exception $e) {
    // En caso de error, devolver la descripción clara en formato JSON
    echo json_encode(['error' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>