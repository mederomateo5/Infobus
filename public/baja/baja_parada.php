<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

// Solo aceptar peticiones por método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gestion/ver_paradas.php');
    exit();
}

$id = $_POST['id'] ?? null;

// Validar que el ID sea numérico
if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../gestion/ver_paradas.php?msg=' . urlencode('Parada inválida.'));
    exit();
}

try {
    // Eliminar la parada de la base de datos
    $eliminar = $pdo->prepare("DELETE FROM paradas WHERE id = ?");
    $eliminar->execute([$id]);

    header('Location: ../gestion/ver_paradas.php?msg=' . urlencode('Parada dada de baja correctamente.'));
    exit();

} catch (PDOException $e) {
    // Redirigir en caso de error de base de datos
    header('Location: ../gestion/ver_paradas.php?msg=' . urlencode('Error al dar de baja la parada.'));
    exit();
}
?>