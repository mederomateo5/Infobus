<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Chofer</title>
    <link rel="stylesheet" href="css/chofer.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="contenedor">

    <h1>Selecciona una empresa</h1> <!--poner padding que no se ve -->

    <div class="empresas" id="listaEmpresas">

        <button class="empresa-btn" onclick="mostrarFormulario('CUTCSA')">
            <img src="img/tab.png" alt="CUTCSA">
        </button>

        <button class="empresa-btn" onclick="mostrarFormulario('COPSA')">
            <img src="img/cot.png" alt="COPSA">
        </button>

        <button class="empresa-btn" onclick="mostrarFormulario('COME')">
            <img src="img/turil.svg" alt="COME">
        </button>

    </div>

    <div class="formulario" id="formulario">

        <h2 class="empresa-seleccionada" id="empresaElegida"></h2>

        <form action="procesar_ubicacion.php" method="POST" onsubmit="return confirmarEnvio()">

            <input type="hidden" name="empresa" id="empresaInput">

            <div class="campo">
                <label>Nombre</label>
                <input
                    type="text"
                    name="nombre"
                    value="<?= htmlspecialchars($_SESSION['user_name']) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label>Latitud</label>
                <input type="text" name="coordenada1" id="latitud" readonly required>
            </div>

            <div class="campo">
                <label>Longitud</label>
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
    
    </div>

</div>

<script>

function mostrarFormulario(nombreEmpresa){

    document.getElementById('listaEmpresas').style.display = 'none';

    document.getElementById('formulario').style.display = 'block';

    document.getElementById('empresaElegida').textContent =
        "Empresa seleccionada: " + nombreEmpresa;

    document.getElementById('empresaInput').value =
        nombreEmpresa;
}

function obtenerUbicacion(){

    if(!navigator.geolocation){
        alert("Tu navegador no soporta geolocalización.");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(posicion){

            document.getElementById('latitud').value =
                posicion.coords.latitude;

            document.getElementById('longitud').value =
                posicion.coords.longitude;
        },

        function(error){
            alert("No se pudo obtener la ubicación.");
            console.log(error);
        }
    );
}

function confirmarEnvio(){

    return confirm(
        "¿Estás seguro? Recuerda que debes contar con datos móviles."
    );
}

</script>
<div class="logout-container">
    <button class="btn-logout" onclick="window.location.href='logout.php'">
        Cerrar sesión
    </button>
</div>
</body>
</html>