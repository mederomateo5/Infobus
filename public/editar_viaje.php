<?php
require_once '../config/config.php';
session_start();

// Verificar sesión y rol de administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../config/database.php';

// Validar ID recibido por GET
$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../public/panel_admin.php?msg=' . urlencode('ID de viaje no válido.'));
    exit();
}

// Obtener los datos del viaje con detalles del ómnibus y compañía
try {
    $stmt = $pdo->prepare("SELECT v.id, v.origen_id, v.destino_id, v.precio, v.ya_salio, v.hora, v.hora_llegada, v.es_directo,
                                  o.n_omnibus, o.asientos, v.asientos_disponibles, c.nombre AS agencia
                           FROM viajes v
                           JOIN omnibus o ON v.omnibus_id = o.id
                           JOIN compania c ON o.compania_id = c.id
                           WHERE v.id = ?");
    $stmt->execute([$id]);
    $viaje = $stmt->fetch();

    if (!$viaje) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('El viaje no existe.'));
        exit();
    }

    // Obtener los días asociados a este viaje
    $stmtDias = $pdo->prepare("SELECT dia_id FROM viaje_dia WHERE viaje_id = ?");
    $stmtDias->execute([$id]);
    $diasAsignados = $stmtDias->fetchAll(PDO::FETCH_COLUMN);

    // Obtener todas las paradas
    $stmtParadas = $pdo->query("SELECT id, nombre FROM paradas ORDER BY nombre ASC");
    $paradas = $stmtParadas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los días de la semana
    $stmtDiasSemana = $pdo->query("SELECT id, nombre FROM dias_semana ORDER BY id ASC");
    $diasSemana = $stmtDiasSemana->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    header('Location: ../public/panel_admin.php?msg=' . urlencode('Error al conectar con la base de datos.'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Viaje</title>
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
    <style>
        .contenedor-editar {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .btn-volver {
            background-color: #ffffff;
            color: #1877f2;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn-volver:hover {
            background-color: #d1d1d1;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <br><br><br>
    <div class="contenedor-editar">
        <a href="panel_admin.php" class="btn-volver">← Volver al Panel</a>

        <div class="tarjeta activa">
            <h1>Editar Viaje (ID: <?= htmlspecialchars($viaje['id']) ?>)</h1>

            <form action="<?= BASE_URL ?>actualizar/actualizar_viaje.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($viaje['id']) ?>">

                <div class="campo">
                    <label>Número de Ómnibus</label>
                    <input type="number" name="n_omnibus" value="<?= htmlspecialchars($viaje['n_omnibus']) ?>" required min="1">
                </div>

                <div class="campo">
                    <label>Asientos Disponibles</label>
                    <input type="number" name="asientos_disponibles" value="<?= htmlspecialchars($viaje['asientos_disponibles']) ?>" required min="0">
                </div>

                <div class="campo">
                    <label>Origen</label>
                    <select name="origen_id" required>
                        <?php foreach ($paradas as $parada): ?>
                            <option value="<?= $parada['id'] ?>" <?= ($viaje['origen_id'] == $parada['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($parada['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Destino</label>
                    <select name="destino_id" required>
                        <?php foreach ($paradas as $parada): ?>
                            <option value="<?= $parada['id'] ?>" <?= ($viaje['destino_id'] == $parada['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($parada['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label>Precio</label>
                    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($viaje['precio']) ?>" required min="0">
                </div>

                <div class="campo">
                    <label>Agencia</label>
                    <select name="agencia" required>
                        <option value="COT" <?= $viaje['agencia'] === 'COT' ? 'selected' : '' ?>>COT</option>
                        <option value="TAB" <?= $viaje['agencia'] === 'TAB' ? 'selected' : '' ?>>TAB</option>
                        <option value="TURIL" <?= $viaje['agencia'] === 'TURIL' ? 'selected' : '' ?>>TURIL</option>
                        <option value="URBANO" <?= $viaje['agencia'] === 'URBANO' ? 'selected' : '' ?>>URBANO</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Días de la semana</label>
                    <div style="display: flex; flex-direction: column; gap: 5px; margin-top: 5px;">
                        <?php foreach ($diasSemana as $dia): ?>
                            <label>
                                <input type="checkbox" name="dias[]" value="<?= $dia['id'] ?>" <?= in_array($dia['id'], $diasAsignados) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($dia['nombre']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="campo">
                    <label>Hora de Salida</label>
                    <input type="time" name="hora" value="<?= htmlspecialchars($viaje['hora']) ?>" required>
                </div>

                <div class="campo">
                    <label>Hora de Llegada</label>
                    <input type="time" name="hora_llegada" value="<?= htmlspecialchars($viaje['hora_llegada']) ?>" required>
                </div>

                <div class="campo">
                    <label>¿Servicio directo?</label>
                    <input type="checkbox" name="es_directo" value="1" <?= $viaje['es_directo'] == 1 ? 'checked' : '' ?>> Sí
                </div>

                <div class="campo">
                    <label>¿Ya salió?</label>
                    <select name="ya_salio" required>
                        <option value="0" <?= $viaje['ya_salio'] == 0 ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= $viaje['ya_salio'] == 1 ? 'selected' : '' ?>>Sí</option>
                    </select>
                </div>

                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>