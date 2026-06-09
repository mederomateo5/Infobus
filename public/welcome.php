<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$nombre = htmlspecialchars($_SESSION['user_name'] ?? 'Usuario');
$tipo   = htmlspecialchars($_SESSION['user_tipo']  ?? 'usuario');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infobus — Bienvenido</title>

</head>
<body>
    <div class="card">
        <h1>Hola, <span><?= $nombre ?></span></h1>
        <div<?= $tipo ?></div>
        <p>Estás dentro del sistema de gestión de Infobus. Desde aquí podrás acceder a todas las funcionalidades según tu rol.</p>
        <a href="logout.php" class="btn">Cerrar sesión</a>
    </div>
</body>
</html>
