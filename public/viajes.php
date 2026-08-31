<?php
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado. Esta página es exclusiva para administradores.");
}

require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Viajes</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="../public/css/panel_admin.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido -->
    <div class="d-flex">

        <!-- 1. Sidebar a la izquierda -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- 2. Contenido principal a la derecha (flex-grow-1 ocupa el espacio restante) -->
        <main class="flex-grow-1 p-4 bg-dark overflow-hidden">

            <div id="usuarios" class="container-fluid">
                <h1 class="h3 mb-4 text-white fw-bold">Viajes registrados</h1>

                <!-- Tabla dentro de una tarjeta -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class="py-3 px-3">ID</th>
                                        <th scope="col" class="py-3">Ómnibus</th>
                                        <th scope="col" class="py-3">Origen</th>
                                        <th scope="col" class="py-3">Destino</th>
                                        <th scope="col" class="py-3">Precio</th>
                                        <th scope="col" class="py-3">Fecha</th>
                                        <th scope="col" class="py-3">Hora salida</th>
                                        <th scope="col" class="py-3">Hora llegada</th>
                                        <th scope="col" class="py-3">Estado</th>
                                        <th scope="col" class="py-3 px-3 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="resultado" class="table-group-divider">
                                    <!-- Tu JavaScript renderizará los <tr> aquí sin problemas -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="js/manejarbuscador.js"></script>

    <!-- 2. Disparas la función usando DOMContentLoaded -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarTodoViajes();
        });
    </script>
</body>
</html>