<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../alta/alta_ruta_gui.php');
    exit();
}

$omnibus_id = filter_input(INPUT_POST, 'omnibus_id', FILTER_VALIDATE_INT);
$parada_id  = filter_input(INPUT_POST, 'parada_id', FILTER_VALIDATE_INT);
$orden      = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);

if (!$omnibus_id || !$parada_id || !$orden) {
    header('Location: ../alta/alta_ruta_gui.php?msg=' . urlencode('Todos los campos son obligatorios.'));
    exit();
}

try {
    // Validar UNIQUE (omnibus_id, orden)
    $chkOrden = $pdo->prepare("SELECT omnibus_id FROM recorre WHERE omnibus_id = ? AND orden = ?");
    $chkOrden->execute([$omnibus_id, $orden]);
    if ($chkOrden->fetch()) {
        header('Location: ../alta/alta_ruta_gui.php?msg=' . urlencode('Ese ómnibus ya tiene una parada asignada en esa posición/orden.'));
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO recorre (omnibus_id, parada_id, orden) VALUES (?, ?, ?)");
    $stmt->execute([$omnibus_id, $parada_id, $orden]);

    header('Location: ../alta/alta_ruta_gui.php?msg=' . urlencode('Parada añadida al recorrido correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../alta/alta_ruta_gui.php?msg=' . urlencode('Error: La parada ya existe en este recorrido.'));
    exit();
}
?>