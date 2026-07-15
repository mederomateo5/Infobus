<?php
    session_start();

    // Verificar sesión y que sea administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../public/panel_admin.php');
        exit();
    }

    // Recibir datos del formulario
    $nombre   = trim($_POST['nombre']   ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password']      ?? '';
    $tipo     = $_POST['tipo']          ?? '';

    // Validaciones básicas
    $tiposValidos = ['usuario', 'chofer', 'administrador'];

    // Validar que no falten datos obligatorios y que el tipo de usuario sea válido
    if ($nombre === '' || $apellido === '' || $email === '' || $telefono === '' || $password === '' || !in_array($tipo, $tiposValidos)) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Faltan datos o el tipo de usuario no es válido.'));
        exit();
    }

    // Validar que la contraseña tenga al menos 6 caracteres
    if (strlen($password) < 6) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('La contraseña debe tener al menos 6 caracteres.'));
        exit();
    }

    try {
        // Verificar que el email no exista ya
        $verificar = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $verificar->execute([$email]);

        if ($verificar->fetch()) {
            header('Location: ../public/panel_admin.php?msg=' . urlencode('Ya existe un usuario con ese email.'));
            exit();
        }

        // Encriptar contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario nuevo
        $insertar = $pdo->prepare(
            "INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $insertar->execute([$nombre, $apellido, $email, $telefono, $passwordHash, $tipo]);

        // Redirigir al panel de administrador con un mensaje de éxito
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Usuario dado de alta correctamente.'));
        exit();

    } catch (PDOException $e) {
        // En caso de error, redirigimos con un mensaje de error
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Error al dar de alta el usuario.'));
        exit();
    }
?>