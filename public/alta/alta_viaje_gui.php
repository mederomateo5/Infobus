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

// Obtener la lista de paradas para los select de origen y destino
try {
    $stmtParadas = $pdo->query("SELECT id, nombre FROM paradas ORDER BY nombre ASC");
    $paradas = $stmtParadas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $paradas = [];
}

// Capturar mensaje de notificación si existe
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alta de Viajes</title>
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
        <main class="flex-grow-1 p-4 overflow-hidden">

            <div id="alta" class="container-fluid">

                <h1 class="h3 mb-4 text-white fw-bold">Dar de alta un viaje</h1>

                <!-- Alerta de mensaje (éxito o error) -->
                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Tarjeta contenedora con grilla adaptable de Bootstrap -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        <form action="alta_viaje.php" method="POST" onsubmit="return validarViaje(this);">
                            <div class="row g-3">

                                <!-- Número de Ómnibus -->
                                <div class="col-12 col-md-6">
                                    <label for="n_omnibus" class="form-label fw-semibold">Número de Ómnibus</label>
                                    <input type="number" id="n_omnibus" name="n_omnibus" class="form-control" placeholder="Ej: 104" required min="1">
                                </div>

                                <!-- Agencia -->
                                <div class="col-12 col-md-6">
                                    <label for="agencia" class="form-label fw-semibold">Agencia</label>
                                    <select id="agencia" name="agencia" class="form-select" required>
                                        <option value="" disabled selected>Seleccione una agencia...</option>
                                        <option value="COT">COT</option>
                                        <option value="TAB">TAB</option>
                                        <option value="TURIL">TURIL</option>
                                        <option value="URBANO">URBANO</option>
                                    </select>
                                </div>

                                <!-- Parada Origen -->
                                <div class="col-12 col-md-6">
                                    <label for="origen_id" class="form-label fw-semibold">Parada de Origen</label>
                                    <select id="origen_id" name="origen_id" class="form-select" required>
                                        <option value="" disabled selected>Seleccione origen...</option>
                                        <?php foreach ($paradas as $parada): ?>
                                            <option value="<?= $parada['id'] ?>"><?= htmlspecialchars($parada['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Parada Destino -->
                                <div class="col-12 col-md-6">
                                    <label for="destino_id" class="form-label fw-semibold">Parada de Destino</label>
                                    <select id="destino_id" name="destino_id" class="form-select" required>
                                        <option value="" disabled selected>Seleccione destino...</option>
                                        <?php foreach ($paradas as $parada): ?>
                                            <option value="<?= $parada['id'] ?>"><?= htmlspecialchars($parada['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Asientos Disponibles -->
                                <div class="col-12 col-md-6">
                                    <label for="asientos_disponibles" class="form-label fw-semibold">Asientos Disponibles</label>
                                    <input type="number" id="asientos_disponibles" name="asientos_disponibles" class="form-control" placeholder="Ej: 44" required min="1" max="100">
                                </div>

                                <!-- Precio -->
                                <div class="col-12 col-md-6">
                                    <label for="precio" class="form-label fw-semibold">Precio ($)</label>
                                    <input type="number" id="precio" step="0.01" name="precio" class="form-control" placeholder="Ej: 350.00" required min="0">
                                </div>

                                <!-- Fecha -->
                                <div class="col-12 col-md-4">
                                    <label for="fecha" class="form-label fw-semibold">Fecha</label>
                                    <input type="date" id="fecha" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                                </div>

                                <!-- Hora Salida -->
                                <div class="col-12 col-md-4">
                                    <label for="hora" class="form-label fw-semibold">Hora de Salida</label>
                                    <input type="time" id="hora" name="hora" class="form-control" required>
                                </div>

                                <!-- Hora Llegada -->
                                <div class="col-12 col-md-4">
                                    <label for="hora_llegada" class="form-label fw-semibold">Hora de Llegada</label>
                                    <input type="time" id="hora_llegada" name="hora_llegada" class="form-control" required>
                                </div>

                                <!-- Servicio Directo -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_directo" name="es_directo" value="1">
                                        <label class="form-check-label fw-semibold" for="es_directo">
                                            Es servicio directo
                                        </label>
                                    </div>
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

    <script src="<?= BASE_URL ?>js/validaciones.js"></script>
</body>
</html>