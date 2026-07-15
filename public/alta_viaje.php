<?php
    session_start();

    // Verificar sesión y rol de administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../public/panel_admin.php');
        exit();
    }

    // Recibir y sanitizar los datos del formulario de ómnibus
    $n_omnibus    = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
    $asientos     = filter_input(INPUT_POST, 'asientos', FILTER_VALIDATE_INT);
    $origen       = trim($_POST['origen'] ?? '');
    $destino      = trim($_POST['destino'] ?? '');
    $precio       = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $agencia      = $_POST['agencia'] ?? '';
    $fecha        = $_POST['fecha'] ?? '';
    $hora         = $_POST['hora'] ?? '';
    $hora_llegada = $_POST['hora_llegada'] ?? '';

    // Validar opciones permitidas de agencia según el ENUM de tu base de datos
    $agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

    // Validar que no falten datos obligatorios
    if (
        !$n_omnibus || !$asientos || $origen === '' || $destino === '' || 
        $precio === false || !in_array($agencia, $agenciasValidas) || 
        $fecha === '' || $hora === '' || $hora_llegada === ''
    ) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Faltan datos o se ingresaron valores inválidos.'));
        exit();
    }

    try {
        // Al ser un viaje nuevo:
        // - 'asientos_disponibles' arranca siendo igual a la cantidad total de 'asientos'.
        // - 'ya_salio' arranca en 0 (FALSE).
        $asientos_disponibles = $asientos;
        $ya_salio = 0;

        // Insertar en la tabla 'omnibus'
        $insertar = $pdo->prepare(
            "INSERT INTO omnibus (n_omnibus, asientos, asientos_disponibles, origen, destino, precio, agencia, ya_salio, fecha, hora, hora_llegada)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $insertar->execute([
            $n_omnibus, 
            $asientos, 
            $asientos_disponibles, 
            $origen, 
            $destino, 
            $precio, 
            $agencia, 
            $ya_salio, 
            $fecha, 
            $hora, 
            $hora_llegada
        ]);

        // Redirigir al panel de administrador con un mensaje de éxito
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Viaje registrado correctamente.'));
        exit();

    } catch (PDOException $e) {
        // En caso de error, redirigimos con un mensaje de error
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Error en la base de datos al dar de alta el viaje.'));
        exit();
    }
?>