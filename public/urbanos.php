<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Urbanos</title>
    <?php include '../includes/head.php'; ?>

    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>css/index_style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="padre">
        <!-- Botón para regresar al Index -->
        <div class="navegacion-paginas">
            <a href="../public/index.php" class="btn-enlace">← Volver a Interurbanos</a>
        </div>

        <h1>Ómnibus Urbanos</h1>

        <div class="hijo"> <!-- Formulario para asignar la ubicación de los urbanos -->
            <form class="form" id="formUbicacion">
                <div class="options" style="grid-column: 1 / 3;">
                    <label for="ubicacion">Selecciona tu ubicación</label>
                    <select id="ubicacion" required>
                        <option value="" disabled selected hidden>Selecciona una ciudad o localidad</option>
                        <option value="juan-lacaze">Juan Lacaze</option>
                        <option value="colonia">Colonia del Sacramento</option>
                        <option value="montevideo">Montevideo</option>
                    </select>
                </div>
            </form>
        </div>

        <p id="mensajeUrbano"></p> <!-- Mensaje que se mostrará si no hay horarios disponibles -->

        <!-- Tabla de resultados (Oculta al principio, aparece al elegir ubicación) -->
        <div class="horarios" id="resultadosUrbanos" style="display:none;">
            <h2>Líneas y Horarios Disponibles</h2>
            <table>
                <thead>
                    <tr>
                        <th>Salida</th>
                        <th>Llegada</th>
                        <th>Frecuencia</th>
                        <th>Empresa</th>
                        <th>Ver Ruta</th>
                    </tr>
                </thead>
                <tbody id="tablaHorariosUrbanos"></tbody>
            </table>
        </div>
    </div>

    <!-- JS de Leaflet y Script específico de Urbanos -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= BASE_URL ?>js/urbanos.js"></script>
</body>
</html>