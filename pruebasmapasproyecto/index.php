<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Ómnibus</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Mapa de Recorridos</h1>

    <div class="contenedor">

        <div class="mapa-container">
            <div id="map"></div>
        </div>

        <div class="panel-rutas">

            <h2>Rutas</h2>

            <input
                type="text"
                id="selectorRuta"
                list="listaRutas"
                placeholder="Buscar ruta..."
            >

            <datalist id="listaRutas"></datalist>

            <button onclick="mostrarRutaSeleccionada()">
                Mostrar Ruta
            </button>

            <button onclick="ocultarRuta()">
                Ocultar Ruta
            </button>

        </div>

    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="datos.js"></script>

    <script>

        const map = L.map('map').setView(
            [-34.425, -57.446],
            14
        );

        L.tileLayer(
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap'
            }
        ).addTo(map);

        const iconoAgencias = L.icon({
            iconUrl:
                'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-orange.png',
            shadowUrl:
                'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41]
        });

        const iconoParadas = L.icon({
            iconUrl:
                'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            shadowUrl:
                'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41]
        });

        agencias.forEach(agencia => {
            L.marker(
                [agencia.lat, agencia.lng],
                { icon: iconoAgencias }
            )
            .addTo(map)
            .bindPopup(agencia.nombre);
        });

        paradas.forEach(parada => {
            L.marker(
                [parada.lat, parada.lng],
                { icon: iconoParadas }
            )
            .addTo(map)
            .bindPopup(parada.nombre);
        });

        const listaRutas =
            document.getElementById("listaRutas");

        for (const id in rutas) {

            const opcion =
                document.createElement("option");

            opcion.value = rutas[id].nombre;

            listaRutas.appendChild(opcion);
        }

        const capasRutas = {};

        for (const id in rutas) {

            capasRutas[id] = L.polyline(
                rutas[id].coordinates.map(
                    c => [c[1], c[0]]
                ),
                {
                    color: rutas[id].color,
                    weight: 4
                }
            );
        }

        let rutaActual = null;

        function mostrarRutaSeleccionada() {

            const texto =
                document
                .getElementById("selectorRuta")
                .value
                .trim()
                .toLowerCase();

            let rutaEncontrada = null;

            for (const id in rutas) {

                if (
                    rutas[id]
                    .nombre
                    .toLowerCase() === texto
                ) {
                    rutaEncontrada = id;
                    break;
                }
            }

            if (!rutaEncontrada) {
                alert("Ruta no encontrada");
                return;
            }

            if (rutaActual) {
                map.removeLayer(rutaActual);
            }

            rutaActual =
                capasRutas[rutaEncontrada];

            rutaActual.addTo(map);

            map.fitBounds(
                rutaActual.getBounds()
            );
        }

        function ocultarRuta() {

            if (rutaActual) {

                map.removeLayer(rutaActual);

                rutaActual = null;
            }
        }

    </script>

</body>
</html>