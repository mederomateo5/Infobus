<?php
require_once __DIR__ . '/config.php';

// Datos de conexión a la base de datos
$host     = 'localhost';
$dbname   = 'omnibus_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Registrar el error real en los logs del servidor de forma interna y segura
    error_log("Error de conexión MySQL: " . $e->getMessage());

    // Devolver una respuesta genérica al cliente sin filtrar datos internos
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => "Ocurrió un error interno en el servidor."
    ]);
    exit;
}