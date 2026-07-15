<?php
    session_start();

    // Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Redirigir según el tipo de usuario
    switch ($_SESSION['user_tipo']) {
        case 'chofer':
            header('Location: chofer.php');
            exit();

        case 'administrador':
            header('Location: panel_admin.php');
            exit();

        case 'usuario':
            // Sigue de largo y muestra el panel de abajo
            break;

        default:
            // Tipo desconocido, por las dudas lo mandamos al login
            header('Location: login.php');
            exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="css/welcome.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="layout">
        <!-- ---------- Panel lateral ---------- -->
        <div class="sidebar">
            <h2>Usuario</h2>

            <button class="menu-btn activo" onclick="mostrarSeccion('inicio', this)">Inicio</button>
            <button class="menu-btn" onclick="mostrarSeccion('opcion1', this)">Opción 1</button>
            <button class="menu-btn" onclick="mostrarSeccion('opcion2', this)">Opción 2</button>
            <button class="menu-btn" onclick="mostrarSeccion('opcion3', this)">Opción 3</button>

            <button class="menu-btn salir" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>

        <!-- ---------- Contenido ---------- -->
        <div class="contenido">

            <!-- INICIO -->
            <div class="tarjeta activa" id="inicio">
                <h1>Bienvenido,  <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
                <p>Este es tu panel. Desde acá vas a poder acceder a las distintas opciones disponibles para tu cuenta.</p>
            </div>

            <!-- OPCIÓN 1 (placeholder) -->
            <div class="tarjeta" id="opcion1">
                <h1>Opción 1</h1>
                <p>Contenido de la opción 1 (pendiente de desarrollar).</p>
            </div>

            <!-- OPCIÓN 2 (placeholder) -->
            <div class="tarjeta" id="opcion2">
                <h1>Opción 2</h1>
                <p>Contenido de la opción 2 (pendiente de desarrollar).</p>
            </div>

            <!-- OPCIÓN 3 (placeholder) -->
            <div class="tarjeta" id="opcion3">
                <h1>Opción 3</h1>
                <p>Contenido de la opción 3 (pendiente de desarrollar).</p>
            </div>

        </div><!-- Fin Contenido -->
    </div><!-- Fin Layout -->

    <script>
        function mostrarSeccion(id, boton) {
            // Ocultar todas las tarjetas
            document.querySelectorAll('.tarjeta').forEach(function(tarjeta) {
                tarjeta.classList.remove('activa');
            });

            // Mostrar la elegida
            document.getElementById(id).classList.add('activa');

            // Marcar botón activo en el menú
            document.querySelectorAll('.menu-btn').forEach(function(btn) {
                btn.classList.remove('activo');
            });
            boton.classList.add('activo');
        }
    </script>
</body>
</html>
