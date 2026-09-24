<?php
require_once '../../config/config.php';
session_start();

// Verificar sesión y permisos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

$msg = $_GET['msg'] ?? '';
$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../viajes.php?msg=' . urlencode('ID de viaje no válido.'));
    exit();
}

// Obtener los datos del viaje con la agencia y número de ómnibus
$stmt = $pdo->prepare("
    SELECT v.*, o.n_omnibus, c.nombre AS agencia_nombre
    FROM viajes v
    JOIN omnibus o ON v.omnibus_id = o.id
    JOIN compania c ON o.compania_id = c.id
    WHERE v.id = ?
");
$stmt->execute([$id]);
$viaje = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$viaje) {
    header('Location: ../viajes.php?msg=' . urlencode('Viaje no encontrado.'));
    exit();
}

// Obtener todas las paradas para los select de origen y destino
try {
    $stmtParadas = $pdo->query("SELECT id, nombre FROM paradas ORDER BY nombre ASC");
    $paradas = $stmtParadas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $paradas = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Viaje</title>
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="d-flex">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-white fw-bold">Editar Viaje #<?= htmlspecialchars($viaje['id']) ?></h1>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>actualizar/actualizar_viaje.php" method="POST" onsubmit="return validarViaje(this);">
                            <!-- ID oculto para enviar al procesador -->
                            <input type="hidden" name="id" value="<?= htmlspecialchars($viaje['id']) ?>">

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Número de Ómnibus</label>
                                    <input type="number" name="n_omnibus" class="form-control" value="<?= htmlspecialchars($viaje['n_omnibus']) ?>" required min="1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Agencia</label>
                                    <select name="agencia" class="form-select" required>
                                        <?php 
                                        $agencias = ['COT', 'TAB', 'TURIL', 'URBANO'];
                                        foreach ($agencias as $ag): 
                                        ?>
                                            <option value="<?= $ag ?>" <?= ($viaje['agencia_nombre'] === $ag) ? 'selected' : '' ?>>
                                                <?= $ag ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Parada de Origen</label>
                                    <select name="origen_id" class="form-select" required>
                                        <?php foreach ($paradas as $parada): ?>
                                            <option value="<?= $parada['id'] ?>" <?= ($viaje['origen_id'] == $parada['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($parada['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Parada de Destino</label>
                                    <select name="destino_id" class="form-select" required>
                                        <?php foreach ($paradas as $parada): ?>
                                            <option value="<?= $parada['id'] ?>" <?= ($viaje['destino_id'] == $parada['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($parada['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Asientos Disponibles</label>
                                    <input type="number" name="asientos_disponibles" class="form-control" value="<?= htmlspecialchars($viaje['asientos_disponibles']) ?>" required min="0" max="100">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Precio ($)</label>
                                    <input type="number" step="0.01" name="precio" class="form-control" value="<?= htmlspecialchars($viaje['precio']) ?>" required min="0">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($viaje['fecha']) ?>" required>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Hora de Salida</label>
                                    <input type="time" name="hora" class="form-control" value="<?= htmlspecialchars($viaje['hora']) ?>" required>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Hora de Llegada</label>
                                    <input type="time" name="hora_llegada" class="form-control" value="<?= htmlspecialchars($viaje['hora_llegada']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="es_directo" name="es_directo" value="1" <?= ($viaje['es_directo'] == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="es_directo">
                                            Es servicio directo
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ya_salio" name="ya_salio" value="1" <?= ($viaje['ya_salio'] == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="ya_salio">
                                            El viaje ya salió
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        Guardar cambios
                                    </button>
                                    <a href="../viajes.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
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