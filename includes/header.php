<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<style>

    *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 50px;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    z-index: 1000;
    box-sizing: border-box;
}

.navbar .navbar-logo img {
    height: 50px;
    width: auto;
    display: block;
}

/* Links del centro */
.navbar .navbar-links {
    display: flex;
    gap: 30px;
    list-style: none;
    margin: 0;
    padding: 0;
    /* centrado absoluto respecto al navbar */
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.navbar .navbar-links a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    font-family: Arial, sans-serif;
    font-size: 0.95rem;
    transition: color 0.25s;
}

.navbar .navbar-links a:hover {
    color: #0077ff;
}

/* Ícono usuario — extremo derecho */
.navbar .navbar-user {
    display: flex;
    align-items: center;
    margin-left: auto; /* empuja al borde derecho si el absoluto falla */
}

.navbar .navbar-user a {
    color: #333;
    font-size: 1.2rem;
    text-decoration: none;
    transition: color 0.25s;
}

.navbar .navbar-user a:hover {
    color: #0077ff;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .navbar {
        padding: 14px 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .navbar .navbar-links {
        position: static;
        transform: none;
        order: 3;
        width: 100%;
        justify-content: center;
        gap: 20px;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }

    .navbar .navbar-user {
        margin-left: auto;
    }
}
</style>
<body>

    <nav class="navbar">

        <!-- Logo -->
        <div class="navbar-logo">
            <img src="../public/img/logotipo.png" alt="Logo InfoBus">
        </div>

        <!-- Links centrados -->
        <ul class="navbar-links">
            <li><a href="../public/index.php">Inicio</a></li>
            <li><a href="../public/sobre_nosotros.php">Nosotros</a></li>
            <li><a href="../public/welcome.php">Panel</a></li>
        </ul>

        <!-- Ícono usuario a la derecha -->
        <div class="navbar-user">
            <a href="../public/login.php" title="Iniciar sesión">
                <i class="fa-solid fa-user"></i>
            </a>

        </div>

    </nav>

</body>
</html>
