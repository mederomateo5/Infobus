<?php
require_once '../config/config.php';
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

    <title>Acceso</title>

        <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
        
    <?php include '../includes/head.php'; ?>



</head>

<body>

    <?php include '../includes/header.php'; ?>


    <div class="container py-4 py-md-5">

        <main class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-6 col-xl-5">

                <div class="card shadow-sm border rounded-3 p-3 p-md-4">


                    <!-- Logo -->

                    <div class="text-center mb-3">

                        <img
                            src="../public/img/logotipo.png"
                            alt="Logo InfoBus"
                            class="img-fluid"
                            style="max-width: 160px;"
                        >

                    </div>


                    <!-- Tabs -->

                    <div
                        class="d-flex border-bottom mb-4"
                        role="tablist"
                    >

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

                        <h1 class="h4 fw-bold mb-1">
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
                            action="<?= BASE_URL ?>login.php"
                            novalidate
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="login"
                            >


                            <!-- Email -->

                            <div class="mb-3">

                                <label
                                    for="login-email"
                                    class="form-label fw-bold"
                                >
                                    Email

                                    <span
                                        class="info-icon"
                                        title="Ingresá tu dirección de correo electrónico"
                                    >
                                        i
                                    </span>

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

                                <label
                                    for="login-password"
                                    class="form-label fw-bold"
                                >
                                    Contraseña

                                    <span
                                        class="info-icon"
                                        title="Tu contraseña de acceso"
                                    >
                                        i
                                    </span>

                                </label>


                                <input
                                    type="password"
                                    id="login-password"
                                    name="password"
                                    class="form-control"
                                    placeholder="••••••••"
                                    required
                                >

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

                        <h1 class="h4 fw-bold mb-1">
                            Crear cuenta
                        </h1>

                        <p class="text-secondary mb-4">
                            Completá tus datos para registrarte.
                        </p>


                        <form
                            method="POST"
                            action="<?= BASE_URL ?>login.php"
                            novalidate
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="register"
                            >


                            <!-- Nombre y apellido -->

                            <div class="row">

                                <div class="col-12 col-md-6">

                                    <div class="mb-3">

                                        <label
                                            for="reg-nombre"
                                            class="form-label fw-bold"
                                        >
                                            Nombre

                                            <span
                                                class="info-icon"
                                                title="Tu nombre"
                                            >
                                                i
                                            </span>

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

                                        <label
                                            for="reg-apellido"
                                            class="form-label fw-bold"
                                        >
                                            Apellido

                                            <span
                                                class="info-icon"
                                                title="Tu apellido"
                                            >
                                                i
                                            </span>

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


                            <!-- Email -->

                            <div class="mb-3">

                                <label
                                    for="reg-email"
                                    class="form-label fw-bold"
                                >
                                    Email

                                    <span
                                        class="info-icon"
                                        title="Tu dirección de correo electrónico"
                                    >
                                        i
                                    </span>

                                </label>


                                <input
                                    type="email"
                                    id="reg-email"
                                    name="email"
                                    class="form-control <?= isset($regErrors['email']) ? 'is-invalid' : '' ?>"
                                    placeholder="tu@email.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                >


                                <?php if (isset($regErrors['email'])) { ?>

                                    <div class="invalid-feedback">
                                        <?= $regErrors['email'] ?>
                                    </div>

                                <?php } ?>

                            </div>


                            <!-- Teléfono -->

                            <div class="mb-3">

                                <label
                                    for="reg-telefono"
                                    class="form-label fw-bold"
                                >
                                    Teléfono

                                    <span
                                        class="info-icon"
                                        title="Número de teléfono, solo dígitos. Ej: 099123456"
                                    >
                                        i
                                    </span>

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


                            <!-- Contraseña -->

                            <div class="mb-3">

                                <label
                                    for="reg-password"
                                    class="form-label fw-bold"
                                >
                                    Contraseña

                                    <span
                                        class="info-icon"
                                        title="Mínimo 8 caracteres, una mayúscula y un símbolo especial"
                                    >
                                        i
                                    </span>

                                </label>


                                <input
                                    type="password"
                                    id="reg-password"
                                    name="password"
                                    class="form-control <?= isset($regErrors['password']) ? 'is-invalid' : '' ?>"
                                    placeholder="••••••••"
                                    oninput="updateStrength(this.value)"
                                >


                                <div class="d-flex gap-1 mt-2">

                                    <div
                                        class="strength-bar flex-fill"
                                        id="bar1"
                                    ></div>

                                    <div
                                        class="strength-bar flex-fill"
                                        id="bar2"
                                    ></div>

                                    <div
                                        class="strength-bar flex-fill"
                                        id="bar3"
                                    ></div>

                                </div>


                                <span
                                    class="strength-label d-block mt-1"
                                    id="strength-label"
                                ></span>


                                <?php if (isset($regErrors['password'])) { ?>

                                    <div class="text-danger small">
                                        <?= $regErrors['password'] ?>
                                    </div>

                                <?php } ?>

                            </div>


                            <!-- Confirmar contraseña -->

                            <div class="mb-3">

                                <label
                                    for="reg-confirm"
                                    class="form-label fw-bold"
                                >
                                    Confirmar contraseña

                                    <span
                                        class="info-icon"
                                        title="Repetí la contraseña para confirmarla"
                                    >
                                        i
                                    </span>

                                </label>


                                <input
                                    type="password"
                                    id="reg-confirm"
                                    name="confirm_password"
                                    class="form-control <?= isset($regErrors['confirm_password']) ? 'is-invalid' : '' ?>"
                                    placeholder="••••••••"
                                >


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