<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio - Interurbanos</title>
    
    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="css/index_style.css">
</head>
<body style="    background-image:
        linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
        url(img/fondo.jpg);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;">
    
    <?php include '../includes/header.php'; ?>

    <div class="padre">
        <!-- Botón para conectar con la sección de Urbanos -->
        <div class="navegacion-paginas">
            <a href="urbanos.php" class="btn-enlace">Ir a Ómnibus Urbanos →</a>
        </div>

        <h1>Encuentra tu próximo viaje Interurbano</h1>

        <div class="hijo">
            <form class="form" id="formBusqueda">

                <div class="options">
                    <label for="origen">Origen</label>
                    <select name="origen" id="origen" required>
                        <option value="" disabled selected hidden>Selecciona un origen</option>
                        <option value="Montevideo">Montevideo</option>
                        <option value="Paysandú">Paysandú</option>
                        <option value="Salto">Salto</option>
                    </select>
                </div>

                <div class="options">
                    <label for="destino">Destino</label>
                    <select name="destino" id="destino" required>
                        <option value="" disabled selected hidden>Selecciona un destino</option>
                        <option value="Montevideo">Montevideo</option>
                        <option value="Paysandú">Paysandú</option>
                        <option value="Punta del Este">Punta del Este</option>
                        <option value="Salto">Salto</option>
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

        <p id="mensaje"></p>

        <div class="horarios" id="contenedorHorarios" style="display:none;">
            <h2>Viajes encontrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha</th>
                        <th>Asientos</th>
                        <th>Agencia</th>
                        <th>Precio</th>
                        <th>Ruta</th>
                    </tr>
                </thead>
                <tbody id="tablaHorarios"></tbody>
            </table>
        </div>
    </div>

    <div class="destinos">
        <h1>Destinos Populares</h1>
        <div class="destinos_img">
            <img src="img/placeholder.png" alt="Destino 1">
            <img src="img/placeholder.png" alt="Destino 2">
            <img src="img/placeholder.png" alt="Destino 3">
            <img src="img/placeholder.png" alt="Destino 4">
        </div>
    </div>

    <!-- JS de Leaflet y Script específico de Interurbanos -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>