<?php
    // Incluir la conexión a la base de datos
    require_once '../config/database.php';

    // Obtener orígenes disponibles en la base de datos
    try {
        $stmt_origenes = $pdo->query("SELECT DISTINCT origen FROM omnibus WHERE ya_salio = 0 ORDER BY origen ASC");
        $origenes = $stmt_origenes->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $origenes = [];
    }

    // Respaldo por si la base de datos está vacía
    if (empty($origenes)) {
        $origenes = ['Montevideo', 'Paysandú', 'Salto'];
    }

    // Obtener destinos disponibles en la base de datos
    try {
        $stmt_destinos = $pdo->query("SELECT DISTINCT destino FROM omnibus WHERE ya_salio = 0 ORDER BY destino ASC");
        $destinos = $stmt_destinos->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $destinos = [];
    }

    if (empty($destinos)) {
        $destinos = ['Montevideo', 'Paysandú', 'Punta del Este', 'Salto'];
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio - Interurbanos</title>
    
    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../css/index_style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Contenedor principal -->
    <div class="padre">
        <!-- Botón para ir a la página de urbanos -->
        <div class="navegacion-paginas">
            <a href="../public/urbanos.php" class="btn-enlace">Ir a Ómnibus Urbanos →</a>
        </div>

        <h1>Encuentra tu próximo viaje Interurbano</h1>
        
        <!-- Formulario de búsqueda de viajes -->
        <div class="hijo">
            <form class="form" id="formBusqueda">
                <div class="options">
                    <label for="origen">Origen</label>
                    <select name="origen" id="origen" required>
                        <option value="" disabled selected hidden>Selecciona un origen</option>
                        <?php foreach ($origenes as $ori) { ?>
                            <option value="<?= htmlspecialchars($ori) ?>"><?= htmlspecialchars($ori) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="options">
                    <label for="destino">Destino</label>
                    <select name="destino" id="destino" required>
                        <option value="" disabled selected hidden>Selecciona un destino</option>
                        <?php foreach ($destinos as $des) { ?>
                            <option value="<?= htmlspecialchars($des) ?>"><?= htmlspecialchars($des) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="options">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" required>
                </div>

                <div class="options">
                    <label for="numPasajeros">Cantidad de pasajeros</label>
                    <input type="number" name="numPasajeros" id="numPasajeros" min="1" required>
                </div>

                <input type="submit" value="Buscar">
            </form>
        </div>

        <!-- Mensaje de error o información -->
        <p id="mensaje"></p>

        <!-- Contenedor para mostrar los resultados de la búsqueda -->
        <div class="horarios" id="contenedorHorarios" style="display:none;">
            <h2>Viajes encontrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Asientos Disp.</th>
                        <th>Agencia</th>
                        <th>Precio</th>
                        <th>Ruta</th>
                    </tr>
                </thead>
                <!-- Cuerpo de la tabla donde se mostrarán los resultados -->
                <tbody id="tablaHorarios"></tbody>
            </table>
        </div> <!-- Fin Contenedor de Resultados -->
    </div> <!-- Fin Contenedor Principal -->

    <!-- Contenedor de destinos populares -->
    <div class="destinos">
        <h1>Destinos Populares</h1>
        <div class="destinos_img">
            <img src="../img/placeholder.png" alt="Destino 1">
            <img src="../img/placeholder.png" alt="Destino 2">
            <img src="../img/placeholder.png" alt="Destino 3">
            <img src="../img/placeholder.png" alt="Destino 4">
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../js/scripts.js"></script>
</body>
</html>