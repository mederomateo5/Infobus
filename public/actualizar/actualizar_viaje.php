<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gestion/ver_viajes.php');
    exit();
}

$id                   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$n_omnibus            = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
$agencia              = $_POST['agencia'] ?? '';
$origen_id            = filter_input(INPUT_POST, 'origen_id', FILTER_VALIDATE_INT);
$destino_id           = filter_input(INPUT_POST, 'destino_id', FILTER_VALIDATE_INT);
$asientos_disponibles = filter_input(INPUT_POST, 'asientos_disponibles', FILTER_VALIDATE_INT);
$precio               = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$hora                 = $_POST['hora'] ?? '';
$hora_llegada         = $_POST['hora_llegada'] ?? '';
$es_directo           = isset($_POST['es_directo']) ? 1 : 0;
$ya_salio             = isset($_POST['ya_salio']) ? 1 : 0;
$dias                 = $_POST['dias'] ?? [];

$agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

if (
    !$id || !$n_omnibus || !$origen_id || !$destino_id || $asientos_disponibles === false ||
    $precio === false || !in_array($agencia, $agenciasValidas) ||
    $hora === '' || $hora_llegada === '' || empty($dias)
) {
    header('Location: actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('Faltan datos obligatorios, días o se ingresaron valores inválidos.'));
    exit();
}

if ($origen_id === $destino_id) {
    header('Location: actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('El origen y el destino deben ser paradas diferentes.'));
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
        header('Location: actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('No se encontró el ómnibus con el número y agencia especificados.'));
        exit();
    }

    if ($asientos_disponibles > $omnibus['asientos']) {
        $pdo->rollBack();
        header('Location: actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('La cantidad de asientos disponibles no puede ser mayor a la cantidad de asientos.'));
        exit();
    }

    $omnibusId = (int)$omnibus['id'];

    $update = $pdo->prepare(
        "UPDATE viajes 
         SET omnibus_id = ?, origen_id = ?, destino_id = ?, precio = ?, ya_salio = ?, hora = ?, hora_llegada = ?, es_directo = ?, asientos_disponibles = ?
         WHERE id = ?"
    );
    $update->execute([
        $omnibusId,
        $origen_id,
        $destino_id,
        $precio,
        $ya_salio,
        $hora,
        $hora_llegada,
        $es_directo,
        $asientos_disponibles,
        $id
    ]);

    // Actualizar días de la semana
    $deleteDias = $pdo->prepare("DELETE FROM viaje_dia WHERE viaje_id = ?");
    $deleteDias->execute([$id]);

    $insertDia = $pdo->prepare("INSERT INTO viaje_dia (viaje_id, dia_id) VALUES (?, ?)");
    foreach ($dias as $dia_id) {
        if (ctype_digit((string)$dia_id)) {
            $insertDia->execute([$id, (int)$dia_id]);
        }
    }

    $pdo->commit();
    header('Location: ../gestion/ver_viajes.php?msg=' . urlencode('Viaje actualizado correctamente.'));
    exit();

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: actualizar_viaje_gui.php?id=' . $id . '&msg=' . urlencode('Error al actualizar el viaje en la base de datos.'));
    exit();
}
?>