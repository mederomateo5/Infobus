<?php
// ================================================================
//  login.php  –  Login + Registro en una sola página
// ================================================================
require_once '../config/database.php';
require_once '../src/User.php';

session_start();

// Si ya está logueado, lo mandamos a welcome
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ── Reset & Variables ─────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0d0f14;
            --surface:   #161920;
            --surface2:  #1e2230;
            --border:    #2a2f42;
            --accent:    #f5a623;
            --accent2:   #e8884a;
            --text:      #f0f2f8;
            --muted:     #7a8099;
            --error:     #ff6b6b;
            --success:   #5dd89c;
            --radius:    14px;
            --radius-sm: 8px;
        }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            line-height: 1.6;
        }

        /* ── Layout ────────────────────────────────────────────── */
        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* Panel izquierdo – ilustración / branding */
        .brand-panel {
            background: linear-gradient(145deg, #1a1e2e 0%, #0d0f14 60%, #1c1408 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 56px;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 30% 40%, rgba(245,166,35,.13) 0%, transparent 70%),
                radial-gradient(ellipse 40% 35% at 70% 80%, rgba(232,136,74,.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Big decorative bus outline */
        .brand-panel::after {
            content: '🚌';
            position: absolute;
            bottom: -20px;
            right: -30px;
            font-size: 220px;
            opacity: .05;
            pointer-events: none;
            filter: grayscale(1);
        }

        .brand-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 2.4rem;
            letter-spacing: -.03em;
            color: var(--accent);
            margin-bottom: 8px;
            position: relative;
        }

        .brand-tagline {
            font-size: .95rem;
            color: var(--muted);
            max-width: 320px;
            margin-bottom: 48px;
            position: relative;
        }

        .brand-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: relative;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: .9rem;
            color: #9ba3c0;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(245,166,35,.12);
            border: 1px solid rgba(245,166,35,.25);
            display: grid;
            place-items: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* Panel derecho – formularios */
        .form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 40px;
            overflow-y: auto;
        }

        .card {
            width: 100%;
            max-width: 440px;
        }

        /* ── Tabs ──────────────────────────────────────────────── */
        .tabs {
            display: flex;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 4px;
            margin-bottom: 32px;
            gap: 4px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: calc(var(--radius) - 3px);
            background: transparent;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
        }

        .tab-btn.active {
            background: var(--accent);
            color: #0d0f14;
            font-weight: 700;
        }

        /* ── Form sections ─────────────────────────────────────── */
        .form-section { display: none; }
        .form-section.active { display: block; }

        .form-title {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 6px;
            letter-spacing: -.02em;
        }

        .form-subtitle {
            color: var(--muted);
            font-size: .875rem;
            margin-bottom: 28px;
        }

        /* ── Field groups ──────────────────────────────────────── */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
            position: relative;
        }

        .field label {
            font-size: .82rem;
            font-weight: 500;
            color: #a0a8c0;
            letter-spacing: .03em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Ícono info placeholder */
        .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid var(--muted);
            font-size: .65rem;
            color: var(--muted);
            cursor: pointer;
            font-style: italic;
            font-weight: 700;
            /* Reemplazá con tu ícono real */
        }

        .field input {
            width: 100%;
            padding: 11px 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(245,166,35,.12);
        }

        .field input.invalid {
            border-color: var(--error);
        }

        .field-error {
            font-size: .78rem;
            color: var(--error);
            min-height: 16px;
        }

        /* Fuerza de contraseña */
        .pass-strength {
            display: flex;
            gap: 4px;
            margin-top: 6px;
        }

        .strength-bar {
            flex: 1;
            height: 3px;
            border-radius: 99px;
            background: var(--border);
            transition: background .3s;
        }

        .strength-bar.weak   { background: var(--error); }
        .strength-bar.medium { background: var(--accent); }
        .strength-bar.strong { background: var(--success); }

        .strength-label {
            font-size: .75rem;
            color: var(--muted);
            margin-top: 4px;
        }

        /* ── Botón principal ───────────────────────────────────── */
        .btn-primary {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            border-radius: var(--radius-sm);
            color: #0d0f14;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s, opacity .15s;
            margin-top: 8px;
            letter-spacing: .01em;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(245,166,35,.28);
        }

        .btn-primary:active { transform: translateY(0); }

        /* ── Alert ─────────────────────────────────────────────── */
        .alert {
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            margin-bottom: 18px;
        }

        .alert-error {
            background: rgba(255,107,107,.1);
            border: 1px solid rgba(255,107,107,.3);
            color: var(--error);
        }

        /* ── Responsive ────────────────────────────────────────── */
        @media (max-width: 768px) {
            .page { grid-template-columns: 1fr; }
            .brand-panel { display: none; }
            .form-panel { padding: 32px 20px; }
            .card { max-width: 100%; }
            .field-row { grid-template-columns: 1fr; gap: 0; }
        }

        @media (max-width: 380px) {
            .form-panel { padding: 24px 16px; }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ── Brand panel ─────────────────────────────────────── -->
    <aside class="brand-panel">
        <div class="brand-logo">Ómnibus UY</div>
        <p class="brand-tagline">Gestioná rutas, choferes y pasajeros desde un solo lugar.</p>
        <ul class="brand-features">
            <li>
                <span class="feature-icon">🗺️</span>
                Seguimiento de rutas en tiempo real
            </li>
            <li>
                <span class="feature-icon">👤</span>
                Gestión de choferes y pasajeros
            </li>
            <li>
                <span class="feature-icon">📊</span>
                Reportes y estadísticas de viajes
            </li>
        </ul>
    </aside>

    <!-- ── Form panel ──────────────────────────────────────── -->
    <main class="form-panel">
        <div class="card">

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

        </div><!-- /.card -->
    </main>

</div><!-- /.page -->

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
        const colors = ['', '#ff6b6b', '#f5a623', '#5dd89c'];

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
