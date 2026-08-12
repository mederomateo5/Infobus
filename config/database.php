<?php
    // Datos de conexión a la base de datos
    $host     = 'localhost';
    $dbname   = 'omnibus_app';
    $username = 'root';
    $password = '';

    // Crear una instancia de PDO para la conexión a la base de datos
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
    }
?>
