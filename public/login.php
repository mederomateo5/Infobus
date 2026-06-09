<?php
// ================================================================
//  login.php  –  Login + Registro en una sola página
// ================================================================
require_once '../config/database.php';
require_once '../src/User.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: welcome.php');
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
        header('Location: welcome.php');
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
    <title>Ómnibus — Acceso</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #e9f0fb;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #1c1e21;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* ── Layout centrado ── */
        .page {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 16px;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #dddfe2;
            border-radius: 8px;
            padding: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* ── Logo / título ── */
        .brand-logo {
            font-size: 28px;
            font-weight: bold;
            color: #1877f2;
            text-align: center;
            margin-bottom: 16px;
        }

        /* ── Tabs ── */
        .tabs {
            display: flex;
            border-bottom: 1px solid #dddfe2;
            margin-bottom: 20px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 10px;
            border: none;
            background: transparent;
            font-size: 15px;
            font-weight: bold;
            color: #606770;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
        }

        .tab-btn.active {
            color: #1877f2;
            border-bottom: 3px solid #1877f2;
        }

        /* ── Secciones de formulario ── */
        .form-section { display: none; }
        .form-section.active { display: block; }

        .form-title {
            font-size: 20px;
            font-weight: bold;
            color: #1c1e21;
            margin-bottom: 4px;
        }

        .form-subtitle {
            font-size: 13px;
            color: #606770;
            margin-bottom: 16px;
        }

        /* ── Campos ── */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 12px;
        }

        .field label {
            font-size: 13px;
            font-weight: bold;
            color: #1c1e21;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 1px solid #606770;
            font-size: 10px;
            color: #606770;
            cursor: pointer;
            font-style: italic;
            font-weight: bold;
        }

        .field input {
            width: 100%;
            padding: 10px 12px;
            background-color: #ffffff;
            border: 1px solid #ccd0d5;
            border-radius: 6px;
            font-size: 14px;
            color: #1c1e21;
            outline: none;
        }

        .field input:focus {
            border-color: #1877f2;
            box-shadow: 0 0 0 2px #b3caf5;
        }

        .field input.invalid {
            border-color: #e02020;
        }

        .field-error {
            font-size: 12px;
            color: #e02020;
            min-height: 15px;
        }

        /* ── Fuerza de contraseña ── */
        .pass-strength {
            display: flex;
            gap: 4px;
            margin-top: 4px;
        }

        .strength-bar {
            flex: 1;
            height: 4px;
            border-radius: 4px;
            background-color: #dddfe2;
            transition: background-color 0.3s;
        }

        .strength-bar.weak   { background-color: #e02020; }
        .strength-bar.medium { background-color: #f5a623; }
        .strength-bar.strong { background-color: #42b72a; }

        .strength-label {
            font-size: 12px;
            color: #606770;
            margin-top: 3px;
        }

        /* ── Botón principal ── */
        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #1877f2;
            border: none;
            border-radius: 6px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 8px;
        }

        .btn-primary:hover {
            background-color: #166fe5;
        }

        .btn-primary:active {
            background-color: #1464d2;
        }

        /* ── Alerta de error ── */
        .alert {
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .alert-error {
            background-color: #fff0f0;
            border: 1px solid #f5c6c6;
            color: #e02020;
        }

    </style>
</head>
<body>

<div class="page">

    <main>
        <div class="card">

            <!-- Logo -->
            <div class="brand-logo"><img src="img/logotipo.png" style="height: 100px;"></div>

            <!-- Tabs -->
            <div class="tabs" role="tablist">
                <button class="tab-btn <?= $activeTab === 'login'    ? 'active' : '' ?>"
                        role="tab" onclick="switchTab('login')">
                    Iniciar sesión
                </button>
                <button class="tab-btn <?= $activeTab === 'register' ? 'active' : '' ?>"
                        role="tab" onclick="switchTab('register')">
                    Registrarse
                </button>
            </div>

            <!-- ── LOGIN ────────────────────────────────────── -->
            <section class="form-section <?= $activeTab === 'login' ? 'active' : '' ?>"
                     id="tab-login">

                <h1 class="form-title">Bienvenido de vuelta</h1>
                <p class="form-subtitle">Ingresá con tu cuenta para continuar.</p>

                <?php if ($loginError): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($loginError) ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php" novalidate>
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

            <!-- ── REGISTRO ─────────────────────────────────── -->
            <section class="form-section <?= $activeTab === 'register' ? 'active' : '' ?>"
                     id="tab-register">

                <h1 class="form-title">Crear cuenta</h1>
                <p class="form-subtitle">Completá tus datos para registrarte.</p>

                <form method="POST" action="login.php" novalidate>
                    <input type="hidden" name="action" value="register">

                    <div class="field-row">
                        <div class="field">
                            <label for="reg-nombre">
                                Nombre
                                <span class="info-icon" title="Tu nombre de pila">i</span>
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

        </div>
    </main>

</div>

<script>
    // ── Tabs ─────────────────────────────────────────────────
    function switchTab(tab) {
        document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    // ── Fuerza de contraseña ──────────────────────────────────
    function updateStrength(val) {
        const bars   = [document.getElementById('bar1'), document.getElementById('bar2'), document.getElementById('bar3')];
        const label  = document.getElementById('strength-label');
        const levels = ['weak','medium','strong'];
        let score = 0;

        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[!@#$%^&*(),.?":{}|<>_\-\[\]\/\\]/.test(val)) score++;

        const texts = ['', 'Débil', 'Media', 'Fuerte'];
        const colors = ['', '#e02020', '#f5a623', '#42b72a'];

        bars.forEach((b, i) => {
            b.className = 'strength-bar';
            if (i < score) b.classList.add(levels[score - 1]);
        });

        label.textContent = val.length ? texts[score] : '';
        label.style.color  = colors[score];
    }
</script>

</body>
</html>
