<?php
    session_start();

    // Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../public/login.php');
        exit();
    }

    // Verificar que sea administrador
    if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado. Esta página es exclusiva para administradores.");
    }

    // Conexión a la base de datos
    require_once '../config/database.php';

    // ----------------------------------------------------
    // Traer lista de usuarios para mostrarla en el panel
    // ----------------------------------------------------
    $stmt = $pdo->query("SELECT id, nombre, apellido, email, telefono, tipo FROM users ORDER BY id DESC");
    $usuarios = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id,n_omnibus,asientos,asientos_disponibles,origen,destino,precio,agencia,ya_salio,fecha,hora,hora_llegada FROM omnibus ORDER BY id DESC");
    $viajes = $stmt->fetchAll();

    // Mensaje de alta/baja (viene por GET desde alta_usuario.php / baja_usuario.php)
    $mensaje = isset($_GET['msg']) ? $_GET['msg'] : null;
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
        <!-- ---------- Panel lateral ---------- -->
        <div class="sidebar">
            <h2>Administrador</h2>

            <!-- Botones del menú lateral -->
            <button class="menu-btn activo" onclick="mostrarSeccion('inicio', this)">Inicio</button>
            <button class="menu-btn" onclick="mostrarSeccion('usuarios', this)">Usuarios</button>
            <button class="menu-btn" onclick="mostrarSeccion('alta', this)">Alta de usuario</button>
            <button class="menu-btn" onclick="mostrarSeccion('Viajes', this)">Viajes</button>
            <button class="menu-btn" onclick="mostrarSeccion('altaV', this)">Alta de viaje</button>

            <button class="menu-btn salir" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>

        <!-- ---------- Contenido ---------- -->
        <div class="contenido">
            <!-- Mostrar mensaje si existe -->
            <?php if ($mensaje) { ?>
                <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
            <?php } ?>

            <!-- INICIO -->
            <div class="tarjeta activa" id="inicio">
                <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
                <p>
                    Desde este panel podés administrar los usuarios del sistema: darlos de alta, 
                    darlos de baja y consultar el listado general.
                </p>
            </div>

            <!-- LISTADO DE USUARIOS -->
            <div class="tarjeta" id="usuarios">
                <h1>Usuarios registrados</h1>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th>Acción</th>
                    </tr>

                    <!-- Mostrar mensaje si no hay usuarios -->
                    <?php if (empty($usuarios)) { ?>
                        <tr>
                            <td colspan="7">No hay usuarios registrados.</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($usuarios as $u) { ?>
                        <!-- Mostrar cada usuario en una fila de la tabla -->
                        <tr>
                            <td><?= htmlspecialchars($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td><?= htmlspecialchars($u['apellido']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['telefono']) ?></td>
                            <td><?= htmlspecialchars($u['tipo']) ?></td>

                            <!-- Botón de baja solo si el usuario no es el mismo que está logueado -->
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

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Num. de Omnibus</th>
                        <th>Asientos</th>
                        <th>Asientos Disp.</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Precio</th>
                        <th>Agencia</th>
                        <th>Ya salio?</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Hora de llegada</th>
                        <th>Acciones</th>
                    </tr>

                    <!-- Mostrar mensaje si no hay viajes -->
                    <?php if (empty($viajes)) { ?>
                        <tr>
                            <td colspan="13">No hay viajes registrados.</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($viajes as $v) { ?>
                        <tr>
                            <td><?= htmlspecialchars($v['id']) ?></td>
                            <td><?= htmlspecialchars($v['n_omnibus']) ?></td>
                            <td><?= htmlspecialchars($v['asientos']) ?></td>
                            <td><?= htmlspecialchars($v['asientos_disponibles']) ?></td>
                            <td><?= htmlspecialchars($v['origen']) ?></td>
                            <td><?= htmlspecialchars($v['destino']) ?></td>
                            <td><?= htmlspecialchars($v['precio']) ?></td>
                            <td><?= htmlspecialchars($v['agencia']) ?></td>
                            <td><?= htmlspecialchars($v['ya_salio'] ? 'Sí' : 'No') ?></td>
                            <td><?= htmlspecialchars($v['fecha']) ?></td>
                            <td><?= htmlspecialchars($v['hora']) ?></td>
                            <td><?= htmlspecialchars($v['hora_llegada']) ?></td>
                            <td>
    <!-- Botón de Editar (Redirige a la nueva pantalla pasando el ID por GET) -->
    <a href="../public/editar_viaje.php?id=<?= htmlspecialchars($v['id']) ?>" class="btn-editar">Editar</a>

    <!-- Botón de baja de viaje -->
    <form action="../public/baja_viaje.php" method="POST" onsubmit="return confirm('¿Seguro que querés eliminar este viaje?');" style="display:inline;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($v['id']) ?>">
        <button type="submit" class="btn-baja">Eliminar</button>
    </form>
</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <!-- ALTA DE VIAJE -->
            <div class="tarjeta" id="altaV">
                <h1>Dar de alta un viaje</h1>

                <form action="../public/alta_viaje.php" method="POST">
                    <div class="campo">
                        <label>Número de Ómnibus</label>
                        <input type="number" name="n_omnibus" required min="1">
                    </div>

                    <div class="campo">
                        <label>Asientos Totales</label>
                        <input type="number" name="asientos" required min="1">
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
                        <label>Precio</label>
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
                        <input type="date" name="fecha" required>
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
        </div> <!-- Fin Contenido -->
    </div> <!-- Fin Layout -->

    <script>
        function mostrarSeccion(id, boton) {
            // Ocultar todas las tarjetas
            document.querySelectorAll('.tarjeta').forEach(function(tarjeta) {
                tarjeta.classList.remove('activa');
            });

            // Mostrar la elegida
            document.getElementById(id).classList.add('activa');

            // Marcar botón activo en el menú
            document.querySelectorAll('.menu-btn').forEach(function(btn) {
                btn.classList.remove('activo');
            });
            boton.classList.add('activo');
        }
    </script>
</body>
</html>
