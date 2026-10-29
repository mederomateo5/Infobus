<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || ($_SESSION['user_tipo'] !== 'administrador' && $_SESSION['user_tipo'] !== 'compania')) {
        die("Acceso denegado.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gestion/ver_companias.php');
    exit();
}

$id = $_POST['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../gestion/ver_companias.php?msg=' . urlencode('Compañía inválida.'));
    exit();
}

try {
    $eliminar = $pdo->prepare("DELETE FROM compania WHERE id = ?");
    $eliminar->execute([$id]);

    header('Location: ../gestion/ver_companias.php?msg=' . urlencode('Compañía eliminada correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../gestion/ver_companias.php?msg=' . urlencode('No se puede eliminar la compañía porque tiene ómnibus u otros registros asociados.'));
    exit();
}
?>