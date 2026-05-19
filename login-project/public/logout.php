<?php
    // Iniciar sesión actual
    session_start();

    // Vaciar variables de sesión
    session_unset();

    // Destruir sesión
    session_destroy();

    // Redirigir al login
    header('Location: login.php');
    exit();
?>