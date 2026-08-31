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
    <title>Inicio - Panel de Administración</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="../public/css/panel_admin.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo (Columna en Móvil / Fila en PC) -->
    <div class="d-flex flex-column flex-md-row">

        <!-- Sidebar Lateral -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-3 p-md-4 overflow-hidden">

            <div class="container-fluid">

                <!-- Encabezado de Bienvenida -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-white fw-bold m-0">Panel de Control</h1>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fa-solid fa-user-shield me-1"></i> Administrador
                    </span>
                </div>

                <!-- Tarjetas Informativas / Accesos Rápidos de Ejemplo -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card shadow-sm border-0 border-start border-4 border-primary">
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

                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card shadow-sm border-0 border-start border-4 border-success">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small fw-medium mb-1">REGISTRO</p>
                                    <h5 class="card-title fw-bold m-0">Alta Usuario</h5>
                                </div>
                                <a href="alta_usuario.php" class="btn btn-light-success btn-sm text-success fs-4">
                                    <i class="fa-solid fa-user-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aquí puedes colocar más tablas, gráficos o contenido general de inicio -->

            </div>

        </main>

    </div>

</body>
</html>