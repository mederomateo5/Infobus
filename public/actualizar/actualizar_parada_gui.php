<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

$msg = $_GET['msg'] ?? '';
$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../gestion/ver_paradas.php?msg=' . urlencode('ID de parada no válido.'));
    exit();
}

// Obtener datos de la parada a editar
$stmt = $pdo->prepare("SELECT id, nombre, latitud, longitud FROM paradas WHERE id = ?");
$stmt->execute([$id]);
$parada = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$parada) {
    header('Location: ../gestion/ver_paradas.php?msg=' . urlencode('Parada no encontrada.'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Parada</title>
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include '../../private/includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-hidden">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-body fw-bold">Editar Parada #<?= htmlspecialchars($parada['id']) ?></h1>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>actualizar/actualizar_parada.php" method="POST">
                            <!-- ID oculto para enviar al procesador -->
                            <input type="hidden" name="id" value="<?= htmlspecialchars($parada['id']) ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nombre de la parada</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($parada['nombre']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Latitud</label>
                                    <input type="number" step="any" name="latitud" class="form-control" value="<?= htmlspecialchars($parada['latitud']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Longitud</label>
                                    <input type="number" step="any" name="longitud" class="form-control" value="<?= htmlspecialchars($parada['longitud']) ?>" required>
                                </div>

                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        Guardar cambios
                                    </button>
                                    <a href="../gestion/ver_paradas.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
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