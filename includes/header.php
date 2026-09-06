<?php require_once __DIR__ . '/../config/config.php'; ?>
<nav class="navbar navbar-expand-md bg-white fixed-top shadow-sm px-3 px-md-5 py-2 py-md-3">
    <div class="container-fluid position-relative p-0 flex-wrap flex-md-nowrap">

        <!-- Logo -->
        <a class="navbar-brand me-auto p-0" href="<?= BASE_URL ?>index.php">
            <img src="<?= BASE_URL ?>img/logotipo.png" alt="Logo InfoBus" class="header-logo">
        </a>

        <!-- Links -->
        <ul class="navbar-nav header-links flex-row gap-3 gap-md-4 my-2 my-md-0 mx-auto mx-md-0">
            <li class="nav-item">
                <a class="nav-link text-dark fw-medium px-0" href="<?= BASE_URL ?>index.php">
                    Inicio
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark fw-medium px-0" href="<?= BASE_URL ?>sobre_nosotros.php">
                    Nosotros
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark fw-medium px-0" href="<?= BASE_URL ?>welcome.php">
                    Panel
                </a>
            </li>

        <li class="nav-item">
                <a class="nav-link text-dark fw-medium px-0" href="<?= BASE_URL ?>paradas.php">
                    Paradas
                </a>
            </li>
        </ul>

        <!-- Ícono Usuario -->
        <div class="d-flex align-items-center ms-auto ms-md-0 header-user">
            <a href="<?= BASE_URL ?>login.php" class="text-dark fs-5" title="Iniciar sesión">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

    </div>
</nav>