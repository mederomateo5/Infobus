<?php
require_once '../../private/config/config.php';
header("Content-Type: application/json");

require_once '../../private/config/database.php';

try {
    // Leer el JSON enviado desde JavaScript
    $datos = json_decode(file_get_contents("php://input"), true);

    // ==========================================
    // MOSTRAR TODOS
    // ==========================================

    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {

        $sql = "SELECT * FROM paradas";
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

    $sql = "SELECT * FROM paradas WHERE nombre LIKE :busqueda";
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