<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión de usuario
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: actualizar_contrasenia_gui.php');
    exit();
}

$userId            = $_SESSION['user_id'];
$passwordActual    = $_POST['password_actual']    ?? '';
$nuevoPassword     = $_POST['nuevo_password']     ?? '';
$confirmarPassword = $_POST['confirmar_password'] ?? '';

// 1. Validar que los campos no estén vacíos
if ($passwordActual === '' || $nuevoPassword === '' || $confirmarPassword === '') {
    header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('Todos los campos son obligatorios.'));
    exit();
}

// 2. Validar que las nuevas contraseñas coincidan
if ($nuevoPassword !== $confirmarPassword) {
    header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('Las nuevas contraseñas no coinciden.'));
    exit();
}

// 3. Validar las condiciones de seguridad de la contraseña
$errors = [];
if (strlen($nuevoPassword) < 8) {
    $errors[] = 'Mínimo 8 caracteres.';
}
if (!preg_match('/[A-Z]/', $nuevoPassword)) {
    $errors[] = 'Debe contener al menos una mayúscula.';
}
if (!preg_match('/[!@#$%^&*(),.?":{}|<>_\-\[\]\/\\\\]/', $nuevoPassword)) {
    $errors[] = 'Debe contener al menos un símbolo especial.';
}

if (!empty($errors)) {
    header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode(implode(' ', $errors)));
    exit();
}

try {
    // 4. Obtener la contraseña actual en la base de datos para verificarla
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('Usuario no encontrado.'));
        exit();
    }

    // 5. Verificar que la contraseña actual ingresada sea correcta
    if (!password_verify($passwordActual, $user['password'])) {
        header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('La contraseña actual es incorrecta.'));
        exit();
    }

    // 6. Validar que la nueva contraseña sea distinta a la anterior
    if (password_verify($nuevoPassword, $user['password'])) {
        header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('La nueva contraseña no puede ser igual a la actual.'));
        exit();
    }

    // 7. Generar el nuevo hash y actualizar en la base de datos
    $nuevoPasswordHash = password_hash($nuevoPassword, PASSWORD_DEFAULT);
    
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$nuevoPasswordHash, $userId]);

    // Redirigir con éxito de vuelta al GUI
    header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('Contraseña actualizada correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_contrasenia_gui.php?msg=' . urlencode('Error al actualizar la contraseña.'));
    exit();
}