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

// Mensaje de alta/baja (viene por GET desde alta_usuario.php / baja_usuario.php)
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>

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

        /* ---------- Estructura general con panel lateral ---------- */
        .layout{
            display:flex;
            min-height:calc(100vh);
            padding-top:78px; /* alto aproximado del navbar fijo del header */
        }

        .sidebar{
            width:230px;
            background:white;
            box-shadow:2px 0 8px rgba(0,0,0,.15);
            padding:20px 0;
            display:flex;
            flex-direction:column;
        }

        .sidebar h2{
            text-align:center;
            color:#1877f2;
            font-size:20px;
            margin-bottom:25px;
            padding:0 10px;
        }

        .sidebar button{
            border:none;
            background:none;
            text-align:left;
            padding:14px 25px;
            font-size:15px;
            font-weight:bold;
            color:#333;
            cursor:pointer;
            transition:.2s;
            border-left:4px solid transparent;
        }

        .sidebar button:hover{
            background:#f0f4ff;
            color:#1877f2;
        }

        .sidebar button.activo{
            background:#f0f4ff;
            color:#1877f2;
            border-left:4px solid #1877f2;
        }

        .sidebar .salir{
            margin-top:auto;
            color:#e0245e;
        }

        /* ---------- Contenido ---------- */
        .contenido{
            flex:1;
            padding:30px;
        }

        .tarjeta{
            background:white;
            border-radius:12px;
            padding:25px;
            box-shadow:0 2px 8px rgba(0,0,0,.1);
            margin-bottom:20px;
            display:none;
        }

        .tarjeta.activa{
            display:block;
        }

        h1{
            color:#1877f2;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th, td{
            text-align:left;
            padding:10px;
            border-bottom:1px solid #eee;
        }

        th{
            color:#1877f2;
        }

        .campo{
            display:flex;
            flex-direction:column;
            margin-bottom:15px;
            max-width:400px;
        }

        .campo label{
            margin-bottom:5px;
            font-weight:bold;
        }

        .campo input,
        .campo select{
            padding:10px;
            border:1px solid #ccc;
            border-radius:6px;
        }

        .btn{
            background:#1877f2;
            color:white;
            border:none;
            padding:12px 18px;
            border-radius:6px;
            cursor:pointer;
            font-weight:bold;
        }

        .btn:hover{
            background:#166fe5;
        }

        .btn-baja{
            background:#e0245e;
            color:white;
            border:none;
            padding:6px 12px;
            border-radius:6px;
            cursor:pointer;
            font-weight:bold;
        }

        .btn-baja:hover{
            background:#c01e4e;
        }

        .mensaje{
            background:#e6f4ea;
            color:#1e7e34;
            padding:12px 18px;
            border-radius:6px;
            margin-bottom:20px;
            font-weight:bold;
        }
    </style>
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
        <button class="menu-btn" onclick="mostrarSeccion('empresas', this)">Empresas</button>

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

        <!-- EMPRESAS -->
        <div class="tarjeta" id="empresas">
            <h1>Empresas</h1>
            <p>Acá podés agregar más adelante la gestión de las empresas de ómnibus (CUTCSA, COPSA, COME) que usan los choferes al enviar su ubicación.</p>
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
