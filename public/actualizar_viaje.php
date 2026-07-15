<?php
    session_start();

    // Verificar sesión y rol de administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../config/database.php';

    // Solo aceptar peticiones POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../public/panel_admin.php');
        exit();
    }

    // Recibir y sanitizar datos
    $id                   = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $n_omnibus            = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
    $asientos             = filter_input(INPUT_POST, 'asientos', FILTER_VALIDATE_INT);
    $asientos_disponibles = filter_input(INPUT_POST, 'asientos_disponibles', FILTER_VALIDATE_INT);
    $origen               = trim($_POST['origen'] ?? '');
    $destino              = trim($_POST['destino'] ?? '');
    $precio               = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $agencia              = $_POST['agencia'] ?? '';
    $ya_salio             = filter_input(INPUT_POST, 'ya_salio', FILTER_VALIDATE_INT);
    $fecha                = $_POST['fecha'] ?? '';
    $hora                 = $_POST['hora'] ?? '';
    $hora_llegada         = $_POST['hora_llegada'] ?? '';

    // Agencias válidas del ENUM
    $agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

    // Validaciones de negocio básicas
    if (
        !$id || !$n_omnibus || !$asientos || $asientos_disponibles === false || 
        $origen === '' || $destino === '' || $precio === false || 
        !in_array($agencia, $agenciasValidas) || ($ya_salio !== 0 && $ya_salio !== 1) ||
        $fecha === '' || $hora === '' || $hora_llegada === ''
    ) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Faltan datos o se ingresaron valores inválidos para la actualización.'));
        exit();
    }

    try {
        // Ejecutar UPDATE
        $actualizar = $pdo->prepare(
            "UPDATE omnibus 
             SET n_omnibus = ?, 
                 asientos = ?, 
                 asientos_disponibles = ?, 
                 origen = ?, 
                 destino = ?, 
                 precio = ?, 
                 agencia = ?, 
                 ya_salio = ?, 
                 fecha = ?, 
                 hora = ?, 
                 hora_llegada = ?
             WHERE id = ?"
        );

        $actualizar->execute([
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
            $hora_llegada,
            $id
        ]);

        // Redirigir con mensaje de éxito
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Viaje actualizado correctamente.'));
        exit();

    } catch (PDOException $e) {
        // Si hay problemas con la base de datos redirige con error
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Error en la base de datos al actualizar el viaje.'));
        exit();
    }
?>