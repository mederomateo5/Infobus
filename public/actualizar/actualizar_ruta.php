<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vrutas.php');
    exit();
}

$omnibus_id = filter_input(INPUT_POST, 'omnibus_id', FILTER_VALIDATE_INT);
$parada_id  = filter_input(INPUT_POST, 'parada_id', FILTER_VALIDATE_INT);
$nuevo_orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);

if (!$omnibus_id || !$parada_id || !$nuevo_orden) {
    header('Location: actualizar_ruta_gui.php?omnibus_id=' . $omnibus_id . '&parada_id=' . $parada_id . '&msg=' . urlencode('Datos obligatorios faltantes.'));
    exit();
}

try {
    // Verificar si el nuevo orden entra en colisión con otro registro distinto del mismo ómnibus
    $chk = $pdo->prepare("SELECT orden FROM recorre WHERE omnibus_id = ? AND orden = ? AND parada_id != ?");
    $chk->execute([$omnibus_id, $nuevo_orden, $parada_id]);
    if ($chk->fetch()) {
        header('Location: actualizar_ruta_gui.php?omnibus_id=' . $omnibus_id . '&parada_id=' . $parada_id . '&msg=' . urlencode('Ya existe otra parada asignada a esa posición de secuencia.'));
        exit();
    }

    $update = $pdo->prepare("UPDATE recorre SET orden = ? WHERE omnibus_id = ? AND parada_id = ?");
    $update->execute([$nuevo_orden, $omnibus_id, $parada_id]);

    header('Location: ../vrutas.php?msg=' . urlencode('Recorrido actualizado con éxito.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_ruta_gui.php?omnibus_id=' . $omnibus_id . '&parada_id=' . $parada_id . '&msg=' . urlencode('Error al actualizar el recorrido.'));
    exit();
}
?>