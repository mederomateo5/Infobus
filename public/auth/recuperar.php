<?php
require_once '../../private/config/config.php';
require_once '../../private/config/database.php';
require_once '../../private/src/User.php';
require_once '../../private/src/DosFactores.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: redirigir.php');
    exit();
}

$mensaje = '';
$tipoMensaje = 'danger';
$userObj = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $mensaje = 'Por favor, ingresá tu correo electrónico.';
        $tipoMensaje = 'danger';
    } else {
        $user = $userObj->getByEmail($email);

        if ($user) {
            // Si el usuario existe, iniciamos el 2FA y lo mandamos a ingresar el código
            if (DosFactores::iniciar($user)) {
                header('Location: verificar_codigo.php');
                exit();
            } else {
                $mensaje = 'No pudimos enviar el correo de recuperación. Probá de nuevo más tarde.';
                $tipoMensaje = 'danger';
            }
        } else {
            // Mensaje genérico idéntico si no existe, para evitar enumeración de cuentas
            $mensaje = 'Si el correo está registrado en nuestro sistema, te hemos enviado un código de acceso.';
            $tipoMensaje = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Recuperar contraseña</title>
    <?php include '../../private/includes/head.php'; ?>
</head>
<body class="bg-body">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card shadow-sm p-4">
        <h4 class="mb-2">Recuperar contraseña</h4>
        <p class="text-muted">Te enviaremos un código para que puedas entrar a tu cuenta sin contraseña y puedas cambiarla mediante el panel de usuario.</p>

        <?php if ($mensaje): ?>
          <div class="alert alert-<?= $tipoMensaje ?>"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label for="email" class="form-label fw-bold">Correo electrónico</label>
            <input type="email" id="email" name="email" required autofocus
                   class="form-control" placeholder="tu@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
          <button type="submit" class="btn btn-primary w-100">Enviar código</button>
        </form>

        <a href="login.php" class="d-block text-center mt-3">Volver a iniciar sesión</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>