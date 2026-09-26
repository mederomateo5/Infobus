<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urbanos</title>
    <?php include '../includes/head.php'; ?>

    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<style> 
body {
background-image:
        linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
        url("../public/img/fondo.jpg");
    background-repeat:no-repeat;
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
}
</style>

<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container py-4" style="max-width: 1100px;">
        <!-- Botón para regresar al Index -->
        <div class="d-flex justify-content-start mb-3">
            <a href="../public/index.php" class="btn btn-primary fw-semibold">&larr; Volver a Interurbanos</a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h1 class="h3 mb-4 text-dark fw-bold text-center">Ómnibus Urbanos</h1>

                <form id="formUbicacion">
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label fw-semibold">Selecciona tu ubicación</label>
                        <select id="ubicacion" class="form-select" required>
                            <option value="" disabled selected hidden>Selecciona una ciudad o localidad</option>
                            <option value="juan-lacaze">Juan Lacaze</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <p id="mensajeUrbano" class="text-danger text-center fw-bold"></p>

        <!-- Contenedor del Mapa -->
        <div id="mapa" class="rounded shadow-sm mb-4" style="height: 450px; width: 100%; display: none;"></div>

        <!-- Tabla de resultados -->
        <div class="card shadow-sm border-0 mb-4" id="resultadosUrbanos" style="display:none;">
            <div class="card-body p-4">
                <h2 class="h4 mb-3 text-primary fw-bold">Líneas y Horarios Disponibles</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary text-center">
                            <tr class="text-center">
                                <th>Salida</th>
                                <th>Llegada</th>
                                <th>Estimado</th>
                                <th>Frecuencia</th>
                                <th>Ver Ruta</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHorariosUrbanos"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- JS de Leaflet y Script específico de Urbanos -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= BASE_URL ?>js/urbanos.js"></script>
</body>
</html>