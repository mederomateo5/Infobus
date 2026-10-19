<?php
require_once '../../private/config/config.php';
    session_start();

    // Verificar sesión y que sea administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../../private/config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: alta_parada_gui.php');
        exit();
    }

    // Recibir datos del formulario
    $nombre   = trim($_POST['nombre']   ?? '');
    $latitud = trim($_POST['latitud'] ?? '');
    $longitud    = trim($_POST['longitud']    ?? '');

    // Validar que no falten datos obligatorios y que el tipo de usuario sea válido
    if ($nombre === '' || $latitud === '' || $longitud === '' ) {
        header('Location: alta_parada_gui.php?msg=' . urlencode('Faltan datos o el tipo de usuario no es válido.'));
        exit();
    }

    // Validar que latitud y longitud sean valores numéricos dentro de rango válido
if (!is_numeric($latitud) || !is_numeric($longitud) || $latitud < -90 || $latitud > 90 || $longitud < -180 || $longitud > 180) {
    header('Location: alta_parada_gui.php?msg=' . urlencode('Las coordenadas ingresadas no son válidas.'));
    exit();
}

try {
    // Verificar que no exista otra parada con el mismo nombre
    $verificar = $pdo->prepare("SELECT id FROM paradas WHERE nombre = ?");
    $verificar->execute([$nombre]);

    if ($verificar->fetch()) {
        header('Location: alta_parada_gui.php?msg=' . urlencode('Ya existe una parada registrada con ese nombre.'));
        exit();
    }

        // Insertar parada nueva
        $insertar = $pdo->prepare(
            "INSERT INTO paradas (nombre, latitud, longitud)
            VALUES (?, ?, ?)"
        );
        $insertar->execute([$nombre, $latitud, $longitud]);

        // Redirigir al panel de administrador con un mensaje de éxito
        header('Location: alta_parada_gui.php?msg=' . urlencode('Parada dada de alta correctamente.'));
        exit();

    } catch (PDOException $e) {
        // En caso de error, redirigimos con un mensaje de error
        header('Location: alta_parada_gui.php?msg=' . urlencode('Error al dar de alta la parada.'));
        exit();
    }
?>