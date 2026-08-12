 <?php
session_start();

// Verificar sesión y permisos de administrador
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
    die("Acceso denegado. Esta página es exclusiva para administradores.");
}

require_once '../config/database.php';
include '../includes/header.php';
include '../includes/sidebar.php';
?>

    
    <!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../public/css/panel_admin.css">
    
</head>

<body>

        


        <!-- Contenido principal -->
        <div class="contenido">



           

            <!-- ALTA DE USUARIO -->
            <div class="tarjeta" id="alta">
                <h1>Dar de alta un usuario</h1>
                <form action="../public/alta_usuario.php" method="POST">
                    <div class="campo">
                        <label>Nombre</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="campo">
                        <label>Apellido</label>
                        <input type="text" name="apellido" required>
                    </div>
                    <div class="campo">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="campo">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" required>
                    </div>
                    <div class="campo">
                        <label>Contraseña</label>
                        <input type="password" name="password" required minlength="6">
                    </div>
                    <div class="campo">
                        <label>Tipo de usuario</label>
                        <select name="tipo" required>
                            <option value="usuario">Usuario</option>
                            <option value="chofer">Chofer</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Dar de alta</button>
                </form>
            </div>

            <div id="resultadoBusqueda"></div>
            <!-- LISTADO DE VIAJES -->
            <div class="tarjeta" id="Viajes">
                <h1>Viajes registrados</h1>
                <div class="tabla-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-compacta">ID</th>
                                <th class="col-compacta">Num. Ómnibus</th>
                                <th class="col-compacta">Asientos</th>
                                <th class="col-compacta">Asientos Disp.</th>
                                <th class="col-texto">Origen</th>
                                <th class="col-texto">Destino</th>
                                <th class="col-compacta">Precio</th>
                                <th class="col-compacta">Agencia</th>
                                <th class="col-compacta">¿Ya salió?</th>
                                <th class="col-compacta">Fecha</th>
                                <th class="col-compacta">Hora</th>
                                <th class="col-compacta">Llegada</th>
                                <th class="col-compacta">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ALTA DE VIAJE -->
            <div class="tarjeta" id="altaV">
                <h1>Dar de alta un viaje</h1>

                <form action="../public/alta_viaje.php" method="POST" onsubmit="return validarViaje(this);">
                    <div class="campo">
                        <label>Número de Ómnibus</label>
                        <input type="number" name="n_omnibus" required min="1">
                    </div>

                    <div class="campo">
                        <label>Asientos Totales</label>
                        <input type="number" name="asientos" required min="1" max="70">
                    </div>

                    <div class="campo">
                        <label>Origen</label>
                        <input type="text" name="origen" required>
                    </div>

                    <div class="campo">
                        <label>Destino</label>
                        <input type="text" name="destino" required>
                    </div>

                    <div class="campo">
                        <label>Precio ($)</label>
                        <input type="number" step="0.01" name="precio" required min="0">
                    </div>

                    <div class="campo">
                        <label>Agencia</label>
                        <select name="agencia" required>
                            <option value="COT">COT</option>
                            <option value="TAB">TAB</option>
                            <option value="TURIL">TURIL</option>
                            <option value="URBANO">URBANO</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label>Fecha</label>
                        <input type="date" name="fecha" required min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="campo">
                        <label>Hora de Salida</label>
                        <input type="time" name="hora" required>
                    </div>

                    <div class="campo">
                        <label>Hora de Llegada</label>
                        <input type="time" name="hora_llegada" required>
                    </div>

                    <button type="submit" class="btn">Dar de alta viaje</button>
                </form>
            </div>
        </div>
    </div>

    <script>

        function validarViaje(form) {
            const origen = form.origen.value.trim().toLowerCase();
            const destino = form.destino.value.trim().toLowerCase();
            const fecha = form.fecha.value;
            const horaSalida = form.hora.value;
            const horaLlegada = form.hora_llegada.value;

            if (origen === destino) {
                alert("El origen y el destino no pueden ser el mismo.");
                return false;
            }

            // Fecha y hora local
            const ahora = new Date();
            const anio = ahora.getFullYear();
            const mes = String(ahora.getMonth() + 1).padStart(2, '0');
            const dia = String(ahora.getDate()).padStart(2, '0');
            const hoy = `${anio}-${mes}-${dia}`;

            const horasActuales = String(ahora.getHours()).padStart(2, '0');
            const minutosActuales = String(ahora.getMinutes()).padStart(2, '0');
            const horaActualStr = `${horasActuales}:${minutosActuales}`;

            if (fecha < hoy) {
                alert("No podés seleccionar una fecha del pasado.");
                return false;
            }

            if (fecha === hoy && horaSalida < horaActualStr) {
                alert("La hora de salida no puede ser anterior a la hora actual.");
                return false;
            }

            if (horaSalida === horaLlegada) {
                alert("La hora de salida y la de llegada no pueden ser iguales.");
                return false;
            }

            // Validar salidas mayores a llegadas solo a partir de las 23:00 hs
            const [hSalida, mSalida] = horaSalida.split(':').map(Number);
            const [hLlegada, mLlegada] = horaLlegada.split(':').map(Number);

            const minSalida = hSalida * 60 + mSalida;
            const minLlegada = hLlegada * 60 + mLlegada;

            if (minSalida > minLlegada) {
                if (hSalida < 23) {
                    alert("La hora de salida no puede ser posterior a la hora de llegada");
                    return false;
                }
            }

            return true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/manejarbuscador.js"></script>

</body>

</html>