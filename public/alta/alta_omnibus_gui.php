<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['compania', 'administrador'])) {
    die("Acceso denegado. Esta página es exclusiva para compañías o administradores.");
}

require_once '../../private/config/database.php';

$companias = $pdo->query("SELECT id, nombre FROM compania ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alta de Ómnibus</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido sin tapar el fondo personalizado -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar lateral -->
        <?php include '../../private/includes/sidebar.php'; ?>

        <!-- Zona principal del formulario -->
        <main class="flex-grow-1 p-4 overflow-auto bg-body">

            <div id="alta" class="container-fluid">

                <h1 class="h3 mb-4 text-body fw-bold">Dar de alta un ómnibus</h1>

                <!-- Alerta de mensaje (éxito o error) -->
                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Tarjeta contenedora con grilla adaptable de Bootstrap -->
                <div class="card shadow-sm border-0 bg-body rounded-3">
                    <div class="card-body p-4">

                        <form action="alta_omnibus.php" method="POST">
                            <div class="row g-3">

                                <!-- Compañía -->
                                <div class="col-12 col-md-6">
                                    <label for="compania_id" class="form-label fw-semibold">Compañía</label>
                                    <select id="compania_id" name="compania_id" class="form-select" required>
                                        <option value="" disabled selected>Seleccione una compañía...</option>
                                        <?php foreach ($companias as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Número de Coche/Ómnibus -->
                                <div class="col-12 col-md-6">
                                    <label for="n_omnibus" class="form-label fw-semibold">Número de Coche/Ómnibus</label>
                                    <input type="number" id="n_omnibus" name="n_omnibus" class="form-control" min="1" required>
                                </div>

                                <!-- Cantidad de Asientos -->
                                <div class="col-12 col-md-6">
                                    <label for="asientos" class="form-label fw-semibold">Cantidad de Asientos</label>
                                    <input type="number" id="asientos" name="asientos" class="form-control" min="1" max="100" required>
                                </div>

                                <!-- Identificador / Nombre de Ruta -->
                                <div class="col-12 col-md-6">
                                    <label for="ruta_key" class="form-label fw-semibold">Identificador / Nombre de Ruta</label>
                                    <input type="text" id="ruta_key" name="ruta_key" class="form-control" value="urbano" required>
                                </div>

                                <!-- Botón de Envío -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                                        <i class="fa-solid fa-bus"></i> Dar de alta
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </main>

    </div>

    <script src="<?= BASE_URL ?>assets/js/validaciones.js"></script>
</body>
</html>