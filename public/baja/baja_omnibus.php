<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../vomnibus.php');
    exit();
}

$id = $_POST['id'] ?? null;

if (!$id || !ctype_digit((string)$id)) {
    header('Location: ../vomnibus.php?msg=' . urlencode('Ómnibus inválido.'));
    exit();
}

try {
    $eliminar = $pdo->prepare("DELETE FROM omnibus WHERE id = ?");
    $eliminar->execute([$id]);

    header('Location: ../vomnibus.php?msg=' . urlencode('Ómnibus eliminado correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: ../vomnibus.php?msg=' . urlencode('No se puede eliminar el ómnibus porque tiene viajes u otros registros asociados.'));
    exit();
}
?>