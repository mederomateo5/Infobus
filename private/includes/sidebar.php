<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/head.php';

// Detecta la página actual para marcar el enlace activo automáticamente
$pagina_actual = basename($_SERVER['PHP_SELF']);

// Obtener el tipo de usuario actual de la sesión de forma segura
$tipo_usuario = $_SESSION['user_tipo'] ?? '';
?>

<aside class="sidebar-panel d-flex flex-column flex-shrink-0 p-3 bg-body border-end shadow-sm">

    <!-- Botón para mostrar/ocultar el menú en celulares (solo visible en pantallas chicas) -->
    <button class="btn btn-outline-secondary d-flex d-md-none align-items-center justify-content-between w-100 mb-2"
        type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
        aria-expanded="false" aria-controls="sidebarMenu">
        <span><i class="fa-solid fa-bars me-2"></i>Menú</span>
        <i class="fa-solid fa-chevron-down"></i>
    </button>

    <div class="collapse d-md-block" id="sidebarMenu">

        <!-- Cerrar sesión siempre visible en celulares -->
        <a href="<?= BASE_URL ?>auth/logout.php"
            class="btn btn-outline-danger d-flex d-md-none align-items-center justify-content-center w-100 mb-2">
            <i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar Sesión
        </a>

        <!-- Lista de Enlaces de Navegación -->
        <ul class="nav nav-pills flex-column mb-auto gap-1">

            <?php if ($tipo_usuario === 'compania'): ?>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>paneles/panel_compania.php"
                        class="nav-link <?= $pagina_actual == 'panel_compania.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-chart-line me-3 fs-5" style="width: 20px;"></i>
                        <span>Inicio</span>
                    </a>
                </li>

            <?php endif; ?>

            <!-- ========================================== -->
            <!-- 1. SECCIÓN EXCLUSIVA PARA ADMINISTRADORES  -->
            <!-- ========================================== -->
            <?php if ($tipo_usuario === 'administrador'): ?>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>paneles/panel_admin.php"
                        class="nav-link <?= $pagina_actual == 'panel_admin.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-chart-line me-3 fs-5" style="width: 20px;"></i>
                        <span>Inicio</span>
                    </a>
                </li>

            <?php endif; ?>

            <?php if ($tipo_usuario === 'administrador' || $tipo_usuario === 'compania'): ?>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>paneles/panel_usuario.php"
                        class="nav-link <?= $pagina_actual == 'panel_usuario.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-user me-3 fs-5" style="width: 20px;"></i>
                        <span>Mi usuario</span>
                    </a>
                </li>

            <?php endif; ?>

            <?php if ($tipo_usuario === 'administrador'): ?>

                <!-- Ver Usuarios -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>gestion/ver_usuarios.php"
                        class="nav-link <?= $pagina_actual == 'ver_usuarios.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-users me-3 fs-5" style="width: 20px;"></i>
                        <span>Ver Usuarios</span>
                    </a>
                </li>

                <!-- Alta de Usuarios -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>alta/alta_usuario_gui.php"
                        class="nav-link <?= $pagina_actual == 'alta_usuario_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-user-plus me-3 fs-5" style="width: 20px;"></i>
                        <span>Alta de Usuarios</span>
                    </a>
                </li>

                <!-- Ver Compañías -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>gestion/ver_companias.php"
                        class="nav-link <?= $pagina_actual == 'ver_companias.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-building me-3 fs-5" style="width: 20px;"></i>
                        <span>Ver Compañías</span>
                    </a>
                </li>

                <!-- Alta de Compañías -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>alta/alta_compania_gui.php"
                        class="nav-link <?= $pagina_actual == 'alta_compania_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-building-circle-check me-3 fs-5" style="width: 20px;"></i>
                        <span>Alta de Compañías</span>
                    </a>
                </li>


                <!-- Paradas -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>gestion/ver_paradas.php"
                        class="nav-link <?= $pagina_actual == 'ver_paradas.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-location-dot me-3 fs-5" style="width: 20px;"></i>
                        <span>Ver Paradas</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>alta/alta_parada_gui.php"
                        class="nav-link <?= $pagina_actual == 'alta_parada_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-map-pin me-3 fs-5" style="width: 20px;"></i>
                        <span>Alta de Paradas</span>
                    </a>
                </li>

            <?php endif; ?>

            <?php if ($tipo_usuario === 'administrador' || $tipo_usuario === 'compania'): ?>

                <!-- ========================================== -->
                <!-- 2. SECCIÓN COMPARTIDA (Admin y Compañía)   -->
                <!-- ========================================== -->

                <!-- Ómnibus -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>gestion/ver_omnibus.php"
                        class="nav-link <?= $pagina_actual == 'ver_omnibus.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-bus me-3 fs-5" style="width: 20px;"></i>
                        <span>Ver Ómnibus</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>alta/alta_omnibus_gui.php"
                        class="nav-link <?= $pagina_actual == 'alta_omnibus_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-bus-simple me-3 fs-5" style="width: 20px;"></i>
                        <span>Alta de Ómnibus</span>
                    </a>
                </li>

                <!-- Rutas / Recorridos -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>gestion/ver_rutas.php"
                        class="nav-link <?= $pagina_actual == 'ver_rutas.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-route me-3 fs-5" style="width: 20px;"></i>
                        <span>Ver Recorridos</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>alta/alta_ruta_gui.php"
                        class="nav-link <?= $pagina_actual == 'alta_ruta_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-map-location-dot me-3 fs-5" style="width: 20px;"></i>
                        <span>Asignar Recorrido</span>
                    </a>
                </li>

                <!-- Viajes -->
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>gestion/ver_viajes.php"
                        class="nav-link <?= $pagina_actual == 'ver_viajes.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-ticket me-3 fs-5" style="width: 20px;"></i>
                        <span>Ver Viajes</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>alta/alta_viaje_gui.php"
                        class="nav-link <?= $pagina_actual == 'alta_viaje_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-calendar-plus me-3 fs-5" style="width: 20px;"></i>
                        <span>Alta de Viajes</span>
                    </a>
                </li>

            <?php endif; ?>

            <?php if ($tipo_usuario === 'usuario'): ?>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>paneles/panel_usuario.php"
                        class="nav-link <?= $pagina_actual == 'panel_usuario.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-chart-line me-3 fs-5" style="width: 20px;"></i>
                        <span>Inicio</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>usuario/favoritos.php"
                        class="nav-link <?= $pagina_actual == 'favoritos.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-star me-3 fs-5" style="width: 20px;"></i>
                        <span>Mis favoritos</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>usuario/informacion.php"
                        class="nav-link <?= $pagina_actual == 'informacion.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-circle-info me-3 fs-5" style="width: 20px;"></i>
                        <span>Mi información</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>actualizar/actualizar_email_gui.php"
                        class="nav-link <?= $pagina_actual == 'actualizar_email_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-envelope me-3 fs-5" style="width: 20px;"></i>
                        <span>Actualizar correo</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>actualizar/actualizar_contrasenia_gui.php"
                        class="nav-link <?= $pagina_actual == 'actualizar_contrasenia_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-lock me-3 fs-5" style="width: 20px;"></i>
                        <span>Actualizar contraseña</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>actualizar/actualizar_telefono_gui.php"
                        class="nav-link <?= $pagina_actual == 'actualizar_telefono_gui.php' ? 'active text-body' : 'text-body' ?> d-flex align-items-center">
                        <i class="fa-solid fa-phone me-3 fs-5" style="width: 20px;"></i>
                        <span>Actualizar teléfono</span>
                    </a>
                </li>

            <?php endif; ?>

        </ul>
        
        <hr class="my-3 text-secondary opacity-25">

        <!-- Cerrar sesión -->
        <div class="dropdown">
            <a href="<?= BASE_URL ?>auth/logout.php" class="d-flex align-items-center text-body text-decoration-none px-2 py-1 rounded hover-bg">
                <i class="fa-solid fa-right-from-bracket me-3 text-danger fs-5" style="width: 20px;"></i>
                <span class="fw-medium text-danger">Cerrar Sesión</span>
            </a>
        </div>

    </div>

</aside>