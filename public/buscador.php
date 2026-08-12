<?php
header("Content-Type: application/json");

// 1. Incluir la conexión (aquí ya se crea $pdo)
require_once '../config/database.php';

try {
    // 2. Leer el JSON enviado desde JavaScript
    $datos = json_decode(file_get_contents("php://input"), true);
    $texto = trim($datos["busqueda"] ?? "");

    // Validar que no esté vacío
    if (empty($texto)) {
        echo json_encode([
            "exito" => false,
            "mensaje" => "La búsqueda está vacía",
            "resultados" => []
        ]);
        exit;
    }

    // 3. Consulta preparada usando el $pdo que proviene de database.php
    $sql = "SELECT * FROM users WHERE nombre LIKE :busqueda";
    $stmt = $pdo->prepare($sql);

    // El % permite buscar coincidencias parciales
    $parametro = "%" . $texto . "%";
    $stmt->execute(['busqueda' => $parametro]);

    // 4. Obtener todos los resultados
    $resultados = $stmt->fetchAll();

    // 5. Devolver respuesta en JSON
    echo json_encode([
        "exito" => true,
        "texto" => $texto,
        "resultados" => $resultados
    ]);

} catch (\PDOException $e) {
    // Manejo de errores de base de datos
    http_response_code(500);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Error en la base de datos: " . $e->getMessage()
    ]);
}
?>