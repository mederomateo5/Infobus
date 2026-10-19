<?php
require_once '../../private/config/config.php';
require_once '../../private/src/DosFactores.php';
session_start();

if (isset($_SESSION['user_id'])) { header('Location: redirigir.php'); exit(); }
if (empty($_SESSION['2fa']))     { header('Location: login.php');     exit(); }

$mensaje  = '';
$terminado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch (DosFactores::verificar(trim($_POST['codigo'] ?? ''))) {
        case 'ok':
            header('Location: redirigir.php');
            exit();
        case 'incorrecto':
            $mensaje = 'Código incorrecto.';
            break;
        case 'bloqueado':
            $mensaje = 'Demasiados intentos. Volvé a iniciar sesión.';
            $terminado = true;
            break;
        default: // vencido o sin_pendiente
            $mensaje = 'El código venció. Volvé a iniciar sesión.';
            $terminado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Verificación en dos pasos</title>
    <?php include '../../private/includes/head.php'; ?>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm p-4">
        <h4 class="mb-2">Verificación en dos pasos</h4>
        <p class="text-muted">Te enviamos un código de 6 dígitos a tu correo. Vence en 5 minutos.</p>

        <?php if ($mensaje): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!$terminado): ?>
          <form method="POST">
            <input type="text" name="codigo" maxlength="6" required autofocus
                   inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code"
                   class="form-control form-control-lg text-center mb-3">
            <button class="btn btn-primary w-100">Verificar</button>
          </form>
        <?php endif; ?>

        <a href="login.php" class="d-block text-center mt-3">Volver a iniciar sesión</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>