<?php
session_start();
require_once '../../private/config/config.php';
header("Content-Type: application/json; charset=utf-8");

// Validar que el usuario esté autenticado y tenga rol de administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "exito" => false,
        "mensaje" => "Acceso no autorizado."
    ]);
    exit;
}

require_once '../../private/config/database.php';

try {
    // Leer el JSON enviado desde JavaScript
    $datos = json_decode(file_get_contents("php://input"), true);

    // ==========================================
    // MOSTRAR TODOS
    // ==========================================

    if (isset($datos["mostrarTodo"]) && $datos["mostrarTodo"] === true) {

        // Evitar SELECT * para proteger datos sensibles de los usuarios
        $sql = "SELECT id, nombre, apellido, email, telefono, tipo FROM users";
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
    // MOSTRAR TODOS LOS VIAJES
    // ==========================================

    if (isset($datos["mostrarTodoViajes"]) && $datos["mostrarTodoViajes"] === true) {

        $sql = "SELECT * FROM viajes";
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

    // Evitar SELECT * limitando las columnas devueltas
    $sql = "SELECT id, nombre, apellido, email, telefono, tipo FROM users WHERE nombre LIKE :busqueda";
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
    // Registrar el error internamente de forma segura
    error_log("Error en buscador_usuarios.php: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "exito" => false,
        "mensaje" => "Ocurrió un error al procesar la solicitud en la base de datos."
    ]);
}
?>