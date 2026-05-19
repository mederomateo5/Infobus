<?php
    // Iniciar sesión
    session_start();

    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        // Si no está logueado, volver al login
        header('Location: login.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body>
    <h1>Bienvenido</h1>
    <p>Ya iniciaste sesión correctamente</p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>