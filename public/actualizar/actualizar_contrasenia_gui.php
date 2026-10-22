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

// Obtener datos actuales del usuario (para mostrar su tipo en la vista)
$stmt = $pdo->prepare("SELECT id, nombre, apellido, email, tipo FROM users WHERE id = ?");
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
    <title>Actualizar Contraseña</title>
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body class="bg-body">
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
                    <h1 class="h3 text-body fw-bold m-0">Actualizar Contraseña</h1>
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

                <!-- Formulario de Cambio de Contraseña -->
                <div class="card shadow-sm border-0 bg-body">
                    <div class="card-body p-4">
                        <form action="actualizar_contrasenia.php" method="POST">

                            <div class="mb-3">
                                <label for="nuevo_password" class="form-label fw-semibold text-body">Nueva Contraseña</label>
                                <input type="password" name="nuevo_password" id="nuevo_password" class="form-control" required minlength="8" placeholder="Mínimo 8 caracteres, una mayúscula y un símbolo">
                                <div class="form-text">Debe tener al menos 8 caracteres, una letra mayúscula y un símbolo especial.</div>
                            </div>

                            <div class="mb-4">
                                <label for="confirmar_password" class="form-label fw-semibold text-body">Confirmar Nueva Contraseña</label>
                                <input type="password" name="confirmar_password" id="confirmar_password" class="form-control" required minlength="8" placeholder="Repite la nueva contraseña">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fa-solid fa-save me-1"></i> Cambiar contraseña
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