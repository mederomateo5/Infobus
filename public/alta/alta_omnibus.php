<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_tipo']) || !in_array($_SESSION['user_tipo'], ['compania', 'administrador'])) {
    die("Acceso denegado. Esta página es exclusiva para compañías o administradores.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: alta_omnibus_gui.php');
    exit();
}

$compania_id = filter_input(INPUT_POST, 'compania_id', FILTER_VALIDATE_INT);
$n_omnibus   = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
$asientos    = filter_input(INPUT_POST, 'asientos', FILTER_VALIDATE_INT);
$ruta_key    = trim($_POST['ruta_key'] ?? 'urbano');

if (!$compania_id || !$n_omnibus || !$asientos) {
    header('Location: alta_omnibus_gui.php?msg=' . urlencode('Faltan datos obligatorios o son inválidos.'));
    exit();
}

try {
    // Validar restricción UNIQUE (compania_id, n_omnibus)
    $chk = $pdo->prepare("SELECT id FROM omnibus WHERE compania_id = ? AND n_omnibus = ?");
    $chk->execute([$compania_id, $n_omnibus]);
    if ($chk->fetch()) {
        header('Location: alta_omnibus_gui.php?msg=' . urlencode('Esa compañía ya tiene registrado ese número de ómnibus.'));
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO omnibus (compania_id, n_omnibus, asientos, ruta_key) VALUES (?, ?, ?, ?)");
    $stmt->execute([$compania_id, $n_omnibus, $asientos, $ruta_key]);

    header('Location: alta_omnibus_gui.php?msg=' . urlencode('Ómnibus registrado correctamente.'));
    exit();

} catch (PDOException $e) {
    header('Location: alta_omnibus_gui.php?msg=' . urlencode('Error al registrar el ómnibus.'));
    exit();
}
?>