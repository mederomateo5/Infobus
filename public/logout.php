<?php
require_once '../config/config.php';
    // Iniciar sesión y destruirla
    session_start();
    session_destroy();
    header('Location: ../public/login.php');
    exit();
?>
