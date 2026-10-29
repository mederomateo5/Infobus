<?php
require_once '../../private/config/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || ($_SESSION['user_tipo'] !== 'administrador' && $_SESSION['user_tipo'] !== 'compania')) {
        die("Acceso denegado.");
}

require_once '../../private/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../gestion/ver_omnibus.php');
    exit();
}

$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$compania_id = filter_input(INPUT_POST, 'compania_id', FILTER_VALIDATE_INT);
$n_omnibus   = filter_input(INPUT_POST, 'n_omnibus', FILTER_VALIDATE_INT);
$asientos    = filter_input(INPUT_POST, 'asientos', FILTER_VALIDATE_INT);
$ruta_key    = trim($_POST['ruta_key'] ?? 'urbano');

if (!$id || !$compania_id || !$n_omnibus || !$asientos) {
    header('Location: actualizar_omnibus_gui.php?id=' . $id . '&msg=' . urlencode('Datos obligatorios incompletos.'));
    exit();
}

try {
    // Verificar que no se duplique la clave (compania_id, n_omnibus) en otro coche
    $chk = $pdo->prepare("SELECT id FROM omnibus WHERE compania_id = ? AND n_omnibus = ? AND id != ?");
    $chk->execute([$compania_id, $n_omnibus, $id]);
    if ($chk->fetch()) {
        header('Location: actualizar_omnibus_gui.php?id=' . $id . '&msg=' . urlencode('Esa compañía ya posee un coche registrado con ese número.'));
        exit();
    }

    $update = $pdo->prepare("UPDATE omnibus SET compania_id = ?, n_omnibus = ?, asientos = ?, ruta_key = ? WHERE id = ?");
    $update->execute([$compania_id, $n_omnibus, $asientos, $ruta_key, $id]);

    header('Location: ../gestion/ver_omnibus.php?msg=' . urlencode('Ómnibus actualizado con éxito.'));
    exit();

} catch (PDOException $e) {
    header('Location: actualizar_omnibus_gui.php?id=' . $id . '&msg=' . urlencode('Error al actualizar el ómnibus.'));
    exit();
}
?>