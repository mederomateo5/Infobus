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

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

       body{
    margin: 0;

    background-image:
        linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
        url("img/fondo.jpg");

    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
        }

        .contenedor{
            max-width:900px;
            margin:30px auto;
            padding:20px;
        }

        h1{
            text-align:center;
            color:#1877f2;
            margin-bottom:25px;
        }

        .empresas{
            display:flex;
            flex-direction:column;
            gap:15px;
        }

        .empresa-btn{
    width:100%;
    border:none;
    background:white;
    border-radius:12px;
    padding:20px;

    display:flex;
    justify-content:center;
    align-items:center;

    cursor:pointer;
    box-shadow:0 2px 8px rgba(0,0,0,.1);
    transition:.2s;
}

        .empresa-btn:hover{
            transform:translateY(-2px);
        }

        .empresa-btn img{
    width:180px;
    height:120px;
    object-fit:contain;
    display:block;
}

        .empresa-btn span{
            font-size:20px;
            font-weight:bold;
            color:#333;
        }

        .formulario{
            display:none;
            background:white;
            padding:25px;
            border-radius:12px;
            box-shadow:0 2px 8px rgba(0,0,0,.1);
        }

        .campo{
            display:flex;
            flex-direction:column;
            margin-bottom:15px;
        }

        .campo label{
            margin-bottom:5px;
            font-weight:bold;
        }

        .campo input{
            padding:10px;
            border:1px solid #ccc;
            border-radius:6px;
        }

        .btn{
            background:#1877f2;
            color:white;
            border:none;
            padding:12px;
            border-radius:6px;
            cursor:pointer;
            font-weight:bold;
        }

        .btn:hover{
            background:#166fe5;
        }

        .empresa-seleccionada{
            text-align:center;
            color:#1877f2;
            margin-bottom:20px;
        }
        .logout-container {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 40px;
    padding-bottom: 30px;
}

.btn-logout {
    background: transparent;
    border: 2px solid red;
    color: red;
    font-weight: bold;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
    font-size: 16px;
}

.btn-logout:hover {
    background: red;
    color: white;
}
    </style>
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