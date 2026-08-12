<?php
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado. Esta página es exclusiva para administradores.");
}

require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Usuarios</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="../public/css/panel_admin.css">
</head>
<body>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido sin tapar el fondo personalizado -->
    <div class="d-flex">

        <!-- Sidebar lateral -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Zona principal del formulario -->
        <main class="flex-grow-1 p-4 overflow-hidden">
            
            <div id="alta" class="container-fluid">
                
                <h1 class="h3 mb-4 text-white fw-bold">Dar de alta un usuario</h1>

                <!-- Tarjeta contenedora con grilla adaptable de Bootstrap -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        
                        <form action="../public/alta_usuario.php" method="POST">
                            <div class="row g-3">
                                
                                <!-- Nombre -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>

                                <!-- Apellido -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" required>
                                </div>

                                <!-- Email -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <!-- Teléfono -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" required>
                                </div>

                                <!-- Contraseña -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Contraseña</label>
                                    <input type="password" name="password" class="form-control" required minlength="6">
                                </div>

                                <!-- Tipo de Usuario -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold">Tipo de usuario</label>
                                    <select name="tipo" class="form-select" required>
                                        <option value="usuario">Usuario</option>
                                        <option value="chofer">Chofer</option>
                                        <option value="administrador">Administrador</option>
                                    </select>
                                </div>

                                <!-- Botón de Envío -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                                        <i class="fa-solid fa-user-plus"></i> Dar de alta
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </main>

    </div>

    <script src="js/validaciones.js"></script>
</body>
</html>