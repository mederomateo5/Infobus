<?php
require_once '../config/config.php';
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

if (isset($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('$mensaje');
        // Limpiamos el ?msg= de la barra de direcciones para que no vuelva a saltar con F5
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ómnibus Registrados</title>
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
        <main class="flex-grow-1 p-4 overflow-auto">

            <div id="omnibus" class="container-fluid">
                
                <!-- Encabezado (Texto oscuro para fondo claro) -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-dark fw-bold m-0">Ómnibus Registrados</h1>
                    <a href="<?= BASE_URL ?>alta/alta_omnibus_gui.php" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Nuevo Ómnibus
                    </a>
                </div>

                <!-- Tarjeta contenedora para Búsqueda y Tabla -->
                <div class="card shadow-sm border-0 bg-white rounded-3">
                    <div class="card-body p-4">
                        
                        <!-- Barra de Búsqueda -->
                        <div class="row g-2 mb-4 align-items-center">
                            <div class="col-12 col-md-5 col-lg-4">
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar por coche, compañía, ruta...">
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

                        <!-- Tabla con encabezado suave -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="py-3 px-3">ID</th>
                                        <th scope="col" class="py-3">Compañía</th>
                                        <th scope="col" class="py-3">N° Coche</th>
                                        <th scope="col" class="py-3">Asientos</th>
                                        <th scope="col" class="py-3">Ruta / Clave</th>
                                        <th scope="col" class="py-3 px-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="resultado">
                                    <!-- El JavaScript renderizará los <tr> aquí -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="<?= BASE_URL ?>js/buscaromnibus.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarTodo();
        });
    </script>
</body>
</html>