<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

// Verificar que sea chofer
if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'chofer') {
    die("Acceso denegado. Esta página es exclusiva para choferes.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Panel de Chofer</title>
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="../public/css/chofer.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="contenedor">
        <br><br><br>
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
        <h1>Selecciona una empresa</h1>

        <!-- Contenedor de empresas y formulario -->
        <div class="empresas" id="listaEmpresas">
            <button class="empresa-btn" onclick="mostrarFormulario('TAB')">
                <img src="../public/img/tab.png" alt="TAB">
            </button>

            <button class="empresa-btn" onclick="mostrarFormulario('COT')">
                <img src="../public/img/cot.png" alt="COT">
            </button>

            <button class="empresa-btn" onclick="mostrarFormulario('TURIL')">
                <img src="../public/img/turil.svg" alt="TURIL">
            </button>
        </div>

        <!-- Formulario para enviar ubicación -->
        <div class="formulario" id="formulario">
            <h2 class="empresa-seleccionada" id="empresaElegida"></h2>

            <!-- Formulario que envía la ubicación al servidor -->
            <form action="../public/procesar_ubicacion.php" method="POST" onsubmit="return confirmarEnvio()">
                <input type="hidden" name="empresa" id="empresaInput">

                <!-- Campo para mostrar el nombre del chofer -->
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="coordenada1" id="latitud" readonly required>
                </div>

                <!-- Campos para mostrar latitud y longitud -->
                <div class="campo">
                    <label>Latitud</label>
                    <input type="text" name="coordenada2" id="longitud" readonly required>
                </div>

                <button type="button" class="btn" onclick="obtenerUbicacion()">
                    Obtener ubicación
                </button>

                <br><br>

                <button type="submit" class="btn">
                    Enviar
                </button>
            </form>
        </div> <!-- Fin Formulario -->
    </div> <!-- Fin Contenedor -->

    <!-- Script para manejar la selección de empresa y la geolocalización -->
    <script>
        // Función para mostrar el formulario y ocultar la lista de empresas
        function mostrarFormulario(nombreEmpresa) {
            document.getElementById('listaEmpresas').style.display = 'none';
            document.getElementById('formulario').style.display = 'block';
            document.getElementById('empresaElegida').textContent = "Empresa seleccionada: " + nombreEmpresa;
            document.getElementById('empresaInput').value = nombreEmpresa;
        }

        // Función para obtener la ubicación del chofer usando la API de Geolocalización
        function obtenerUbicacion() {
            if (!navigator.geolocation) {
                alert("Tu navegador no soporta geolocalización.");
                return;
            }

            // Solicitar la ubicación actual
            navigator.geolocation.getCurrentPosition(
                function(posicion) {
                    document.getElementById('latitud').value = posicion.coords.latitude;
                    document.getElementById('longitud').value = posicion.coords.longitude;
                },

                function(error) {
                    alert("No se pudo obtener la ubicación.");
                    console.log(error);
                }
            );
        }

        function confirmarEnvio() {
            return confirm(
                "¿Estás seguro? Recuerda que debes contar con datos móviles."
            );
        }
    </script>

    <!-- Botón de cerrar sesión -->
    <div class="logout-container">
        <button class="btn-logout" onclick="window.location.href='../public/logout.php'">
            Cerrar sesión
        </button>
    </div>
</body>
</html>