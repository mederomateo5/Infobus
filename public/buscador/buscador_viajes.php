<?php
require_once '../../config/config.php';
header("Content-Type: application/json");

require_once '../../config/database.php';

try {
    $datos = json_decode(file_get_contents("php://input"), true);

    $sqlBase = "SELECT v.*, 
                       p_origen.nombre AS origen_nombre, 
                       p_destino.nombre AS destino_nombre,
                       GROUP_CONCAT(ds.nombre ORDER BY ds.id SEPARATOR ', ') AS dias_semana
                FROM viajes v
                INNER JOIN paradas p_origen ON v.origen_id = p_origen.id
                INNER JOIN paradas p_destino ON v.destino_id = p_destino.id
                LEFT JOIN viaje_dia vd ON v.id = vd.viaje_id
                LEFT JOIN dias_semana ds ON vd.dia_id = ds.id
                GROUP BY v.id";

    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {
        $sql = $sqlBase . " ORDER BY v.hora ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll();

        echo json_encode([
            "exito" => true,
            "resultados" => $resultados
        ]);
        exit;
    }

    $texto = trim($datos["busqueda"] ?? "");

    if (empty($texto)) {
        echo json_encode([
            "exito" => false,
            "mensaje" => "La búsqueda está vacía",
            "resultados" => []
        ]);
        exit;
    }

    $sql = "SELECT v.*, 
                   p_origen.nombre AS origen_nombre, 
                   p_destino.nombre AS destino_nombre,
                   GROUP_CONCAT(ds.nombre ORDER BY ds.id SEPARATOR ', ') AS dias_semana
            FROM viajes v
            INNER JOIN paradas p_origen ON v.origen_id = p_origen.id
            INNER JOIN paradas p_destino ON v.destino_id = p_destino.id
            LEFT JOIN viaje_dia vd ON v.id = vd.viaje_id
            LEFT JOIN dias_semana ds ON vd.dia_id = ds.id
            WHERE p_origen.nombre LIKE :busqueda OR p_destino.nombre LIKE :busqueda
            GROUP BY v.id
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($sql);
    $parametro = "%" . $texto . "%";
    $stmt->execute(['busqueda' => $parametro]);
    $resultados = $stmt->fetchAll();

    echo json_encode([
        "exito" => true,
        "texto" => $texto,
        "resultados" => $resultados
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Error en la base de datos: " . $e->getMessage()
    ]);
}
?>