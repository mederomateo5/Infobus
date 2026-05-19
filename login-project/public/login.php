<?php
    require '../config/database.php';
    require '../src/User.php';
    // Verificar si el formulario fue enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear objeto User
        $user = new User($pdo);
        // Obtener datos del formulario
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $email = htmlspecialchars($_POST['email']);
        // Intentar login
        $loggedInUser = $user->login($username, $password, $email);
        // Verificar si existe el usuario
        if ($loggedInUser) {
            // Iniciar sesión
            session_start();
            // Guardar ID del usuario
            $_SESSION['user_id'] = $loggedInUser['id'];
            // Redirigir
            header('Location: welcome.php');
            exit();
        } else {
            $error = "Credenciales incorrectas";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;">
            <?php echo $error; ?>
        </p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input
            type="text"
            name="username"
            placeholder="Username"
            required
        >
        <br><br>
        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >
        <br><br>
        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >
        <br><br>
        <button type="submit">
            Login
        </button>
    </form>
    <br>
    <a href="signup.php">
        Crear cuenta
    </a>
</body>
</html>