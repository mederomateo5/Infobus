<?php
require_once '../../private/config/config.php';
    session_start();

    // Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // Redirigir según el tipo de usuario
    switch ($_SESSION['user_tipo']) {
        case 'compania':
            header('Location: ../paneles/panel_compania.php');
            exit();

        case 'administrador':
            header('Location: ../paneles/panel_admin.php');
            exit();

        case 'usuario':
            header('Location: ../paneles/panel_usuario.php');
            exit();

        default:
            // Tipo desconocido, por las dudas lo mandamos al login
            header('Location: login.php');
            exit();
    }
?>