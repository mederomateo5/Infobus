<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y que sea administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado.");
}

require_once '../../private/config/database.php';
require_once '../../private/src/User.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: alta_usuario_gui.php');
    exit();
}

// Recibir datos del formulario
$nombre   = trim($_POST['nombre']   ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$password = $_POST['password']      ?? '';
$tipo     = $_POST['tipo']          ?? '';

// Instanciar clase User para reutilizar validaciones centralizadas
$userObj = new User($pdo);

// Validaciones de tipos permitidos
$tiposValidos = ['usuario', 'compania', 'administrador'];

if ($nombre === '' || $apellido === '' || $email === '' || $telefono === '' || $password === '' || !in_array($tipo, $tiposValidos)) {
    header('Location: alta_usuario_gui.php?msg=' . urlencode('Faltan datos obligatorios o el tipo de usuario no es válido.'));
    exit();
}

// Validar formato de Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: alta_usuario_gui.php?msg=' . urlencode('El email no tiene un formato válido.'));
    exit();
}

// Validar formato de Teléfono
if (!preg_match('/^\+?[0-9]{7,15}$/', $telefono)) {
    header('Location: alta_usuario_gui.php?msg=' . urlencode('El teléfono debe tener entre 7 y 15 dígitos.'));
    exit();
}

// Validar contraseña centralizada (mínimo 8 caracteres, mayúscula y símbolo especial)
$passErrors = $userObj->validatePassword($password);
if (!empty($passErrors)) {
    header('Location: alta_usuario_gui.php?msg=' . urlencode('Contraseña débil: ' . implode(' ', $passErrors)));
    exit();
}

try {
    // Verificar que el email no exista ya
    $verificar = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $verificar->execute([strtolower($email)]);

    if ($verificar->fetch()) {
        header('Location: alta_usuario_gui.php?msg=' . urlencode('Ya existe un usuario con ese email.'));
        exit();
    }

    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario nuevo
    $insertar = $pdo->prepare(
        "INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $insertar->execute([
        htmlspecialchars($nombre),
        htmlspecialchars($apellido),
        strtolower($email),
        $telefono,
        $passwordHash,
        $tipo
    ]);

    // Redirigir al panel de administrador con un mensaje de éxito
    header('Location: alta_usuario_gui.php?msg=' . urlencode('Usuario dado de alta correctamente.'));
    exit();

} catch (PDOException $e) {
    error_log("Error en alta_usuario.php: " . $e->getMessage());
    header('Location: alta_usuario_gui.php?msg=' . urlencode('Error al dar de alta el usuario.'));
    exit();
}
?>