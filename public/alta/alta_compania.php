<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: alta_compania_gui.php');
    exit();
}

$nombre   = trim($_POST['nombre'] ?? '');
$email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password'] ?? '';

if (!$nombre || !$email || !$telefono || !$password) {
    header('Location: alta_compania_gui.php?msg=' . urlencode('Faltan datos obligatorios o son inválidos.'));
    exit();
}

try {
    // Validar restricción UNIQUE (email)
    $chk = $pdo->prepare("SELECT id FROM compania WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetch()) {
        header('Location: alta_compania_gui.php?msg=' . urlencode('Ya existe una compañía registrada con ese email.'));
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO compania (nombre, email, telefono, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $telefono, $hash]);

    header('Location: alta_compania_gui.php?msg=' . urlencode('Compañía registrada correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: alta_compania_gui.php?msg=' . urlencode('Error al registrar la compañía.'));
    exit();
}
?>