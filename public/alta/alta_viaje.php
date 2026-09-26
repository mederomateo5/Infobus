<?php
require_once '../../config/config.php';
session_start();

// Verificar sesión y rol de administrador
if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['compania', 'administrador'])) {
    die("Acceso denegado. Esta página es exclusiva para compañías o administradores.");
}

require_once '../../config/database.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../alta/alta_viaje_gui.php');
    exit();
}

// Recibir y sanitizar datos
$n_omnibus    = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
$agencia      = $_POST['agencia'] ?? '';
$origen_id    = filter_input(INPUT_POST, 'origen_id', FILTER_VALIDATE_INT);
$destino_id   = filter_input(INPUT_POST, 'destino_id', FILTER_VALIDATE_INT);
$asientos_disp = filter_input(INPUT_POST, 'asientos_disponibles', FILTER_VALIDATE_INT);
$precio       = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$fecha        = $_POST['fecha'] ?? '';
$hora         = $_POST['hora'] ?? '';
$hora_llegada = $_POST['hora_llegada'] ?? '';
$es_directo   = isset($_POST['es_directo']) ? 1 : 0;

$agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

// Validaciones principales
if (
    !$n_omnibus || !$origen_id || !$destino_id || !$asientos_disp ||
    $precio === false || !in_array($agencia, $agenciasValidas) ||
    $fecha === '' || $hora === '' || $hora_llegada === ''
) {
    header('Location: ../alta/alta_viaje_gui.php?msg=' . urlencode('Faltan datos o se ingresaron valores inválidos.'));
    exit();
}

try {
    // Obtener el ID del ómnibus
    $stmtOmnibus = $pdo->prepare(
        "SELECT o.id, o.asientos FROM omnibus o
         JOIN compania c ON o.compania_id = c.id
         WHERE o.n_omnibus = ? AND c.nombre = ?"
    );
    $stmtOmnibus->execute([$n_omnibus, $agencia]);
    $omnibus = $stmtOmnibus->fetch();

    if (!$omnibus) {
        header('Location: ../alta/alta_viaje_gui.php?msg=' . urlencode('No se encontró el ómnibus con el número y agencia especificados.'));
        exit();
    }

    if ($asientos_disp > $omnibus['asientos']) {
    header('Location: ../alta/alta_viaje_gui.php?msg=' . urlencode('La cantidad de asientos disponibles no puede ser mayor a la cantidad de asientos.'));
    exit();
}

    $omnibusId = (int)$omnibus['id'];
    $yaSalio = 0; // Por defecto FALSE según esquema

    // Insertar el viaje en la base de datos
    $insertar = $pdo->prepare(
        "INSERT INTO viajes (omnibus_id, origen_id, destino_id, precio, ya_salio, fecha, hora, hora_llegada, es_directo, asientos_disponibles)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    $exito = $insertar->execute([
        $omnibusId,
        $origen_id,
        $destino_id,
        $precio,
        $yaSalio,
        $fecha,
        $hora,
        $hora_llegada,
        $es_directo,
        $asientos_disp
    ]);

    if ($exito) {
        header('Location: ../alta/alta_viaje_gui.php?msg=' . urlencode('Viaje registrado correctamente.'));
    } else {
        header('Location: ../alta/alta_viaje_gui.php?msg=' . urlencode('Error al registrar el viaje.'));
    }
    exit();

} catch (PDOException $e) {
    header('Location: ../alta/alta_viaje_gui.php?msg=' . urlencode('Error en la base de datos al dar de alta el viaje.'));
    exit();
}