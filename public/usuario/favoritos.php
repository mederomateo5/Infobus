<?php
require_once '../../config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['usuario'])) {
    die("Acceso denegado. Esta página es exclusiva para usuarios.");
}

require_once '../../config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Favoritos</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo de Alto Completo -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar Lateral -->
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-4 overflow-auto">

            <div class="container-fluid">

                <!-- Encabezado de Bienvenida -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-dark fw-bold m-0">Mis favoritos</h1>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fa-solid fa-user me-1"></i> Usuario
                    </span>
                </div>

                <!-- Tarjetas Informativas / Accesos Rápidos (3 Columnas por Fila) -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">

                   

                                    
                </div>

            </div>

        </main>

    </div>

</body>
</html>