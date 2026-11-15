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
    header('Location: actualizar_telefono_gui.php');
    exit();
}

$userId         = $_SESSION['user_id'];
$nuevoTelefono  = trim($_POST['nuevo_telefono'] ?? '');
$passwordActual = $_POST['password_actual'] ?? '';

// 1. Validar que los campos no estén vacíos
if ($nuevoTelefono === '' || $passwordActual === '') {
    header('Location: actualizar_telefono_gui.php?msg=' . urlencode('Todos los campos son obligatorios.'));
    exit();
}

// Validar teléfono: solo números, entre 7 y 15 dígitos
if (!preg_match('/^[0-9]{7,15}$/', $nuevoTelefono)) {
    header('Location: actualizar_telefono_gui.php?msg=' . urlencode('El teléfono debe tener entre 7 y 15 dígitos y contener solo números.'));
    exit();
}

try {
    // 2. Obtener la contraseña y el teléfono actual del usuario en la base de datos
    $stmt = $pdo->prepare("SELECT password, telefono FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: actualizar_telefono_gui.php?msg=' . urlencode('Usuario no encontrado.'));
        exit();
    }

    // Verificar si el teléfono es exactamente el mismo que ya tiene
    if ($user['telefono'] === $nuevoTelefono) {
        header('Location: actualizar_telefono_gui.php?msg=' . urlencode('El nuevo teléfono es igual al actual.'));
        exit();
    }

    // 3. Verificar que la contraseña actual ingresada sea correcta
    if (!password_verify($passwordActual, $user['password'])) {
        header('Location: actualizar_telefono_gui.php?msg=' . urlencode('La contraseña actual es incorrecta.'));
        exit();
    }

    // 4. Actualizar el teléfono en la base de datos
    $update = $pdo->prepare("UPDATE users SET telefono = ? WHERE id = ?");
    $update->execute([$nuevoTelefono, $userId]);

    // Redirigir con éxito de vuelta al GUI
    header('Location: actualizar_telefono_gui.php?msg=' . urlencode('Teléfono actualizado correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_telefono_gui.php?msg=' . urlencode('Error al actualizar el teléfono.'));
    exit();
}