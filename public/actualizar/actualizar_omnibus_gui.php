<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || ($_SESSION['user_tipo'] !== 'administrador' && $_SESSION['user_tipo'] !== 'compania')) {
        die("Acceso denegado.");
}

require_once '../../private/config/database.php';

$msg = $_GET['msg'] ?? '';
$id  = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../gestion/ver_omnibus.php?msg=' . urlencode('ID no válido.'));
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM omnibus WHERE id = ?");
$stmt->execute([$id]);
$omnibus = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$omnibus) {
    header('Location: ../gestion/ver_omnibus.php?msg=' . urlencode('Ómnibus no encontrado.'));
    exit();
}

$companias = $pdo->query("SELECT id, nombre FROM compania ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Ómnibus</title>
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include '../../private/includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto bg-body">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-body fw-bold">Editar Ómnibus #<?= htmlspecialchars($omnibus['id']) ?></h1>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>actualizar/actualizar_omnibus.php" method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($omnibus['id']) ?>">

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Compañía</label>
                                    <select name="compania_id" class="form-select" required>
                                        <?php foreach ($companias as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= ($omnibus['compania_id'] == $c['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Número de Ómnibus</label>
                                    <input type="number" name="n_omnibus" class="form-control" value="<?= htmlspecialchars($omnibus['n_omnibus']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Asientos</label>
                                    <input type="number" name="asientos" class="form-control" value="<?= htmlspecialchars($omnibus['asientos']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Clave / Ruta</label>
                                    <input type="text" name="ruta_key" class="form-control" value="<?= htmlspecialchars($omnibus['ruta_key']) ?>" required>
                                </div>

                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">Guardar cambios</button>
                                    <a href="../gestion/ver_omnibus.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
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