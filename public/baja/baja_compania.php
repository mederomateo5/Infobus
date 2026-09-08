<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vcompania.php');
    exit();
}

$id = $_POST['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../vcompania.php?msg=' . urlencode('Compañía inválida.'));
    exit();
}

try {
    $eliminar = $pdo->prepare("DELETE FROM compania WHERE id = ?");
    $eliminar->execute([$id]);

    header('Location: ../vcompania.php?msg=' . urlencode('Compañía eliminada correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../vcompania.php?msg=' . urlencode('No se puede eliminar la compañía porque tiene ómnibus u otros registros asociados.'));
    exit();
}
?>