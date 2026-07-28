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
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Faltan datos o se ingresaron valores inválidos.'));
        exit();
    }

    // ----------------------------------------------------
    // VALIDACIONES DE NEGOCIO
    // ----------------------------------------------------

    // 1. Origen y destino no pueden ser iguales
    if (strcasecmp($origen, $destino) === 0) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('El origen y el destino no pueden ser iguales.'));
        exit();
    }

    // 2. Fecha no puede ser anterior a hoy
    $hoy = date('Y-m-d');
    if ($fecha < $hoy) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('La fecha del viaje no puede ser del pasado.'));
        exit();
    }

    // 3. Hora no puede ser en el pasado si la fecha es HOY
    $hora_actual = date('H:i');
    if ($fecha === $hoy && $hora < $hora_actual) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('La hora de salida no puede ser anterior a la hora actual.'));
        exit();
    }

    // 4. Salida y llegada no pueden ser la misma hora exacta
    if ($hora === $hora_llegada) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('La hora de salida y la de llegada no pueden ser iguales.'));
        exit();
    }

    // 5. Validar si la hora de salida es posterior a la de llegada
    list($h_salida, $m_salida) = explode(':', $hora);
    list($h_llegada, $m_llegada) = explode(':', $hora_llegada);

    $min_salida = ($h_salida * 60) + $m_salida;
    $min_llegada = ($h_llegada * 60) + $m_llegada;

    if ($min_salida > $min_llegada) {
        // Solo se permite si la salida es a las 23:00 hs o más
        if ($h_salida < 23) {
            header('Location: ../public/panel_admin.php?msg=' . urlencode('La hora de salida solo puede ser posterior a la de llegada si sale a partir de las 23:00 hs.'));
            exit();
        }
    }

    try {
        $asientos_disponibles = $asientos;
        $ya_salio = 0;

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

        header('Location: ../public/panel_admin.php?msg=' . urlencode('Viaje registrado correctamente.'));
        exit();

    } catch (PDOException $e) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Error en la base de datos al dar de alta el viaje.'));
        exit();
    }
?>