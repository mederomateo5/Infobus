<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gestion/ver_companias.php');
    exit();
}

$id       = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nombre   = trim($_POST['nombre'] ?? '');
$email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

if (!$id || !$nombre || !$email || !$telefono) {
    header('Location: actualizar_compania_gui.php?id=' . $id . '&msg=' . urlencode('Datos obligatorios incompletos.'));
    exit();
}

try {
    // Verificar que no se duplique el email en otra compañía
    $chk = $pdo->prepare("SELECT id FROM compania WHERE email = ? AND id != ?");
    $chk->execute([$email, $id]);
    if ($chk->fetch()) {
        header('Location: actualizar_compania_gui.php?id=' . $id . '&msg=' . urlencode('Otra compañía ya posee registrado ese email.'));
        exit();
    }

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE compania SET nombre = ?, email = ?, telefono = ?, password = ? WHERE id = ?");
        $update->execute([$nombre, $email, $telefono, $hash, $id]);
    } else {
        $update = $pdo->prepare("UPDATE compania SET nombre = ?, email = ?, telefono = ? WHERE id = ?");
        $update->execute([$nombre, $email, $telefono, $id]);
    }

    header('Location: ../gestion/ver_companias.php?msg=' . urlencode('Compañía actualizada con éxito.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_compania_gui.php?id=' . $id . '&msg=' . urlencode('Error al actualizar la compañía.'));
    exit();
}
?>