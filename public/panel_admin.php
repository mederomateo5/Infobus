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

    // Consultar usuarios y viajes
    $stmt = $pdo->query("SELECT id, nombre, apellido, email, telefono, tipo FROM users ORDER BY id DESC");
    $usuarios = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, n_omnibus, asientos, asientos_disponibles, origen, destino, precio, agencia, ya_salio, fecha, hora, hora_llegada FROM omnibus ORDER BY id DESC");
    $viajes = $stmt->fetchAll();

    $mensaje = $_GET['msg'] ?? null;
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
    <?php include '../includes/header.php'; ?>

    <div class="layout">
        <!-- Panel lateral -->
        <div class="sidebar">
            <h2>Administrador</h2>
            <button class="menu-btn activo" onclick="mostrarSeccion('inicio', this)">Inicio</button>
            <button class="menu-btn" onclick="mostrarSeccion('usuarios', this)">Usuarios</button>
            <button class="menu-btn" onclick="mostrarSeccion('alta', this)">Alta de usuario</button>
            <button class="menu-btn" onclick="mostrarSeccion('Viajes', this)">Viajes</button>
            <button class="menu-btn" onclick="mostrarSeccion('altaV', this)">Alta de viaje</button>
            <button class="menu-btn salir" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>

        <!-- Contenido principal -->
        <div class="contenido">
            <?php if ($mensaje) { ?>
                <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
            <?php } ?>

            <!-- INICIO -->
            <div class="tarjeta activa" id="inicio">
                <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Administrador') ?></h1>
                <p>Desde este panel podés administrar los usuarios y viajes del sistema.</p>
            </div>

            <!-- LISTADO DE USUARIOS -->
            <div class="tarjeta" id="usuarios">
                <h1>Usuarios registrados</h1>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Tipo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)) { ?>
                            <tr><td colspan="7">No hay usuarios registrados.</td></tr>
                        <?php } ?>
                        <?php foreach ($usuarios as $u) { ?>
                            <tr>
                                <td><?= htmlspecialchars($u['id']) ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['apellido']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['telefono']) ?></td>
                                <td><?= htmlspecialchars($u['tipo']) ?></td>
                                <td>
                                    <?php if ($u['id'] != $_SESSION['user_id']) { ?>
                                        <form action="../public/baja_usuario.php" method="POST" onsubmit="return confirm('¿Seguro que querés dar de baja a este usuario?');" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
                                            <button type="submit" class="btn-baja">Dar de baja</button>
                                        </form>
                                    <?php } else { ?>
                                        <em>(vos)</em>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

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
                            <?php if (empty($viajes)) { ?>
                                <tr><td colspan="13" style="text-align:center;">No hay viajes registrados.</td></tr>
                            <?php } ?>
                            <?php foreach ($viajes as $v) { ?>
                                <tr>
                                    <td class="col-compacta"><?= htmlspecialchars($v['id']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['n_omnibus']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['asientos']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['asientos_disponibles']) ?></td>
                                    <td class="col-texto"><?= htmlspecialchars($v['origen']) ?></td>
                                    <td class="col-texto"><?= htmlspecialchars($v['destino']) ?></td>
                                    <td class="col-compacta">$<?= htmlspecialchars($v['precio']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['agencia']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['ya_salio'] ? 'Sí' : 'No') ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['fecha']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['hora']) ?></td>
                                    <td class="col-compacta"><?= htmlspecialchars($v['hora_llegada']) ?></td>
                                    <td class="col-compacta">
                                        <div class="acciones-cell">
                                            <a href="../public/editar_viaje.php?id=<?= htmlspecialchars($v['id']) ?>" class="btn-editar">Editar</a>
                                            <form action="../public/baja_viaje.php" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar este viaje?');">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($v['id']) ?>">
                                                <button type="submit" class="btn-baja">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

    <script>
        function mostrarSeccion(id, boton) {
            document.querySelectorAll('.tarjeta').forEach(function(tarjeta) {
                tarjeta.classList.remove('activa');
            });
            document.getElementById(id).classList.add('activa');

            document.querySelectorAll('.menu-btn').forEach(function(btn) {
                btn.classList.remove('activo');
            });
            boton.classList.add('activo');
        }

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

            // Obtener fecha y hora actual local del navegador
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

            // Validar restricción: hora de salida > hora de llegada solo si salida >= 23:00
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
</body>
</html>