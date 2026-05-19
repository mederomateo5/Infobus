<?php
    require '../config/database.php';
    require '../src/User.php';
    // Verificar si el formulario fue enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear objeto User
        $user = new User($pdo);
        // Guardar datos del formulario
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $email = htmlspecialchars($_POST['email']);
        // Registrar usuario
        $user->register($username, $password, $email);
        // Redirigir al login
        header('Location: login.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<body>
    <h2>Registro</h2>
    <form method="POST" action="signup.php">
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
            Registrarse
        </button>
    </form>
    <br>
    <a href="login.php">
        Ya tengo cuenta
    </a>
</body>
</html>