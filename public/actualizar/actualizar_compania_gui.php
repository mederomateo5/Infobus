<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

$msg = $_GET['msg'] ?? '';
$id  = $_GET['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../vcompania.php?msg=' . urlencode('ID no válido.'));
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM compania WHERE id = ?");
$stmt->execute([$id]);
$compania = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compania) {
    header('Location: ../vcompania.php?msg=' . urlencode('Compañía no encontrada.'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Compañía</title>
    <?php include '../../includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/panel_admin.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="d-flex">
        <?php include '../../includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-white fw-bold">Editar Compañía #<?= htmlspecialchars($compania['id']) ?></h1>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>actualizar/actualizar_compania.php" method="POST">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($compania['id']) ?>">

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($compania['nombre']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($compania['email']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($compania['telefono']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Contraseña (dejar en blanco para conservar la actual)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Nueva contraseña">
                                </div>

                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">Guardar cambios</button>
                                    <a href="../vcompania.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
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