<?php
require_once '../../config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['compania', 'administrador'])) {
    die("Acceso denegado. Esta página es exclusiva para compañías o administradores.");
}

require_once '../../config/database.php';

// Obtener la lista de paradas para los select de origen y destino
try {
    $stmtParadas = $pdo->query("SELECT id, nombre FROM paradas ORDER BY nombre ASC");
    $paradas = $stmtParadas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $paradas = [];
}

// Obtener la lista de ómnibus junto con el nombre de su agencia para el selector dinámico
try {
    $stmtOmnibusList = $pdo->query("SELECT o.n_omnibus, c.nombre as agencia FROM omnibus o JOIN compania c ON o.compania_id = c.id ORDER BY o.n_omnibus ASC");
    $omnibusList = $stmtOmnibusList->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $omnibusList = [];
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
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar lateral -->
        <?php include '../../includes/sidebar.php'; ?>

        <!-- Zona principal del formulario -->
        <main class="flex-grow-1 p-4 overflow-auto">

            <div id="alta" class="container-fluid">

                <h1 class="h3 mb-4 text-dark fw-bold">Dar de alta un viaje</h1>

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

                        <form action="alta_viaje.php" method="POST" onsubmit="return validarViaje(this);">
                            <div class="row g-3">

                                <!-- Agencia (Posicionada primero a la izquierda) -->
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

                                <!-- Número de Ómnibus (Selector dinámico a la derecha filtrado por agencia) -->
                                <div class="col-12 col-md-6">
                                    <label for="n_omnibus" class="form-label fw-semibold">Número de Ómnibus</label>
                                    <select id="n_omnibus" name="n_omnibus" class="form-select" required>
                                        <option value="" disabled selected>Seleccione una agencia primero...</option>
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
    <script>
        // Cargar los datos de ómnibus desde PHP para filtrado dinámico en frontend
        const omnibusData = <?= json_encode($omnibusList) ?>;
        const agenciaSelect = document.getElementById('agencia');
        const omnibusSelect = document.getElementById('n_omnibus');

        agenciaSelect.addEventListener('change', function() {
            const agenciaSeleccionada = this.value;
            
            // Restablecer opciones
            omnibusSelect.innerHTML = '<option value="" disabled selected>Seleccione el número de ómnibus...</option>';
            
            // Filtrar los ómnibus de la agencia seleccionada
            const filtrados = omnibusData.filter(item => item.agencia === agenciaSeleccionada);
            
            if (filtrados.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No hay ómnibus registrados para esta agencia';
                option.disabled = true;
                omnibusSelect.appendChild(option);
            } else {
                filtrados.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.n_omnibus;
                    option.textContent = 'Ómnibus N° ' + item.n_omnibus;
                    omnibusSelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>