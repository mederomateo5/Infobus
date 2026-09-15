<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ver paradas</title>
    <?php include '../includes/head.php'; ?>

    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../public/css/index_style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="padre">
        <!-- Botón para regresar -->
        <div class="navegacion-paginas">
            <a href="../public/index.php" class="btn-enlace">← Volver a Interurbanos</a>
        </div>

        <h1>Paradas Cercanas</h1>

        <!-- Contenedor principal con la clase nueva -->
        <div class="hijo hijo-paradas"> 
            
            <!-- Botón y su etiqueta centrados -->
            <div class="contenedor-boton-ubicacion">
                <label for="btnUbicacion">Selecciona tu ubicación</label>
                <button type="button" id="btnUbicacion" class="btn-ubicacion-custom">
                    Encontrar mi ubicación
                </button>
            </div>

            <!-- Mapa ABAJO del botón, ocupando el 100% del ancho -->
            <div id="mapa" class="rounded" style="width: 100%; height: 450px; z-index: 1;"></div>
        </div>

        <!-- Tabla de resultados (Oculta al principio, estilo idéntico a urbanos) -->
        <div class="horarios" id="resultadosParadas" style="display:none;">
            <h2>Paradas Disponibles</h2>
            <table>
                <thead>
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

    <!-- JS de Leaflet y Script específico de Paradas -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/paradas.js"></script>
</body>
</html>