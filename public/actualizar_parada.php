<?php
require_once '../config/config.php';
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../config/database.php';

// Solo aceptar peticiones por método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/vparadas.php');
    exit();
}

// Recibir datos del formulario
$id       = $_POST['id']       ?? null;
$nombre   = trim($_POST['nombre']   ?? '');
$latitud  = trim($_POST['latitud']  ?? '');
$longitud = trim($_POST['longitud'] ?? '');

// Validar que no falten datos obligatorios
if (!$id || $nombre === '' || $latitud === '' || $longitud === '') {
    header('Location: ../public/actualizar_parada_gui.php?id=' . $id . '&msg=' . urlencode('Faltan datos obligatorios.'));
    exit();
}

// Validar rangos válidos para coordenadas geográficas
if (!is_numeric($latitud) || !is_numeric($longitud) || $latitud < -90 || $latitud > 90 || $longitud < -180 || $longitud > 180) {
    header('Location: ../public/actualizar_parada_gui.php?id=' . $id . '&msg=' . urlencode('Las coordenadas ingresadas no son válidas.'));
    exit();
}

try {
    // Validar que el nombre no pertenezca a OTRA parada
    $verificar = $pdo->prepare("SELECT id FROM paradas WHERE nombre = ? AND id != ?");
    $verificar->execute([$nombre, $id]);

    if ($verificar->fetch()) {
        header('Location: ../public/actualizar_parada_gui.php?id=' . $id . '&msg=' . urlencode('Ya existe otra parada con ese nombre.'));
        exit();
    }

    // Actualizar los datos de la parada
    $update = $pdo->prepare("UPDATE paradas SET nombre = ?, latitud = ?, longitud = ? WHERE id = ?");
    $update->execute([$nombre, $latitud, $longitud, $id]);

    header('Location: ../public/vparadas.php?msg=' . urlencode('Parada actualizada correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../public/actualizar_parada_gui.php?id=' . $id . '&msg=' . urlencode('Error al actualizar la parada.'));
    exit();
}
?>