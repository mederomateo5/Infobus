<?php
    // Datos de conexión
    $host = 'localhost';
    $dbname = 'login_project';
    $username = 'root';
    $password = '';
    // Intentar conexión
    try {
        // Crear conexión PDO
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password
        );
        // Mostrar errores reales si algo falla
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
?>