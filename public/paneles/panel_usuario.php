<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos de usuario
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}


require_once '../../private/config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio - Panel de Usuario</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo de Alto Completo -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar Lateral -->
        <?php include '../../private/includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-4 overflow-auto bg-body">

            <div class="container-fluid">

                <!-- Encabezado de Bienvenida -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-body fw-bold m-0">Panel de Control</h1>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fa-solid fa-user me-1"></i> Usuario
                    </span>
                </div>

                <!-- Tarjetas Informativas / Accesos Rápidos (3 Columnas por Fila) -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">

                    <!-- Mis Favoritos -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">ACCESO RÁPIDO</p>
                                    <h5 class="card-title fw-bold m-0">Mis Favoritos</h5>
                                </div>
                                <a href="../usuario/favoritos.php" class="btn btn-light-warning btn-sm text-warning fs-4">
                                    <i class="fa-solid fa-star"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Mi Información -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-info">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">PERFIL</p>
                                    <h5 class="card-title fw-bold m-0">Mi Información</h5>
                                </div>
                                <a href="../usuario/informacion.php" class="btn btn-light-info btn-sm text-info fs-4">
                                    <i class="fa-solid fa-circle-info"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actualizar Correo -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-success">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">SEGURIDAD</p>
                                    <h5 class="card-title fw-bold m-0">Actualizar Correo</h5>
                                </div>
                                <a href="../actualizar/actualizar_email_gui.php" class="btn btn-light-success btn-sm text-success fs-4">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actualizar Contraseña -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-danger">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">SEGURIDAD</p>
                                    <h5 class="card-title fw-bold m-0">Actualizar Contraseña</h5>
                                </div>
                                <a href="../actualizar/actualizar_contrasenia_gui.php" class="btn btn-light-danger btn-sm text-danger fs-4">
                                    <i class="fa-solid fa-lock"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actualizar Teléfono -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">SEGURIDAD</p>
                                    <h5 class="card-title fw-bold m-0">Actualizar Teléfono</h5>
                                </div>
                                <a href="../actualizar/actualizar_telefono_gui.php" class="btn btn-light-primary btn-sm text-primary fs-4">
                                    <i class="fa-solid fa-phone"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>

    </div>

</body>
</html>