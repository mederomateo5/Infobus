<?php
require_once '../../config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado. Esta página es exclusiva para administradores.");
}

require_once '../../config/database.php';

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alta de Compañía</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido sin tapar el fondo personalizado -->
    <div class="d-flex">

        <!-- Sidebar lateral -->
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Zona principal del formulario -->
        <main class="flex-grow-1 p-4 overflow-auto">

            <div id="alta" class="container-fluid">

                <h1 class="h3 mb-4 text-dark fw-bold">Dar de alta una compañía</h1>

                <!-- Alerta de mensaje (éxito o error) -->
                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Tarjeta contenedora con grilla adaptable de Bootstrap -->
                <div class="card shadow-sm border-0 bg-white rounded-3">
                    <div class="card-body p-4">

                        <form action="alta_compania.php" method="POST">
                            <div class="row g-3">

                                <!-- Nombre -->
                                <div class="col-12 col-md-6">
                                    <label for="nombre" class="form-label fw-semibold">Nombre de la Compañía</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                                </div>

                                <!-- Email -->
                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-12 col-md-6">
                                    <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" id="telefono" name="telefono" class="form-control" required>
                                </div>

                                <!-- Contraseña -->
                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label fw-semibold">Contraseña</label>
                                    <input type="password" id="password" name="password" class="form-control" required>
                                </div>

                                <!-- Botón de Envío -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                                        <i class="fa-solid fa-building"></i> Dar de alta
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </main>

    </div>

    <script src="<?= BASE_URL ?>js/validaciones.js"></script>
</body>
</html>