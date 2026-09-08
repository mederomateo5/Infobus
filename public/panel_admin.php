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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio - Panel de Administración</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo de Alto Completo -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar Lateral -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-4 overflow-auto">

            <div class="container-fluid">

                <!-- Encabezado de Bienvenida -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-white fw-bold m-0">Panel de Control</h1>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fa-solid fa-user-shield me-1"></i> Administrador
                    </span>
                </div>

                <!-- Tarjetas Informativas / Accesos Rápidos (3 Columnas por Fila) -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">

                    <!-- Usuarios -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">GESTIÓN</p>
                                    <h5 class="card-title fw-bold m-0">Usuarios</h5>
                                </div>
                                <a href="usuarios.php" class="btn btn-light-primary btn-sm text-primary fs-4">
                                    <i class="fa-solid fa-users"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Alta Usuario -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-success">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Alta Usuario</h5>
                                </div>
                                <a href="alta/alta_usuario_gui.php" class="btn btn-light-success btn-sm text-success fs-4">
                                    <i class="fa-solid fa-user-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Ver Compañías -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-secondary">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">GESTIÓN</p>
                                    <h5 class="card-title fw-bold m-0">Ver Compañías</h5>
                                </div>
                                <a href="vcompania.php" class="btn btn-light-secondary btn-sm text-secondary fs-4">
                                    <i class="fa-solid fa-building"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Alta Compañía -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-secondary">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Alta de Compañía</h5>
                                </div>
                                <a href="alta/alta_compania_gui.php" class="btn btn-light-secondary btn-sm text-secondary fs-4">
                                    <i class="fa-solid fa-building-circle-check"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Ver Ómnibus -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-info">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">GESTIÓN</p>
                                    <h5 class="card-title fw-bold m-0">Ver Ómnibus</h5>
                                </div>
                                <a href="vomnibus.php" class="btn btn-light-info btn-sm text-info fs-4">
                                    <i class="fa-solid fa-bus"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Alta Ómnibus -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-info">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Alta de Ómnibus</h5>
                                </div>
                                <a href="alta/alta_omnibus_gui.php" class="btn btn-light-info btn-sm text-info fs-4">
                                    <i class="fa-solid fa-bus-simple"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Ver Recorridos -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">GESTIÓN</p>
                                    <h5 class="card-title fw-bold m-0">Ver Recorridos</h5>
                                </div>
                                <a href="vrutas.php" class="btn btn-light-warning btn-sm text-warning fs-4">
                                    <i class="fa-solid fa-route"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Asignar Recorridos -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-warning">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Asignar Recorridos</h5>
                                </div>
                                <a href="alta/alta_ruta_gui.php" class="btn btn-light-warning btn-sm text-warning fs-4">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Ver Viajes -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">GESTIÓN</p>
                                    <h5 class="card-title fw-bold m-0">Ver Viajes</h5>
                                </div>
                                <a href="viajes.php" class="btn btn-light-primary btn-sm text-primary fs-4">
                                    <i class="fa-solid fa-ticket"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Alta de Viajes -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-primary">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Alta de Viajes</h5>
                                </div>
                                <a href="alta/alta_viaje_gui.php" class="btn btn-light-primary btn-sm text-primary fs-4">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Ver Paradas -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-danger">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">GESTIÓN</p>
                                    <h5 class="card-title fw-bold m-0">Ver Paradas</h5>
                                </div>
                                <a href="vparadas.php" class="btn btn-light-danger btn-sm text-danger fs-4">
                                    <i class="fa-solid fa-location-dot"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Alta de Paradas -->
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 border-start border-4 border-danger">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Alta de Paradas</h5>
                                </div>
                                <a href="alta/alta_parada_gui.php" class="btn btn-light-danger btn-sm text-danger fs-4">
                                    <i class="fa-solid fa-map-pin"></i>
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