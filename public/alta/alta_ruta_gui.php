<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

$omnibus = $pdo->query("SELECT o.id, o.n_omnibus, c.nombre as compania FROM omnibus o JOIN compania c ON o.compania_id = c.id ORDER BY c.nombre, o.n_omnibus")->fetchAll(PDO::FETCH_ASSOC);
$paradas = $pdo->query("SELECT id, nombre FROM paradas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alta de Tramo / Recorrido</title>
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="d-flex">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto">
            <div class="tarjeta">
                <h2 class="h3 text-white fw-bold">Asignar Parada a Recorrido</h2>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="alta_ruta.php" method="POST">
                    <div class="campo mb-3">
                        <label for="omnibus_id">Ómnibus</label>
                        <select id="omnibus_id" name="omnibus_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione el ómnibus...</option>
                            <?php foreach ($omnibus as $o): ?>
                                <option value="<?= $o['id'] ?>">Coche #<?= $o['n_omnibus'] ?> - <?= htmlspecialchars($o['compania']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo mb-3">
                        <label for="parada_id">Parada</label>
                        <select id="parada_id" name="parada_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione la parada...</option>
                            <?php foreach ($paradas as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo mb-3">
                        <label for="orden">Orden de Secuencia</label>
                        <input type="number" id="orden" name="orden" class="form-control" placeholder="Ej: 1 (Origen), 2, 3..." min="1" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Guardar Tramo de Recorrido</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>