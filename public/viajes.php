<?php
require_once '../config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado. Esta página es exclusiva para administradores.");
}

require_once '../config/database.php';

if (isset($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('$mensaje');
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Viajes Registrados</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido -->
    <div class="d-flex">

        <!-- 1. Sidebar a la izquierda -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- 2. Contenido principal a la derecha -->
        <main class="flex-grow-1 p-4 bg-dark overflow-hidden">

            <div id="viajes" class="container-fluid">
                <h1 class="h3 mb-4 text-white fw-bold">Viajes registrados</h1>

                <!-- Barra de Búsqueda -->
                <div class="row g-2 mb-4 align-items-center">
                    <div class="col-12 col-md-5 col-lg-4">
                        <input type="text" id="busqueda" class="form-control" placeholder="Buscar por origen o destino...">
                    </div>
                    <div class="col-auto">
                        <button onclick="buscar()" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Buscar
                        </button>
                    </div>
                    <div class="col-auto">
                        <button onclick="mostrarTodo()" class="btn btn-outline-secondary">
                            Mostrar todo
                        </button>
                    </div>
                </div>

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
                                        <th scope="col" class="py-3">Asientos</th>
                                        <th scope="col" class="py-3">Directo</th>
                                        <th scope="col" class="py-3">Fecha</th>
                                        <th scope="col" class="py-3">Salida</th>
                                        <th scope="col" class="py-3">Llegada</th>
                                        <th scope="col" class="py-3">Estado</th>
                                        <th scope="col" class="py-3 px-3 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="resultado" class="table-group-divider">
                                    <!-- Renderizado desde JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="<?= BASE_URL ?>js/buscarviajes.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarTodo();
        });
    </script>
</body>
</html>