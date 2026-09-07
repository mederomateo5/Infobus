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
    header('Location: ../public/viajes.php');
    exit();
}

// Recibir y sanitizar datos del formulario
$id                   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$n_omnibus            = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
$agencia              = $_POST['agencia'] ?? '';
$origen_id            = filter_input(INPUT_POST, 'origen_id', FILTER_VALIDATE_INT);
$destino_id           = filter_input(INPUT_POST, 'destino_id', FILTER_VALIDATE_INT);
$asientos_disponibles = filter_input(INPUT_POST, 'asientos_disponibles', FILTER_VALIDATE_INT);
$precio               = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$fecha                = $_POST['fecha'] ?? '';
$hora                 = $_POST['hora'] ?? '';
$hora_llegada         = $_POST['hora_llegada'] ?? '';
$es_directo           = isset($_POST['es_directo']) ? 1 : 0;
$ya_salio             = isset($_POST['ya_salio']) ? 1 : 0;

$agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

// Validaciones principales
if (
    !$id || !$n_omnibus || !$origen_id || !$destino_id || !$asientos_disponibles ||
    $precio === false || !in_array($agencia, $agenciasValidas) ||
    $fecha === '' || $hora === '' || $hora_llegada === ''
) {
    header('Location: ../public/actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('Faltan datos obligatorios o se ingresaron valores inválidos.'));
    exit();
}

if ($origen_id === $destino_id) {
    header('Location: ../public/actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('El origen y el destino deben ser paradas diferentes.'));
    exit();
}

try {
    // Obtener el ID del ómnibus correspondiente a la agencia y número
    $stmtOmnibus = $pdo->prepare(
        "SELECT o.id FROM omnibus o
         JOIN compania c ON o.compania_id = c.id
         WHERE o.n_omnibus = ? AND c.nombre = ?"
    );
    $stmtOmnibus->execute([$n_omnibus, $agencia]);
    $omnibus = $stmtOmnibus->fetch();

    if (!$omnibus) {
        header('Location: ../public/actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('No se encontró el ómnibus con el número y agencia especificados.'));
        exit();
    }

    $omnibusId = (int)$omnibus['id'];

    // Actualizar el viaje
    $update = $pdo->prepare(
        "UPDATE viajes 
         SET omnibus_id = ?, origen_id = ?, destino_id = ?, precio = ?, ya_salio = ?, fecha = ?, hora = ?, hora_llegada = ?, es_directo = ?, asientos_disponibles = ?
         WHERE id = ?"
    );
    $update->execute([
        $omnibusId,
        $origen_id,
        $destino_id,
        $precio,
        $ya_salio,
        $fecha,
        $hora,
        $hora_llegada,
        $es_directo,
        $asientos_disponibles,
        $id
    ]);

    header('Location: ../public/viajes.php?msg=' . urlencode('Viaje actualizado correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../public/actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('Error al actualizar el viaje en la base de datos.'));
    exit();
}
?>