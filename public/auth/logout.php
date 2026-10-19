<?php
require_once '../../private/config/config.php';
    // Iniciar sesión y destruirla
    session_start();
    session_destroy();
    header('Location: login.php');
    exit();
?>
