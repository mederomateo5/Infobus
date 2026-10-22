<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión de usuario
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['usuario', 'compania', 'administrador'])) {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

$userId = $_SESSION['user_id'];
$msg = $_GET['msg'] ?? '';

// Obtener datos actuales del usuario (para mostrar su teléfono actual)
$stmt = $pdo->prepare("SELECT id, nombre, apellido, email, telefono, tipo FROM users WHERE id = ?");
$stmt->execute([$userId]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Teléfono</title>
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar Lateral -->
        <?php include '../../private/includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-4 overflow-auto bg-body">
            <div class="container-fluid">

                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-body fw-bold m-0">Actualizar Teléfono</h1>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fa-solid fa-user me-1"></i> <?= ucfirst(htmlspecialchars($usuario['tipo'])) ?>
                    </span>
                </div>

                <!-- Alertas de Mensajes -->
                <?php if ($msg !== ''): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Cambio de Teléfono -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="actualizar_telefono.php" method="POST">
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Teléfono Actual</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['telefono']) ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="nuevo_telefono" class="form-label fw-semibold">Nuevo Teléfono</label>
                                <input type="text" name="nuevo_telefono" id="nuevo_telefono" class="form-control" required placeholder="Ingresa tu nuevo número de teléfono">
                            </div>

                            <div class="mb-4">
                                <label for="password_actual" class="form-label fw-semibold">Contraseña Actual (por seguridad)</label>
                                <input type="password" name="password_actual" id="password_actual" class="form-control" required placeholder="Introduce tu contraseña para confirmar">
                                <div class="form-text">Necesitamos tu contraseña actual para autorizar este cambio.</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fa-solid fa-save me-1"></i> Guardar nuevo teléfono
                                </button>
                                <a href="../usuario/informacion.php" class="btn btn-secondary px-4 py-2">Cancelar</a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </main>

    </div>
</body>
</html>