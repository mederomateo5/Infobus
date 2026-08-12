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
    <title>Usuarios</title>
    <!-- Incluimos el header que contiene el CSS de Bootstrap -->
    <?php include '../includes/header.php'; ?>
    <link rel="stylesheet" href="../public/css/panel_admin.css">
</head>
<body>

    <!-- Contenedor Flexbox para alinear Sidebar + Contenido -->
    <div class="d-flex">

        <!-- 1. Sidebar a la izquierda -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- 2. Contenido principal a la derecha (flex-grow-1 ocupa el espacio restante) -->
        <main class="flex-grow-1 p-4 bg-dark overflow-hidden">
            
            <div id="usuarios" class="container-fluid">
                <h1 class="h3 mb-4 text-white fw-bold">Usuarios registrados</h1>

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

                <!-- Tabla dentro de una tarjeta -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class="py-3 px-3">ID</th>
                                        <th scope="col" class="py-3">Nombre</th>
                                        <th scope="col" class="py-3">Apellido</th>
                                        <th scope="col" class="py-3">Email</th>
                                        <th scope="col" class="py-3">Teléfono</th>
                                        <th scope="col" class="py-3">Tipo</th>
                                        <th scope="col" class="py-3 px-3 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="resultado" class="table-group-divider">
                                    <!-- Tu JavaScript renderizará los <tr> aquí sin problemas -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="js/manejarbuscador.js"></script>
</body>
</html>