// Logica de Leaflet para Interurbanos (index.php)

let mapasInicializados = {};

// --- FUNCIÓN PARA CREAR PINES DE COLORES CON CSS ---
function crearPinColor(colorHex) {
    return L.divIcon({
        className: 'custom-pin',
        html: `<div style="
            background-color: ${colorHex};
            width: 20px;
            height: 20px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            left: 50%;
            top: 50%;
            margin: -10px 0 0 -10px;
            position: absolute;
            border: 2px solid #FFFFFF;
            box-shadow: 0 3px 6px rgba(0,0,0,0.4);
        "></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });
}

// Definición de colores para los pines de las paradas
const pinUrbano = crearPinColor("#28a745");       // 🟢 Urbano
const pinInterurbano = crearPinColor("#007bff");  // 🔵 Interurbano
const pinAmbos = crearPinColor("#ffc107");        // 🟠 Ambos

function inicializarMapaTabla(index, rutaKey, nombreOrigen, nombreDestino) {
    const idContenedor = `mapa-${index}`;

    const uruguayBounds = L.latLngBounds(
        [-35.85, -58.5], 
        [-30.05, -53.0]  
    );

    const map = L.map(idContenedor, {
        center: [-34.42, -57.44],
        zoom: 13,
        minZoom: 7,                  
        maxBounds: uruguayBounds,    
        maxBoundsViscosity: 1.0      
    });

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "© OpenStreetMap contributors",
    }).addTo(map);

    // Petición AJAX para obtener el recorrido completo desde la BD utilizando la ruta_key del ómnibus
    fetch(`buscador/obtener_recorrido_ruta.php?ruta=${rutaKey}`)
        .then(response => {
            if (!response.ok) throw new Error("No se pudieron cargar las paradas desde la base de datos");
            return response.json();
        })
        .then(paradas => {
            if (!paradas || paradas.length === 0) {
                console.warn("No hay paradas registradas para esta ruta.");
                return;
            }

            // Extraer coordenadas en formato "longitud,latitud" requeridas por OSRM
            const coordenadasString = paradas.map(p => `${p.longitud},${p.latitud}`).join(';');
            const urlOSRM = `https://router.project-osrm.org/route/v1/driving/${coordenadasString}?overview=full&geometries=geojson`;

            return fetch(urlOSRM)
                .then(res => res.json())
                .then(dataRoute => {
                    let ultimaCapaRuta = null;

                    if (dataRoute.routes && dataRoute.routes.length > 0) {
                        const coordsRuta = dataRoute.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);

                        // Dibujar línea animada si Ant Path está disponible, sino una polilínea normal
                        if (typeof L.polyline.antPath === 'function') {
                            ultimaCapaRuta = L.polyline.antPath(coordsRuta, {
                                color: '#0056b3',
                                weight: 5,
                                opacity: 0.85,
                                pulseColor: '#ffffff',
                                delay: 800,
                                dashArray: [10, 20]
                            }).addTo(map);
                        } else {
                            ultimaCapaRuta = L.polyline(coordsRuta, {
                                color: '#0056b3',
                                weight: 5,
                                opacity: 0.8,
                                dashArray: "8, 8"
                            }).addTo(map);
                        }
                    }

                    if (ultimaCapaRuta) {
                        map.fitBounds(ultimaCapaRuta.getBounds());
                    }

                    // Pintar los pines de las paradas sobre la ruta
                    paradas.forEach(parada => {
                        // Omitir puntos de paso interno si empiezan con [Paso]
                        if (parada.nombre.startsWith("[Paso]")) {
                            return; 
                        }

                        let iconoSeleccionado = pinInterurbano; 
                        if (parada.tipo === "urbano") {
                            iconoSeleccionado = pinUrbano;       
                        } else if (parada.tipo === "ambos") {
                            iconoSeleccionado = pinAmbos;       
                        }
                        
                        let badgeTexto = parada.tipo ? parada.tipo.toUpperCase() : 'INTERURBANO';
                        
                        L.marker([parada.latitud, parada.longitud], { icon: iconoSeleccionado })
                            .addTo(map)
                            .bindPopup(`<b>${parada.nombre}</b><br>Tipo: ${badgeTexto}<br>Orden: ${parada.orden}`);
                    });
                });
        })
        .catch(error => console.error("Error al cargar la ruta dinámica desde la BD:", error));

    setTimeout(() => {
        map.invalidateSize();
    }, 150);

    mapasInicializados[index] = map;
}