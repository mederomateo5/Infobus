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
    header('Location: ../gestion/ver_usuarios.php?msg=' . urlencode('ID de usuario no válido.'));
    exit();
}

// Obtener datos del usuario a editar
$stmt = $pdo->prepare("SELECT id, nombre, apellido, email, telefono, tipo FROM users WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: ../gestion/ver_usuarios.php?msg=' . urlencode('Usuario no encontrado.'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Usuario</title>
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include '../../private/includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-hidden">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-body fw-bold">Editar Usuario #<?= htmlspecialchars($usuario['id']) ?></h1>

                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="<?= BASE_URL ?>actualizar/actualizar_usuario.php" method="POST">
                            <!-- Enviaremos el ID oculto -->
                            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Contraseña (Dejar en blanco si no deseas cambiarla)</label>
                                    <input type="password" name="password" class="form-control" minlength="6">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Tipo de usuario</label>
                                    <select name="tipo" class="form-select" required>
                                        <option value="usuario" <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                                        <option value="compania" <?= $usuario['tipo'] === 'compania' ? 'selected' : '' ?>>Compania</option>
                                        <option value="administrador" <?= $usuario['tipo'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        Guardar cambios
                                    </button>
                                    <a href="../gestion/ver_usuarios.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
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