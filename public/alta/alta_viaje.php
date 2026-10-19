<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['compania', 'administrador'])) {
    die("Acceso denegado. Esta página es exclusiva para compañías o administradores.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: alta_viaje_gui.php');
    exit();
}

$n_omnibus     = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
$agencia       = $_POST['agencia'] ?? '';
$origen_id     = filter_input(INPUT_POST, 'origen_id', FILTER_VALIDATE_INT);
$destino_id    = filter_input(INPUT_POST, 'destino_id', FILTER_VALIDATE_INT);
$asientos_disp = filter_input(INPUT_POST, 'asientos_disponibles', FILTER_VALIDATE_INT);
$precio        = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$hora          = $_POST['hora'] ?? '';
$hora_llegada  = $_POST['hora_llegada'] ?? '';
$es_directo    = isset($_POST['es_directo']) ? 1 : 0;
$dias          = $_POST['dias'] ?? [];

$agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

if (
    !$n_omnibus || !$origen_id || !$destino_id || $asientos_disp === false ||
    $precio === false || !in_array($agencia, $agenciasValidas) ||
    $hora === '' || $hora_llegada === '' || empty($dias)
) {
    header('Location: alta_viaje_gui.php?msg=' . urlencode('Faltan datos, días o se ingresaron valores inválidos.'));
    exit();
}

try {
    $pdo->beginTransaction();

    $stmtOmnibus = $pdo->prepare(
        "SELECT o.id, o.asientos FROM omnibus o
         JOIN compania c ON o.compania_id = c.id
         WHERE o.n_omnibus = ? AND c.nombre = ?"
    );
    $stmtOmnibus->execute([$n_omnibus, $agencia]);
    $omnibus = $stmtOmnibus->fetch();

    if (!$omnibus) {
        $pdo->rollBack();
        header('Location: alta_viaje_gui.php?msg=' . urlencode('No se encontró el ómnibus con el número y agencia especificados.'));
        exit();
    }

    if ($asientos_disp > $omnibus['asientos']) {
        $pdo->rollBack();
        header('Location: alta_viaje_gui.php?msg=' . urlencode('La cantidad de asientos disponibles no puede ser mayor a la cantidad de asientos.'));
        exit();
    }

    $omnibusId = (int)$omnibus['id'];
    $yaSalio = 0;

    $insertar = $pdo->prepare(
        "INSERT INTO viajes (omnibus_id, origen_id, destino_id, precio, ya_salio, hora, hora_llegada, es_directo, asientos_disponibles)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    $insertar->execute([
        $omnibusId,
        $origen_id,
        $destino_id,
        $precio,
        $yaSalio,
        $hora,
        $hora_llegada,
        $es_directo,
        $asientos_disp
    ]);

    $viaje_id = $pdo->lastInsertId();

    $insertDia = $pdo->prepare("INSERT INTO viaje_dia (viaje_id, dia_id) VALUES (?, ?)");
    foreach ($dias as $dia_id) {
        if (ctype_digit((string)$dia_id)) {
            $insertDia->execute([$viaje_id, (int)$dia_id]);
        }
    }

    $pdo->commit();
    header('Location: alta_viaje_gui.php?msg=' . urlencode('Viaje registrado correctamente.'));
    exit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: alta_viaje_gui.php?msg=' . urlencode('Error en la base de datos al dar de alta el viaje.'));
    exit();
}
?>