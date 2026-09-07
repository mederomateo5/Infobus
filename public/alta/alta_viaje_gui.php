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
        <main class="flex-grow-1 p-4 overflow-auto">

            <!-- TARJETA ALTA DE VIAJE -->
            <div class="tarjeta" id="altaV">
                <h2>Dar de alta un viaje</h2>

                <!-- Alerta de mensaje (éxito o error) -->
                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="alta_viaje.php" method="POST" onsubmit="return validarViaje(this);">

                    <div class="campo">
                        <label for="n_omnibus">Número de Ómnibus</label>
                        <input type="number" id="n_omnibus" name="n_omnibus" class="form-control" placeholder="Ej: 104" required min="1">
                    </div>

                    <div class="campo">
                        <label for="agencia">Agencia</label>
                        <select id="agencia" name="agencia" class="form-select" required>
                            <option value="" disabled selected>Seleccione una agencia...</option>
                            <option value="COT">COT</option>
                            <option value="TAB">TAB</option>
                            <option value="TURIL">TURIL</option>
                            <option value="URBANO">URBANO</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="origen_id">Parada de Origen</label>
                        <select id="origen_id" name="origen_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione origen...</option>
                            <?php foreach ($paradas as $parada): ?>
                                <option value="<?= $parada['id'] ?>"><?= htmlspecialchars($parada['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="destino_id">Parada de Destino</label>
                        <select id="destino_id" name="destino_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione destino...</option>
                            <?php foreach ($paradas as $parada): ?>
                                <option value="<?= $parada['id'] ?>"><?= htmlspecialchars($parada['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="asientos_disponibles">Asientos Disponibles</label>
                        <input type="number" id="asientos_disponibles" name="asientos_disponibles" class="form-control" placeholder="Ej: 44" required min="1" max="100">
                    </div>

                    <div class="campo">
                        <label for="precio">Precio ($)</label>
                        <input type="number" id="precio" step="0.01" name="precio" class="form-control" placeholder="Ej: 350.00" required min="0">
                    </div>

                    <div class="campo">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="campo">
                        <label for="hora">Hora de Salida</label>
                        <input type="time" id="hora" name="hora" class="form-control" required>
                    </div>

                    <div class="campo">
                        <label for="hora_llegada">Hora de Llegada</label>
                        <input type="time" id="hora_llegada" name="hora_llegada" class="form-control" required>
                    </div>

                    <div class="campo mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="es_directo" name="es_directo" value="1">
                            <label class="form-check-label text-white" for="es_directo">
                                Es servicio directo
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Dar de alta viaje</button>
                </form>
            </div>

        </main>

    </div>

    <script src="<?= BASE_URL ?>js/validaciones.js"></script>
</body>
</html>