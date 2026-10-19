<?php
require_once '../../private/config/config.php';
    session_start();

    // Verificar sesión y que sea administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../../private/config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../gestion/ver_usuarios.php');
        exit();
    }

    $id = $_POST['id'] ?? null;

    if (!$id || !ctype_digit((string)$id)) {
        header('Location: ../gestion/ver_usuarios.php?msg=' . urlencode('Usuario inválido.'));
        exit();
    }

    // No permitir que el administrador se elimine a sí mismo
    if ((int)$id === (int)$_SESSION['user_id']) {
        header('Location: ../gestion/ver_usuarios.php?msg=' . urlencode('No podés darte de baja a vos mismo.'));
        exit();
    }

    try {
        // Eliminamos el usuario de la tabla 'users'
        $eliminar = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $eliminar->execute([$id]);

        header('Location: ../gestion/ver_usuarios.php?msg=' . urlencode('Usuario dado de baja correctamente.'));
        exit();

    } catch (PDOException $e) {
        // En caso de error, redirigimos con un mensaje de error
        header('Location: ../gestion/ver_usuarios.php?msg=' . urlencode('Error al dar de baja el usuario.'));
        exit();
    }  
?>