<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['usuario'])) {
    die("Acceso denegado. Esta página es exclusiva para usuarios.");
}

require_once '../../private/config/database.php';

$userId = $_SESSION['user_id'];
$msg = $_GET['msg'] ?? '';

// Obtener la información del usuario desde la base de datos
$stmt = $pdo->prepare("SELECT nombre, apellido, email, telefono, tipo FROM users WHERE id = ?");
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
    <title>Mi información</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>

<body>
    <?php include '../../private/includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo de Alto Completo -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar Lateral -->
        <?php include '../../private/includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-4 overflow-auto">

            <div class="container-fluid">

                <!-- Encabezado de Bienvenida -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-dark fw-bold m-0">Mi información</h1>
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

                <!-- Tarjeta con los datos del usuario -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <!-- Nombre -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" disabled>
                            </div>

                            <!-- Apellido -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Apellido</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['apellido']) ?>" disabled>
                            </div>

                            <!-- Teléfono (con botón de cambio al costado) -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['telefono']) ?>" disabled>
                                    <a href="../actualizar/actualizar_telefono_gui.php" class="btn btn-outline-primary" title="Cambiar teléfono">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Tipo de cuenta -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tipo de cuenta</label>
                                <input type="text" class="form-control" value="<?= ucfirst(htmlspecialchars($usuario['tipo'])) ?>" disabled>
                            </div>

                            <!-- Correo Electrónico (con botón de cambio al costado) -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Correo Electrónico</label>
                                <div class="input-group">
                                    <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                                    <a href="../actualizar/actualizar_email_gui.php" class="btn btn-outline-primary" title="Cambiar correo electrónico">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Contraseña (con asteriscos y botón de cambio al costado) -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="••••••••" disabled>
                                    <a href="../actualizar/actualizar_contrasenia_gui.php" class="btn btn-outline-primary" title="Cambiar contraseña">
                                        <i class="fa-solid fa-key"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </main>

    </div>

</body>

</html>