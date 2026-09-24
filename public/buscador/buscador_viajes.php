<?php
require_once '../../config/config.php';
header("Content-Type: application/json");

require_once '../../config/database.php';

try {
    $datos = json_decode(file_get_contents("php://input"), true);

    // Consulta SQL Base relacionando las paradas por sus nombres
    $sqlBase = "SELECT v.*, 
                       p_origen.nombre AS origen_nombre, 
                       p_destino.nombre AS destino_nombre 
                FROM viajes v
                INNER JOIN paradas p_origen ON v.origen_id = p_origen.id
                INNER JOIN paradas p_destino ON v.destino_id = p_destino.id";

    // ==========================================
    // MOSTRAR TODOS
    // ==========================================
    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {

        $sql = $sqlBase . " ORDER BY v.fecha DESC, v.hora ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $resultados = $stmt->fetchAll();

        echo json_encode([
            "exito" => true,
            "resultados" => $resultados
        ]);
        exit;
    }

    // ==========================================
    // BUSCAR
    // ==========================================
    $texto = trim($datos["busqueda"] ?? "");

    if (empty($texto)) {
        echo json_encode([
            "exito" => false,
            "mensaje" => "La búsqueda está vacía",
            "resultados" => []
        ]);
        exit;
    }

    // Busca por nombre de parada de origen o de destino
    $sql = $sqlBase . " WHERE p_origen.nombre LIKE :busqueda OR p_destino.nombre LIKE :busqueda ORDER BY v.fecha DESC";
    $stmt = $pdo->prepare($sql);

    $parametro = "%" . $texto . "%";
    $stmt->execute([
        'busqueda' => $parametro
    ]);

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