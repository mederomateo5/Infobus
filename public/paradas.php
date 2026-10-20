<?php require_once '../private/config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver paradas</title>
    <?php include '../private/includes/head.php'; ?>

    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<style>
    body {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            url("assets/img/fondo.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>

<body class="bg-light">
    <?php include '../private/includes/header.php'; ?>

    <div class="container py-4" style="max-width: 1100px;">
        <!-- Botón para regresar -->
        <div class="d-flex justify-content-start mb-3">
            <a href="index.php" class="btn btn-primary fw-semibold">&larr; Volver a Interurbanos</a>
        </div>

        <!-- Tarjeta principal que contiene el título, el botón y el mapa -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 text-center">
                <h1 class="h3 mb-4 text-dark fw-bold">Paradas Cercanas</h1>
                
                <!-- Botón y su etiqueta centrados -->
                <div class="d-flex flex-column align-items-center mb-4">
                    <label for="btnUbicacion" class="form-label fw-semibold mb-2">Selecciona tu ubicación</label>
                    <button type="button" id="btnUbicacion" class="btn btn-primary px-4 py-2 fw-bold">
                        Encontrar mi ubicación
                    </button>
                </div>

                <!-- Mapa ABAJO del botón, ocupando el 100% del ancho -->
                <div id="mapa" class="rounded shadow-sm" style="width: 100%; height: 450px; z-index: 1;"></div>
            </div>
        </div>

        <!-- Tabla de resultados (Oculta al principio) -->
        <div class="card shadow-sm border-0 mb-4" id="resultadosParadas" style="display:none;">
            <div class="card-body p-4">
                <h2 class="h4 mb-3 text-primary fw-bold">Paradas Disponibles</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Parada</th>
                                <th>Distancia</th>
                                <th>Ver en Mapa</th>
                            </tr>
                        </thead>
                        <tbody id="tablaParadasCercanas"></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= BASE_URL ?>assets/js/mapa_paradas.js"></script>
</body>
</html>