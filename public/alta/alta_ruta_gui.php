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

$omnibus = $pdo->query("SELECT o.id, o.n_omnibus, c.nombre as compania FROM omnibus o JOIN compania c ON o.compania_id = c.id ORDER BY c.nombre, o.n_omnibus")->fetchAll(PDO::FETCH_ASSOC);
$paradas = $pdo->query("SELECT id, nombre FROM paradas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alta de Tramo / Recorrido</title>
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

                <h1 class="h3 mb-4 text-body fw-bold">Asignar Parada a Recorrido</h1>

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

                        <form action="alta_ruta.php" method="POST">
                            <div class="row g-3">

                                <!-- Ómnibus -->
                                <div class="col-12 col-md-6">
                                    <label for="omnibus_id" class="form-label fw-semibold">Ómnibus</label>
                                    <select id="omnibus_id" name="omnibus_id" class="form-select" required>
                                        <option value="" disabled selected>Seleccione el ómnibus...</option>
                                        <?php foreach ($omnibus as $o): ?>
                                            <option value="<?= $o['id'] ?>">Coche #<?= $o['n_omnibus'] ?> - <?= htmlspecialchars($o['compania']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Parada -->
                                <div class="col-12 col-md-6">
                                    <label for="parada_id" class="form-label fw-semibold">Parada</label>
                                    <select id="parada_id" name="parada_id" class="form-select" required>
                                        <option value="" disabled selected>Seleccione la parada...</option>
                                        <?php foreach ($paradas as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Orden -->
                                <div class="col-12 col-md-6">
                                    <label for="orden" class="form-label fw-semibold">Orden de Secuencia</label>
                                    <input type="number" id="orden" name="orden" class="form-control" placeholder="Ej: 1 (Origen), 2, 3..." min="1" required>
                                </div>

                                <!-- Botón de Envío -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                                        <i class="fa-solid fa-route"></i> Guardar Tramo
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