<?php
require_once '../../private/config/config.php';
header("Content-Type: application/json");

require_once '../../private/config/database.php';

try {
    $datos = json_decode(file_get_contents("php://input"), true);

    $sqlBase = "SELECT o.*, c.nombre AS compania_nombre 
                FROM omnibus o
                INNER JOIN compania c ON o.compania_id = c.id";

    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {
        $sql = $sqlBase . " ORDER BY c.nombre ASC, o.n_omnibus ASC";
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

    $sql = $sqlBase . " WHERE c.nombre LIKE :busqueda 
                       OR CAST(o.n_omnibus AS CHAR) LIKE :busqueda 
                       OR o.ruta_key LIKE :busqueda 
                       ORDER BY c.nombre ASC, o.n_omnibus ASC";

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