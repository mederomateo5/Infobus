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
    header('Location: actualizar_email.php');
    exit();
}

$userId        = $_SESSION['user_id'];
$nuevoEmail    = trim($_POST['nuevo_email'] ?? '');
$passwordActual = $_POST['password_actual'] ?? '';

// Validar que los campos no estén vacíos
if ($nuevoEmail === '' || $passwordActual === '') {
    header('Location: actualizar_email_gui.php?msg=' . urlencode('Todos los campos son obligatorios.'));
    exit();
}

// Validar formato de email
if (!filter_var($nuevoEmail, FILTER_VALIDATE_EMAIL)) {
    header('Location: actualizar_email_gui.php?msg=' . urlencode('El formato del correo electrónico no es válido.'));
    exit();
}

try {
    // 1. Obtener la contraseña hasheada actual del usuario en la base de datos
    $stmt = $pdo->prepare("SELECT password, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: actualizar_email_gui.php?msg=' . urlencode('Usuario no encontrado.'));
        exit();
    }

    // Verificar si el email es exactamente el mismo que ya tiene
    if ($user['email'] === $nuevoEmail) {
        header('Location: actualizar_email_gui.php?msg=' . urlencode('El nuevo correo es igual al actual.'));
        exit();
    }

    // 2. Verificar que la contraseña actual ingresada sea correcta
    if (!password_verify($passwordActual, $user['password'])) {
        header('Location: actualizar_email_gui.php?msg=' . urlencode('La contraseña actual es incorrecta.'));
        exit();
    }

    // 3. Validar que el nuevo email NO pertenezca a OTRO usuario
    $verificar = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $verificar->execute([$nuevoEmail, $userId]);

    if ($verificar->fetch()) {
        header('Location: actualizar_email_gui.php?msg=' . urlencode('El correo electrónico ya está registrado por otro usuario.'));
        exit();
    }

    // 4. Actualizar el email en la base de datos
    $update = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $update->execute([$nuevoEmail, $userId]);

    // Redirigir con éxito
    header('Location: actualizar_email_gui.php?msg=' . urlencode('Correo electrónico actualizado correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_email_gui.php?msg=' . urlencode('Error al actualizar el correo electrónico.'));
    exit();
}