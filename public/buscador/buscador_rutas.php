<?php
require_once '../../private/config/config.php';
header("Content-Type: application/json");

require_once '../../private/config/database.php';

try {
    $datos = json_decode(file_get_contents("php://input"), true);

    $sqlBase = "SELECT r.omnibus_id, r.parada_id, r.orden,
                       p.nombre AS parada_nombre,
                       o.n_omnibus,
                       c.nombre AS compania_nombre
                FROM recorre r
                INNER JOIN paradas p ON r.parada_id = p.id
                INNER JOIN omnibus o ON r.omnibus_id = o.id
                INNER JOIN compania c ON o.compania_id = c.id";

    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {
        $sql = $sqlBase . " ORDER BY o.n_omnibus ASC, r.orden ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        echo json_encode([
            "exito" => true,
            "resultados" => $stmt->fetchAll(PDO::FETCH_ASSOC)
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

    $sql = $sqlBase . " WHERE p.nombre LIKE :busqueda 
                       OR c.nombre LIKE :busqueda 
                       OR CAST(o.n_omnibus AS CHAR) LIKE :busqueda 
                       ORDER BY o.n_omnibus ASC, r.orden ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['busqueda' => "%" . $texto . "%"]);

    echo json_encode([
        "exito" => true,
        "texto" => $texto,
        "resultados" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Error en la base de datos: " . $e->getMessage()
    ]);
}
?>