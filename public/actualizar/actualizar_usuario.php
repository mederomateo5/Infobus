<?php
require_once '../../config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../panel_admin.php');
    exit();
}

$id       = $_POST['id']       ?? null;
$nombre   = trim($_POST['nombre']   ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password']      ?? '';
$tipo     = $_POST['tipo']          ?? '';

$tiposValidos = ['usuario', 'compania', 'administrador'];

if (!$id || $nombre === '' || $apellido === '' || $email === '' || $telefono === '' || !in_array($tipo, $tiposValidos)) {
    header('Location: actualizar_usuario_gui.php?id=' . $id . '&msg=' . urlencode('Faltan datos obligatorios.'));
    exit();
}

try {
    // Validar que el email no pertenezca a OTRO usuario
    $verificar = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $verificar->execute([$email, $id]);

    if ($verificar->fetch()) {
        header('Location: actualizar_usuario_gui.php?id=' . $id . '&msg=' . urlencode('El email ya pertenece a otro usuario.'));
        exit();
    }

    // Actualizar datos de usuario
    if (!empty($password)) {
        if (strlen($password) < 6) {
            header('Location: actualizar_usuario_gui.php?id=' . $id . '&msg=' . urlencode('La contraseña debe tener al menos 6 caracteres.'));
            exit();
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET nombre = ?, apellido = ?, email = ?, telefono = ?, tipo = ?, password = ? WHERE id = ?");
        $update->execute([$nombre, $apellido, $email, $telefono, $tipo, $passwordHash, $id]);
    } else {
        // Mantener la contraseña previa
        $update = $pdo->prepare("UPDATE users SET nombre = ?, apellido = ?, email = ?, telefono = ?, tipo = ? WHERE id = ?");
        $update->execute([$nombre, $apellido, $email, $telefono, $tipo, $id]);
    }

    header('Location: ../usuarios.php?msg=' . urlencode('Usuario actualizado correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_usuario_gui.php?id=' . $id . '&msg=' . urlencode('Error al actualizar el usuario.'));
    exit();
}