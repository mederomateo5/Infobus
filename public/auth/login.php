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

$userObj    = new User($pdo);
$loginError = '';
$regErrors  = [];
$activeTab  = 'login';

// ----------------------------------------------------------------
//  POST: Login
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $activeTab = 'login';
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/@gmail\.com$/i', $email)) {
        $loginError = 'Solo se permiten correos de Gmail (@gmail.com).';
    } else {
        $loggedUser = $userObj->login($email, $password);

        if ($loggedUser) {
            if (DosFactores::iniciar($loggedUser)) {
                header('Location: verificar_codigo.php');
                exit();
            }
            $loginError = 'No pudimos enviar el código a tu correo. Probá de nuevo en unos minutos.';
        } else {
            $loginError = 'Email o contraseña incorrectos.';
        }
    }
}

// ----------------------------------------------------------------
//  POST: Registro (Modificado: NO guarda en BD hasta verificar código)
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $activeTab  = 'register';
    $email = trim($_POST['email'] ?? '');

    if (!preg_match('/@gmail\.com$/i', $email)) {
        $regErrors['email'] = 'El correo electrónico debe ser obligatoriamente de Gmail (@gmail.com).';
    } else {
        $regErrors = $userObj->validateRegister($_POST);

        if (empty($regErrors)) {
            // En lugar de registrar directo, iniciamos el 2FA de registro pendiente
            if (DosFactores::iniciarRegistro($_POST)) {
                header('Location: verificar_codigo.php');
                exit();
            }
            $regErrors[] = 'No pudimos enviar el código de verificación a tu correo. Probá más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Acceso</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/login.css">
    <?php include '../../private/includes/head.php'; ?>
</head>

<body>

    <?php include '../../private/includes/header.php'; ?>

    <div class="container py-4 py-md-5">
        <main class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border rounded-3 p-3 p-md-4 bg-body">

                    <!-- Logo -->
                    <div class="text-center mb-3">
                        <img
                            src="../assets/img/logotipo.png"
                            alt="Logo InfoBus"
                            class="img-fluid"
                            style="max-width: 160px;"
                        >
                    </div>

                    <!-- Tabs -->
                    <div class="d-flex border-bottom mb-4" role="tablist">
                        <button
                            class="tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>"
                            role="tab"
                            onclick="switchTab('login')"
                        >
                            Iniciar sesión
                        </button>

                        <button
                            class="tab-btn <?= $activeTab === 'register' ? 'active' : '' ?>"
                            role="tab"
                            onclick="switchTab('register')"
                        >
                            Registrarse
                        </button>
                    </div>

                    <!-- LOGIN -->
                    <section
                        class="form-section <?= $activeTab === 'login' ? 'active' : '' ?>"
                        id="tab-login"
                    >
                        <h1 class="h4 fw-bold mb-1 text-body">
                            Bienvenido de vuelta
                        </h1>
                        <p class="text-secondary mb-4">
                            Ingresá con tu cuenta para continuar.
                        </p>

                        <?php if ($loginError) { ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($loginError) ?>
                            </div>
                        <?php } ?>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>auth/login.php"
                            novalidate
                        >
                            <input
                                type="hidden"
                                name="action"
                                value="login"
                            >

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="login-email" class="form-label fw-bold text-body">
                                    Gmail
                                    <span class="info-icon" title="Ingresá tu dirección de correo electrónico">i</span>
                                </label>
                                <input
                                    type="email"
                                    id="login-email"
                                    name="email"
                                    class="form-control"
                                    placeholder="tu@email.com"
                                    required
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                >
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="login-password" class="form-label fw-bold text-body">
                                    Contraseña
                                    <span class="info-icon" title="Tu contraseña de acceso">i</span>
                                </label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        id="login-password"
                                        name="password"
                                        class="form-control"
                                        placeholder="••••••••"
                                        required
                                    >
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('login-password', this)" tabindex="-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <a href="recuperar.php" class="text-decoration-underline text-secondary small">Olvidé mi contraseña</a>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100 mt-2"
                            >
                                Ingresar →
                            </button>
                        </form>
                    </section>

                    <!-- REGISTRO -->
                    <section
                        class="form-section <?= $activeTab === 'register' ? 'active' : '' ?>"
                        id="tab-register"
                    >
                        <h1 class="h4 fw-bold mb-1 text-body">
                            Crear cuenta
                        </h1>
                        <p class="text-secondary mb-4">
                            Completá tus datos para registrarte.
                        </p>

                        <form
                            method="POST"
                            action="<?= BASE_URL ?>auth/login.php"
                            novalidate
                        >
                            <input
                                type="hidden"
                                name="action"
                                value="register"
                            >

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="reg-nombre" class="form-label fw-bold text-body">
                                            Nombre
                                            <span class="info-icon" title="Tu nombre">i</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="reg-nombre"
                                            name="nombre"
                                            class="form-control <?= isset($regErrors['nombre']) ? 'is-invalid' : '' ?>"
                                            placeholder="Juan"
                                            value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                        >
                                        <?php if (isset($regErrors['nombre'])) { ?>
                                            <div class="invalid-feedback">
                                                <?= $regErrors['nombre'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="mb-3">
                                        <label for="reg-apellido" class="form-label fw-bold text-body">
                                            Apellido
                                            <span class="info-icon" title="Tu apellido">i</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="reg-apellido"
                                            name="apellido"
                                            class="form-control <?= isset($regErrors['apellido']) ? 'is-invalid' : '' ?>"
                                            placeholder="García"
                                            value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>"
                                        >
                                        <?php if (isset($regErrors['apellido'])) { ?>
                                            <div class="invalid-feedback">
                                                <?= $regErrors['apellido'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reg-email" class="form-label fw-bold text-body">
                                    Gmail
                                    <span class="info-icon" title="Tu dirección de correo electrónico de Google">i</span>
                                </label>
                                <input
                                    type="email"
                                    id="reg-email"
                                    name="email"
                                    class="form-control <?= isset($regErrors['email']) ? 'is-invalid' : '' ?>"
                                    placeholder="tu@gmail.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                >
                                <?php if (isset($regErrors['email'])) { ?>
                                    <div class="invalid-feedback">
                                        <?= $regErrors['email'] ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="reg-telefono" class="form-label fw-bold text-body">
                                    Teléfono
                                    <span class="info-icon" title="Número de teléfono, solo dígitos. Ej: 099123456">i</span>
                                </label>
                                <input
                                    type="tel"
                                    id="reg-telefono"
                                    name="telefono"
                                    class="form-control <?= isset($regErrors['telefono']) ? 'is-invalid' : '' ?>"
                                    placeholder="099 123 456"
                                    value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                >
                                <?php if (isset($regErrors['telefono'])) { ?>
                                    <div class="invalid-feedback">
                                        <?= $regErrors['telefono'] ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="reg-password" class="form-label fw-bold text-body">
                                    Contraseña
                                    <span class="info-icon" title="Mínimo 8 caracteres, una mayúscula y un símbolo especial ( ! @ # % * )">i</span>
                                </label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        id="reg-password"
                                        name="password"
                                        class="form-control <?= isset($regErrors['password']) ? 'is-invalid' : '' ?>"
                                        placeholder="••••••••"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('reg-password', this)" tabindex="-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($regErrors['password'])) { ?>
                                    <div class="text-danger small mt-1">
                                        <?= $regErrors['password'] ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label for="reg-confirm" class="form-label fw-bold text-body">
                                    Confirmar contraseña
                                    <span class="info-icon" title="Repetí la contraseña para confirmarla">i</span>
                                </label>
                                <div class="input-group">
                                    <input
                                        type="password"
                                        id="reg-confirm"
                                        name="confirm_password"
                                        class="form-control <?= isset($regErrors['confirm_password']) ? 'is-invalid' : '' ?>"
                                        placeholder="••••••••"
                                    >
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('reg-confirm', this)" tabindex="-1">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <?php if (isset($regErrors['confirm_password'])) { ?>
                                    <div class="invalid-feedback">
                                        <?= $regErrors['confirm_password'] ?>
                                    </div>
                                <?php } ?>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100 mt-2"
                            >
                                Crear cuenta →
                            </button>
                        </form>
                    </section>

                </div>
            </div>
        </main>
    </div>
        
    <script src="../assets/js/login.js"></script>
</body>
</html>