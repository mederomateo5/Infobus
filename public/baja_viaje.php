<?php
    session_start();

    // Verificar sesión y rol de administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    // Conexión a la base de datos
    require_once '../config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../public/panel_admin.php');
        exit();
    }

    $id = $_POST['id'] ?? null;

    // Validar que el ID sea un número entero positivo
    if (!$id || !ctype_digit((string)$id)) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Viaje inválido.'));
        exit();
    }

    try {
        // Eliminamos el viaje de la tabla 'viajes'
        $eliminar = $pdo->prepare("DELETE FROM viajes WHERE id = ?");
        $eliminar->execute([$id]);

        header('Location: ../public/panel_admin.php?msg=' . urlencode('Viaje eliminado correctamente.'));
        exit();

    } catch (PDOException $e) {
        // En caso de error, redirigimos con un mensaje de error
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Error al eliminar el viaje de la base de datos.'));
        exit();
    }
?>