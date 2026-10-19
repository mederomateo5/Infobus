<?php
require_once '../../private/config/config.php';
header("Content-Type: application/json");

require_once '../../private/config/database.php';

try {
    $datos = json_decode(file_get_contents("php://input"), true);

    $sqlBase = "SELECT id, nombre, email, telefono FROM compania";

    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {
        $sql = $sqlBase . " ORDER BY nombre ASC";
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

    $sql = $sqlBase . " WHERE nombre LIKE :busqueda 
                       OR email LIKE :busqueda 
                       OR telefono LIKE :busqueda 
                       ORDER BY nombre ASC";

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