<?php
    session_start();

    // Verificar sesión y rol de administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../public/alta_viaje_gui.php');
        exit();
    }

    // Recibir y sanitizar datos
    $n_omnibus    = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
    $asientos     = filter_input(INPUT_POST, 'asientos', FILTER_VALIDATE_INT);
    $origen       = trim($_POST['origen'] ?? '');
    $destino      = trim($_POST['destino'] ?? '');
    $precio       = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $agencia      = $_POST['agencia'] ?? '';
    $fecha        = $_POST['fecha'] ?? '';
    $hora         = $_POST['hora'] ?? '';
    $hora_llegada = $_POST['hora_llegada'] ?? '';

    $agenciasValidas = ['COT', 'TAB', 'TURIL', 'URBANO'];

    // Validar que no falten datos obligatorios
    if (
        !$n_omnibus || !$asientos || $origen === '' || $destino === '' ||
        $precio === false || !in_array($agencia, $agenciasValidas) ||
        $fecha === '' || $hora === '' || $hora_llegada === ''
    ) {
        header('Location: ../public/alta_viaje_gui.php?msg=' . urlencode('Faltan datos o se ingresaron valores inválidos.'));
        exit();
    }

    try {
        // Obtener el ID del ómnibus correspondiente al número y agencia
        $stmtOmnibus = $pdo->prepare(
            "SELECT o.id FROM omnibus o
             JOIN compania c ON o.compania_id = c.id
             WHERE o.n_omnibus = ? AND c.nombre = ?"
        );
        $stmtOmnibus->execute([$n_omnibus, $agencia]);
        $omnibus = $stmtOmnibus->fetch();

        if (!$omnibus) {
            header('Location: ../public/alta_viaje_gui.php?msg=' . urlencode('No se encontró el ómnibus con el número y agencia especificados.'));
            exit();
        }

        $omnibusId = (int)$omnibus['id'];
        $yaSalio = 0; // nuevo viaje no ha salido aún

        // Insertar el viaje
        $insertar = $pdo->prepare(
            "INSERT INTO viajes (omnibus_id, origen, destino, precio, ya_salio, fecha, hora, hora_llegada)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $exito = $insertar->execute([
            $omnibusId,
            $origen,
            $destino,
            $precio,
            $yaSalio,
            $fecha,
            $hora,
            $hora_llegada
        ]);

        if ($exito) {
            header('Location: ../public/alta_viaje_gui.php?msg=' . urlencode('Viaje registrado correctamente.'));
        } else {
            header('Location: ../public/alta_viaje_gui.php?msg=' . urlencode('Error al registrar el viaje.'));
        }
        exit();

    } catch (PDOException $e) {
        header('Location: ../public/alta_viaje_gui.php?msg=' . urlencode('Error en la base de datos al dar de alta el viaje.'));
        exit();
    }
?>