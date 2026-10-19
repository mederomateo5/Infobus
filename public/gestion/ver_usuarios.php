<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado. Esta página es exclusiva para administradores.");
}

require_once '../../private/config/database.php';

if (isset($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('$mensaje');
        // Limpiamos el ?msg= de la barra de direcciones para que no vuelva a saltar con F5
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Usuarios</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- 1. Sidebar a la izquierda -->
        <?php include '../../private/includes/sidebar.php'; ?>

        <!-- 2. Contenido principal a la derecha (flex-grow-1 ocupa el espacio restante) -->
        <main class="flex-grow-1 p-4 overflow-auto">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-dark fw-bold m-0">Usuarios Registrados</h1>
                    <a href="<?= BASE_URL ?>alta/alta_usuario_gui.php" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Nuevo Usuario
                    </a>
                </div>

                <!-- Tarjeta contenedora para Búsqueda y Tabla -->
                <div class="card shadow-sm border-0 bg-white rounded-3">
                    <div class="card-body p-4">

                        <!-- Barra de Búsqueda -->
                        <div class="row g-2 mb-4 align-items-center">
                            <div class="col-12 col-md-5 col-lg-4">
                                <input type="text" id="busqueda" class="form-control" placeholder="Buscar...">
                            </div>
                            <div class="col-auto">
                                <button onclick="buscar()" class="btn btn-primary d-inline-flex align-items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                </button>
                            </div>
                            <div class="col-auto">
                                <button onclick="mostrarTodo()" class="btn btn-outline-secondary">
                                    Mostrar todo
                                </button>
                            </div>
                        </div>

                        <!-- Tabla con encabezado suave -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col" class="py-3 px-3">ID</th>
                                        <th scope="col" class="py-3">Nombre</th>
                                        <th scope="col" class="py-3">Apellido</th>
                                        <th scope="col" class="py-3">Email</th>
                                        <th scope="col" class="py-3">Teléfono</th>
                                        <th scope="col" class="py-3">Tipo</th>
                                        <th scope="col" class="py-3 px-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="resultado">
                                    <!-- Tu JavaScript renderizará los <tr> aquí sin problemas -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="<?= BASE_URL ?>assets/js/buscar_usuarios.js"></script>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarTodo();
        });
    </script>
</body>
</html>