<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['compania', 'administrador'])) {
    die("Acceso denegado. Esta página es exclusiva para compañías o administradores.");
}

require_once '../../private/config/database.php';

if (isset($_GET['msg'])) {
    $mensaje = htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8');
    echo "<script>
        alert('$mensaje');
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Viajes Registrados</title>
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <div class="d-flex flex-column flex-md-row min-vh-100">
        <?php include '../../private/includes/sidebar.php'; ?>

        <main class="flex-grow-1 p-4 overflow-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-dark fw-bold m-0">Viajes Registrados</h1>
                <a href="<?= BASE_URL ?>alta/alta_viaje_gui.php" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Nuevo Viaje
                </a>
            </div>

            <div class="card shadow-sm border-0 bg-white rounded-3">
                <div class="card-body p-4">
                    <div class="row g-2 mb-4 align-items-center">
                        <div class="col-12 col-md-5 col-lg-4">
                            <input type="text" id="busqueda" class="form-control" placeholder="Buscar por origen o destino...">
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

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col" class="py-3 px-3">ID</th>
                                    <th scope="col" class="py-3">Ómnibus</th>
                                    <th scope="col" class="py-3">Origen</th>
                                    <th scope="col" class="py-3">Destino</th>
                                    <th scope="col" class="py-3">Precio</th>
                                    <th scope="col" class="py-3">Asientos</th>
                                    <th scope="col" class="py-3">Directo</th>
                                    <th scope="col" class="py-3">Días</th>
                                    <th scope="col" class="py-3">Salida</th>
                                    <th scope="col" class="py-3">Llegada</th>
                                    <th scope="col" class="py-3">Estado</th>
                                    <th scope="col" class="py-3 px-3 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="resultado">
                                <!-- Renderizado desde JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?= BASE_URL ?>assets/js/buscar_viajes.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            mostrarTodo();
        });
    </script>
</body>
</html>