<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
    <link rel="stylesheet" href="css/panel_admin.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="layout">

    <!-- ---------- Panel lateral ---------- -->
    <div class="sidebar">
        <h2>Administrador</h2>

        <button class="menu-btn activo" onclick="mostrarSeccion('inicio', this)">Inicio</button>
        <button class="menu-btn" onclick="mostrarSeccion('usuarios', this)">Usuarios</button>
        <button class="menu-btn" onclick="mostrarSeccion('alta', this)">Alta de usuario</button>
        <button class="menu-btn" onclick="mostrarSeccion('Viajes', this)">Viajes</button>

        <button class="menu-btn salir" onclick="window.location.href='logout.php'">Cerrar sesión</button>
    </div>

    <!-- ---------- Contenido ---------- -->
    <div class="contenido">

        <?php if ($mensaje): ?>
            <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <!-- INICIO -->
        <div class="tarjeta activa" id="inicio">
            <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
            <p>Desde este panel podés administrar los usuarios del sistema: darlos de alta, darlos de baja y consultar el listado general.</p>
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

                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['apellido']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['telefono']) ?></td>
                    <td><?= htmlspecialchars($u['tipo']) ?></td>
                    <td>
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <form action="baja_usuario.php" method="POST" onsubmit="return confirm('¿Seguro que querés dar de baja a este usuario?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
                            <button type="submit" class="btn-baja">Dar de baja</button>
                        </form>
                        <?php else: ?>
                            <em>(vos)</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- ALTA DE USUARIO -->
        <div class="tarjeta" id="alta">
            <h1>Dar de alta un usuario</h1>

            <form action="alta_usuario.php" method="POST">

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

        <!-- Viajes -->
        
           <!-- LISTADO DE VIAJES -->
            
        <div class="tarjeta" id="Viajes">
            <h1>Usuarios registrados</h1>

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
                </tr>

                <?php foreach ($viajes as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['id']) ?></td>
                    <td><?= htmlspecialchars($v['n_omnibus']) ?></td>
                    <td><?= htmlspecialchars($v['asientos']) ?></td>
                    <td><?= htmlspecialchars($v['asientos_disponibles']) ?></td>
                    <td><?= htmlspecialchars($v['origen']) ?></td>
                    <td><?= htmlspecialchars($v['destino']) ?></td>
                    <td><?= htmlspecialchars($v['precio']) ?></td>
                    <td><?= htmlspecialchars($v['agencia']) ?></td>
                    <td><?= htmlspecialchars($v['ya_salio']) ?></td>
                    <td><?= htmlspecialchars($v['fecha']) ?></td>
                    <td><?= htmlspecialchars($v['hora']) ?></td>
                    <td><?= htmlspecialchars($v['hora_llegada']) ?></td>
                    <td>
                        <?php if ($v['id'] != $_SESSION['user_id']): ?>
                        <form action="baja_usuario.php" method="POST" onsubmit="return confirm('¿Seguro que querés dar de baja a este usuario?');" style="display:inline;">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
                            <button type="submit" class="btn-baja">Dar de baja</button>
                        </form>
                        <?php else: ?> <!---aca falta cambiar que se de de baja un omniibus en vez de usuarios-->
                            <em>(vos)</em>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- ALTA DE VIAJE -->
        <div class="tarjeta" id="altaV">
            <h1>Dar de alta un usuario</h1>

            <form action="alta_viaje.php" method="POST">

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

    </div>

</div>

<script>

function mostrarSeccion(id, boton){

    // Ocultar todas las tarjetas
    document.querySelectorAll('.tarjeta').forEach(function(tarjeta){
        tarjeta.classList.remove('activa');
    });

    // Mostrar la elegida
    document.getElementById(id).classList.add('activa');

    // Marcar botón activo en el menú
    document.querySelectorAll('.menu-btn').forEach(function(btn){
        btn.classList.remove('activo');
    });
    boton.classList.add('activo');
}

</script>

</body>
</html>
