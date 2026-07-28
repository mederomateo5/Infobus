<?php
    session_start();

    // Verificar sesión y rol de administrador
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'administrador') {
        die("Acceso denegado.");
    }

    require_once '../config/database.php';

    // Validar ID recibido por GET
    $id = $_GET['id'] ?? null;
    if (!$id || !ctype_digit((string)$id)) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('ID de viaje no válido.'));
        exit();
    }

    // Obtener los datos del viaje
    try {
        $stmt = $pdo->prepare("SELECT * FROM omnibus WHERE id = ?");
        $stmt->execute([$id]);
        $viaje = $stmt->fetch();

        if (!$viaje) {
            header('Location: ../public/panel_admin.php?msg=' . urlencode('El viaje no existe.'));
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../public/panel_admin.php?msg=' . urlencode('Error al conectar con la base de datos.'));
        exit();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Viaje</title>
    <!-- Reutilizamos tu CSS de administración -->
    <link rel="stylesheet" href="../public/css/panel_admin.css">
    <style>
        /* Ajuste rápido para centrar el formulario de edición en pantalla */
        .contenedor-editar {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }
        .btn-volver {
            background-color: #ffffff;
            color: #1877f2;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .btn-volver:hover {
            background-color: #d1d1d1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contenedor-editar {
                max-width: 100%;
                margin: 20px auto;
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .contenedor-editar {
                margin: 10px auto;
                padding: 10px;
            }

            .btn-volver {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <br><br><br>
    <div class="contenedor-editar">
        <a href="panel_admin.php" class="btn-volver">← Volver al Panel</a>

        <div class="tarjeta activa">
            
            <h1>Editar Viaje (ID: <?= htmlspecialchars($viaje['id']) ?>)</h1>

            <form action="../public/actualizar_viaje.php" method="POST">
                <!-- ID Oculto para procesar la actualización en la BD -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($viaje['id']) ?>">

                <div class="campo">
                    <label>Número de Ómnibus</label>
                    <input type="number" name="n_omnibus" value="<?= htmlspecialchars($viaje['n_omnibus']) ?>" required min="1">
                </div>

                <div class="campo">
                    <label>Asientos Totales</label>
                    <!-- Nota: si cambias la capacidad total de asientos, es buena idea actualizar la disponibilidad -->
                    <input type="number" name="asientos" value="<?= htmlspecialchars($viaje['asientos']) ?>" required min="1">
                </div>

                <div class="campo">
                    <label>Asientos Disponibles</label>
                    <input type="number" name="asientos_disponibles" value="<?= htmlspecialchars($viaje['asientos_disponibles']) ?>" required min="0">
                </div>

                <div class="campo">
                    <label>Origen</label>
                    <input type="text" name="origen" value="<?= htmlspecialchars($viaje['origen']) ?>" required>
                </div>

                <div class="campo">
                    <label>Destino</label>
                    <input type="text" name="destino" value="<?= htmlspecialchars($viaje['destino']) ?>" required>
                </div>

                <div class="campo">
                    <label>Precio</label>
                    <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($viaje['precio']) ?>" required min="0">
                </div>

                <div class="campo">
                    <label>Agencia</label>
                    <select name="agencia" required>
                        <option value="COT" <?= $viaje['agencia'] === 'COT' ? 'selected' : '' ?>>COT</option>
                        <option value="TAB" <?= $viaje['agencia'] === 'TAB' ? 'selected' : '' ?>>TAB</option>
                        <option value="TURIL" <?= $viaje['agencia'] === 'TURIL' ? 'selected' : '' ?>>TURIL</option>
                        <option value="URBANO" <?= $viaje['agencia'] === 'URBANO' ? 'selected' : '' ?>>URBANO</option>
                    </select>
                </div>

                <div class="campo">
                    <label>¿Ya salió?</label>
                    <select name="ya_salio" required>
                        <option value="0" <?= $viaje['ya_salio'] == 0 ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= $viaje['ya_salio'] == 1 ? 'selected' : '' ?>>Sí</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Fecha</label>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($viaje['fecha']) ?>" required>
                </div>

                <div class="campo">
                    <label>Hora de Salida</label>
                    <input type="time" name="hora" value="<?= htmlspecialchars($viaje['hora']) ?>" required>
                </div>

                <div class="campo">
                    <label>Hora de Llegada</label>
                    <input type="time" name="hora_llegada" value="<?= htmlspecialchars($viaje['hora_llegada']) ?>" required>
                </div>

                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>