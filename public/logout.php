<?php
    // Iniciar sesión y destruirla
    session_start();
    session_destroy();
    header('Location: login.php');
    exit();
?>
