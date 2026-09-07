<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

$companias = $pdo->query("SELECT id, nombre FROM compania ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alta de Ómnibus</title>
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="d-flex">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto">
            <div class="tarjeta">
                <h2>Dar de alta un ómnibus</h2>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="alta_omnibus.php" method="POST">
                    <div class="campo mb-3">
                        <label for="compania_id">Compañía</label>
                        <select id="compania_id" name="compania_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione una compañía...</option>
                            <?php foreach ($companias as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo mb-3">
                        <label for="n_omnibus">Número de Coche/Ómnibus</label>
                        <input type="number" id="n_omnibus" name="n_omnibus" class="form-control" min="1" required>
                    </div>

                    <div class="campo mb-3">
                        <label for="asientos">Cantidad de Asientos</label>
                        <input type="number" id="asientos" name="asientos" class="form-control" min="1" max="100" required>
                    </div>

                    <div class="campo mb-3">
                        <label for="ruta_key">Identificador / Nombre de Ruta</label>
                        <input type="text" id="ruta_key" name="ruta_key" class="form-control" value="urbano" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Registrar Ómnibus</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>