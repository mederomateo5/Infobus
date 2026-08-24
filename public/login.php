<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
    require_once '../config/database.php';
    require_once '../src/User.php';

    session_start();

    if (isset($_SESSION['user_id'])) {
        header('Location: ../public/welcome.php');
        exit();
    }

    $userObj     = new User($pdo);
    $loginError  = '';
    $regErrors   = [];
    $activeTab   = 'login'; // qué pestaña mostrar por defecto

    // ----------------------------------------------------------------
    //  POST: Login
    // ----------------------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
        $activeTab = 'login';
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $loggedUser = $userObj->login($email, $password);

        if ($loggedUser) {
            $_SESSION['user_id']   = $loggedUser['id'];
            $_SESSION['user_name'] = $loggedUser['nombre'];
            $_SESSION['user_tipo'] = $loggedUser['tipo'];
            header('Location: ../public/welcome.php');
            exit();
        } else {
            $loginError = 'Email o contraseña incorrectos.';
        }
    }

    // ----------------------------------------------------------------
    //  POST: Registro
    // ----------------------------------------------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
        $activeTab  = 'register';
        $regErrors  = $userObj->validateRegister($_POST);

        if (empty($regErrors)) {
            $userObj->register($_POST);
            // Loguear automáticamente
            $newUser = $userObj->login($_POST['email'], $_POST['password']);
            if ($newUser) {
                $_SESSION['user_id']   = $newUser['id'];
                $_SESSION['user_name'] = $newUser['nombre'];
                $_SESSION['user_tipo'] = $newUser['tipo'];
                header('Location: welcome.php');
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso</title>
    <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="page">
        <main>
            <div class="card">
                <!-- Logo -->
                <div class="brand-logo img"><img src="../public/img/logotipo.png" alt="Logo"></div>

                <!-- Tabs -->
                <div class="tabs" role="tablist">
                    <button class="tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>"
                            role="tab" onclick="switchTab('login')">
                        Iniciar sesión
                    </button>
                    <button class="tab-btn <?= $activeTab === 'register' ? 'active' : '' ?>"
                            role="tab" onclick="switchTab('register')">
                        Registrarse
                    </button>
                </div>

                <!-- LOGIN -->
                <section class="form-section <?= $activeTab === 'login' ? 'active' : '' ?>"
                        id="tab-login">

                    <h1 class="form-title">Bienvenido de vuelta</h1>
                    <p class="form-subtitle">Ingresá con tu cuenta para continuar.</p>

                    <?php if ($loginError) { ?>
                        <div class="alert-error"><?= htmlspecialchars($loginError) ?></div>
                    <?php } ?>

                    <!-- Formulario de login -->
                    <form method="POST" action="../public/login.php" novalidate>
                        <input type="hidden" name="action" value="login">

                        <div class="field">
                            <label for="login-email">
                                Email
                                <span class="info-icon" title="Ingresá tu dirección de correo electrónico">i</span>
                            </label>
                            <input type="email" id="login-email" name="email"
                                placeholder="tu@email.com" required
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="field">
                            <label for="login-password">
                                Contraseña
                                <span class="info-icon" title="Tu contraseña de acceso">i</span>
                            </label>
                            <input type="password" id="login-password" name="password"
                                placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn-primary">Ingresar →</button>
                    </form>
                </section>

                <!-- REGISTRO -->
                <section class="form-section <?= $activeTab === 'register' ? 'active' : '' ?>"
                        id="tab-register">

                    <h1 class="form-title">Crear cuenta</h1>
                    <p class="form-subtitle">Completá tus datos para registrarte.</p>
                    
                    <!-- Formulario de registro -->
                    <form method="POST" action="../public/login.php" novalidate>
                        <input type="hidden" name="action" value="register">

                        <div class="field-row">
                            <div class="field">
                                <label for="reg-nombre">
                                    Nombre
                                    <span class="info-icon" title="Tu nombre">i</span>
                                </label>
                                <input type="text" id="reg-nombre" name="nombre"
                                    placeholder="Juan"
                                    class="<?= isset($regErrors['nombre']) ? 'invalid' : '' ?>"
                                    value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                                <span class="field-error"><?= $regErrors['nombre'] ?? '' ?></span>
                            </div>

                            <div class="field">
                                <label for="reg-apellido">
                                    Apellido
                                    <span class="info-icon" title="Tu apellido">i</span>
                                </label>
                                <input type="text" id="reg-apellido" name="apellido"
                                    placeholder="García"
                                    class="<?= isset($regErrors['apellido']) ? 'invalid' : '' ?>"
                                    value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">
                                <span class="field-error"><?= $regErrors['apellido'] ?? '' ?></span>
                            </div>
                        </div>

                        <div class="field">
                            <label for="reg-email">
                                Email
                                <span class="info-icon" title="Tu dirección de correo electrónico">i</span>
                            </label>
                            <input type="email" id="reg-email" name="email"
                                placeholder="tu@email.com"
                                class="<?= isset($regErrors['email']) ? 'invalid' : '' ?>"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <span class="field-error"><?= $regErrors['email'] ?? '' ?></span>
                        </div>

                        <div class="field">
                            <label for="reg-telefono">
                                Teléfono
                                <span class="info-icon" title="Número de teléfono, solo dígitos. Ej: 099123456">i</span>
                            </label>
                            <input type="tel" id="reg-telefono" name="telefono"
                                placeholder="099 123 456"
                                class="<?= isset($regErrors['telefono']) ? 'invalid' : '' ?>"
                                value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                            <span class="field-error"><?= $regErrors['telefono'] ?? '' ?></span>
                        </div>

                        <div class="field">
                            <label for="reg-password">
                                Contraseña
                                <span class="info-icon" title="Mínimo 8 caracteres, una mayúscula y un símbolo especial">i</span>
                            </label>
                            <input type="password" id="reg-password" name="password"
                                placeholder="••••••••"
                                class="<?= isset($regErrors['password']) ? 'invalid' : '' ?>"
                                oninput="updateStrength(this.value)">
                            <div class="pass-strength">
                                <div class="strength-bar" id="bar1"></div>
                                <div class="strength-bar" id="bar2"></div>
                                <div class="strength-bar" id="bar3"></div>
                            </div>
                            <span class="strength-label" id="strength-label"></span>
                            <span class="field-error"><?= $regErrors['password'] ?? '' ?></span>
                        </div>

                        <div class="field">
                            <label for="reg-confirm">
                                Confirmar contraseña
                                <span class="info-icon" title="Repetí la contraseña para confirmarla">i</span>
                            </label>
                            <input type="password" id="reg-confirm" name="confirm_password"
                                placeholder="••••••••"
                                class="<?= isset($regErrors['confirm_password']) ? 'invalid' : '' ?>">
                            <span class="field-error"><?= $regErrors['confirm_password'] ?? '' ?></span>
                        </div>

                        <button type="submit" class="btn-primary">Crear cuenta →</button>
                    </form>
                </section>
            </div> <!-- Fin Card -->
        </main>
    </div> <!-- Fin Page -->

    <script>
        // Tabs
        function switchTab(tab) {
            // Ocultar todas las secciones y quitar la clase activa de todos los botones
            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            event.currentTarget.classList.add('active');
        }

        // Fuerza de contraseña
        function updateStrength(val) {
            // Elementos de la barra de fuerza y etiqueta
            const bars   = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];
            const label  = document.getElementById('strength-label');
            const levels = ['weak','medium','strong'];
            let score = 0;

            // Criterios de fuerza
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[!@#$%^&*(),.?":{}|<>_\-\[\]\/\\]/.test(val)) score++;

            // Actualizar barras y etiqueta
            const texts = ['', 'Débil', 'Media', 'Fuerte'];
            const colors = ['', '#e02020', '#f5a623', '#42b72a'];

            // Resetear clases de barras
            bars.forEach((b, i) => {
                b.className = 'strength-bar';
                if (i < score) b.classList.add(levels[score - 1]);
            });

            // Actualizar etiqueta
            label.textContent = val.length ? texts[score] : '';
            label.style.color  = colors[score];
        }

        // Bloquear pegar en confirmar contraseña
        const confirmar = document.getElementById("reg-confirm");

        // Evitar pegar, cortar o arrastrar en el campo de confirmación de contraseña
        ["paste", "cut", "drop"].forEach(evento => {
            confirmar.addEventListener(evento, function(e) {
                e.preventDefault();
            });
        });
    </script>
</body>
</html>
