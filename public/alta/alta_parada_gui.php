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
    <title>Alta de Paradas</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>

<body>
    <?php include '../../includes/header.php'; ?>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido sin tapar el fondo personalizado -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar lateral -->
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Zona principal del formulario -->
        <main class="flex-grow-1 p-4 overflow-auto">

            <div id="alta" class="container-fluid">

                <h1 class="h3 mb-4 text-dark fw-bold">Dar de alta una parada</h1>

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

                        <form action="alta_parada.php" method="POST">
                            <div class="row g-3">

                                <!-- Nombre -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input
                                     type="text"
                                      name="nombre"
                                       class="form-control" required>
                                </div>

                                <!-- Latitud -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Latitud</label>
                                    <input
                                        type="number"
                                        name="latitud"
                                        class="form-control"
                                        required
                                        min="-90"
                                        max="90"
                                        step="0.0000001"
                                        placeholder="Ej: -34.6037220" required>
                                </div>

                                 <!-- Longitud -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Longitud</label>
                                    <input
                                        type="number"
                                        name="longitud"
                                        class="form-control"
                                        required
                                        min="-90"
                                        max="90"
                                        step="0.0000001"
                                        placeholder="Ej: -34.6037220" required>
                                </div>

                                <!-- Botón de Envío -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                                        <i class="fa-solid fa-user-plus"></i> Dar de alta
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