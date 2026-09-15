<?php
$host     = 'localhost';
$dbname   = 'omnibus_app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si hay un error de conexión, detenemos la ejecución y mostramos el error exacto
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => true,
        'mensaje' => "Error de conexión MySQL: " . $e->getMessage()
    ]);
    exit;
}
?>