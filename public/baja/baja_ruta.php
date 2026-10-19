<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gestion/ver_rutas.php');
    exit();
}

$omnibus_id = $_POST['omnibus_id'] ?? null;
$parada_id  = $_POST['parada_id'] ?? null;

if (!$omnibus_id || !$parada_id) {
    header('Location: ../gestion/ver_rutas.php?msg=' . urlencode('Identificadores no válidos.'));
    exit();
}

try {
    $eliminar = $pdo->prepare("DELETE FROM recorre WHERE omnibus_id = ? AND parada_id = ?");
    $eliminar->execute([$omnibus_id, $parada_id]);

    header('Location: ../gestion/ver_rutas.php?msg=' . urlencode('Parada eliminada del recorrido.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../gestion/ver_rutas.php?msg=' . urlencode('Error al eliminar el registro del recorrido.'));
    exit();
}
?>