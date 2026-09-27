<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

$msg        = $_GET['msg'] ?? '';
$omnibus_id = $_GET['omnibus_id'] ?? null;
$parada_id  = $_GET['parada_id'] ?? null;

if (!$omnibus_id || !$parada_id) {
    header('Location: ../vrutas.php?msg=' . urlencode('Parámetros no válidos.'));
    exit();
}

$stmt = $pdo->prepare("
    SELECT r.*, p.nombre as parada_nombre, o.n_omnibus, c.nombre as compania 
    FROM recorre r 
    JOIN paradas p ON r.parada_id = p.id
    JOIN omnibus o ON r.omnibus_id = o.id
    JOIN compania c ON o.compania_id = c.id
    WHERE r.omnibus_id = ? AND r.parada_id = ?
");
$stmt->execute([$omnibus_id, $parada_id]);
$recorrido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$recorrido) {
    header('Location: ../vrutas.php?msg=' . urlencode('Tramo de ruta no encontrado.'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Secuencia de Recorrido</title>
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-white fw-bold">Modificar Secuencia del Recorrido</h1>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>actualizar/actualizar_ruta.php" method="POST">
                            <input type="hidden" name="omnibus_id" value="<?= htmlspecialchars($recorrido['omnibus_id']) ?>">
                            <input type="hidden" name="parada_id" value="<?= htmlspecialchars($recorrido['parada_id']) ?>">

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Ómnibus</label>
                                    <input type="text" class="form-control" value="Coche #<?= $recorrido['n_omnibus'] ?> (<?= htmlspecialchars($recorrido['compania']) ?>)" disabled>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Parada</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($recorrido['parada_nombre']) ?>" disabled>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Posición / Orden</label>
                                    <input type="number" name="orden" class="form-control" value="<?= htmlspecialchars($recorrido['orden']) ?>" min="1" required>
                                </div>

                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">Actualizar Orden</button>
                                    <a href="../vrutas.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>