<?php require_once __DIR__ . '/../config/config.php'; ?>
<nav class="navbar navbar-expand-md bg-body fixed-top shadow-sm px-3 px-md-5 py-2 py-md-3 border-bottom">
    <div class="container-fluid position-relative p-0 flex-wrap flex-md-nowrap">

        <!-- Logo -->
        <a class="navbar-brand me-auto p-0" href="<?= BASE_URL ?>index.php">
            <img src="<?= BASE_URL ?>assets/img/logotipo.png" alt="Logo InfoBus" class="header-logo">
        </a>

        <!-- Links -->
        <ul class="navbar-nav header-links flex-row gap-3 gap-md-4 my-2 my-md-0 mx-auto mx-md-0">
            <li class="nav-item">
                <a class="nav-link text-body fw-medium px-0" href="<?= BASE_URL ?>index.php">
                    Inicio
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-body fw-medium px-0" href="<?= BASE_URL ?>sobre_nosotros.php">
                    Nosotros
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-body fw-medium px-0" href="<?= BASE_URL ?>paradas.php">
                    Paradas
                </a>
            </li>
        </ul>

        <!-- Acciones derechas: Modo Oscuro + Usuario -->
        <div class="d-flex align-items-center ms-auto ms-md-0 header-user gap-3">
            <!-- Botón Modo Oscuro / Claro -->
            <button type="button" id="btnToggleTheme" class="btn btn-link text-body fs-5 p-0 text-decoration-none" title="Cambiar modo de vista">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </button>

            <!-- Ícono Usuario -->
            <a href="<?= BASE_URL ?>auth/login.php" class="text-body fs-5" title="Iniciar sesión">
                <i class="fa-solid fa-user"></i>
            </a>
        </div>

    </div>
</nav>

<script>
    // Script inmediato para manejar el cambio de tema en la navbar y todo el sitio
    document.addEventListener("DOMContentLoaded", () => {
        const btnToggle = document.getElementById("btnToggleTheme");
        const themeIcon = document.getElementById("themeIcon");
        const htmlElement = document.documentElement;

        // Cargar preferencia guardada o usar la del sistema
        const savedTheme = localStorage.getItem("theme") || "light";
        htmlElement.setAttribute("data-bs-theme", savedTheme);
        actualizarIcono(savedTheme);

        btnToggle.addEventListener("click", () => {
            const currentTheme = htmlElement.getAttribute("data-bs-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";

            htmlElement.setAttribute("data-bs-theme", newTheme);
            localStorage.setItem("theme", newTheme);
            actualizarIcono(newTheme);
        });

        function actualizarIcono(theme) {
            if (theme === "dark") {
                themeIcon.classList.replace("fa-moon", "fa-sun");
            } else {
                themeIcon.classList.replace("fa-sun", "fa-moon");
            }
        }
    });
</script>