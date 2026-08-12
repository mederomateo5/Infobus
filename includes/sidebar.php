<?php
// Detecta la página actual para marcar el enlace activo automáticamente
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<aside class="d-flex flex-column flex-shrink-0 p-3 bg-white border-end shadow-sm" 
       style="width: 280px; min-height: 100vh;">
    
    <!-- Brand / Logo del Panel -->
    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none px-2">
        <i class="fa-solid fa-bus fs-4 me-2 text-primary"></i>
        <span class="fs-5 fw-bold">InfoBus Admin</span>
    </a>

    <hr class="my-3 text-secondary opacity-25">

    <!-- Lista de Enlaces de Navegación -->
    <ul class="nav nav-pills flex-column mb-auto gap-1">
        
        <li class="nav-item">
            <a href="inicio.php" 
               class="nav-link <?= $pagina_actual == 'inicio.php' ? 'active text-white' : 'text-dark' ?> d-flex align-items-center">
                <i class="fa-solid fa-chart-line me-3 fs-5" style="width: 20px;"></i>
                <span>Inicio</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="usuarios.php" 
               class="nav-link <?= $pagina_actual == 'usuarios.php' ? 'active text-white' : 'text-dark' ?> d-flex align-items-center">
                <i class="fa-solid fa-user-plus me-3 fs-5" style="width: 20px;"></i>
                <span>Ver Usuarios</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="alta_usuario_gui.php" 
               class="nav-link <?= $pagina_actual == 'alta_usuario_gui.php' ? 'active text-white' : 'text-dark' ?> d-flex align-items-center">
                <i class="fa-solid fa-gauge-high me-3 fs-5" style="width: 20px;"></i>
                <span>Alta de Usuarios</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="viajes.php" 
               class="nav-link <?= $pagina_actual == 'viajes.php' ? 'active text-white' : 'text-dark' ?> d-flex align-items-center">
                <i class="fa-solid fa-gear me-3 fs-5" style="width: 20px;"></i>
                <span>Ver Viajes</span>
            </a>
        </li>

         <li class="nav-item">
            <a href="alta_viaje_gui.php" 
               class="nav-link <?= $pagina_actual == 'alta_viaje_gui.php' ? 'active text-white' : 'text-dark' ?> d-flex align-items-center">
                <i class="fa-solid fa-gear me-3 fs-5" style="width: 20px;"></i>
                <span>Alta de Viajes</span>
            </a>
        </li>

    </ul>

    <hr class="my-3 text-secondary opacity-25">

    <!-- Perfil / Opción inferior (Cerrar sesión) -->
    <div class="dropdown">
        <a href="logout.php" class="d-flex align-items-center text-dark text-decoration-none px-2 py-1 rounded hover-bg">
            <i class="fa-solid fa-right-from-bracket me-3 text-danger fs-5"></i>
            <span class="fw-medium text-danger">Cerrar Sesión</span>
        </a>
    </div>

</aside>