//Logica de leaflet

let mapasInicializados = {};
let paradasData = [];

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

    let paradaOrigenObj = paradasData.find(
        (p) => p.nombre.toLowerCase() === nombreOrigen.toLowerCase(),
    );
    let paradaDestinoObj = paradasData.find(
        (p) => p.nombre.toLowerCase() === nombreDestino.toLowerCase(),
    );

    if (paradaOrigenObj && paradaDestinoObj) {
        const lng1 = parseFloat(paradaOrigenObj.lng);
        const lat1 = parseFloat(paradaOrigenObj.lat);
        const lng2 = parseFloat(paradaDestinoObj.lng);
        const lat2 = parseFloat(paradaDestinoObj.lat);

        const urlOSRM = `https://router.project-osrm.org/route/v1/driving/${lng1},${lat1};${lng2},${lat2}?overview=full&geometries=geojson`;

        fetch(urlOSRM)
            .then((res) => res.json())
            .then((data) => {
                if (data.routes && data.routes.length > 0) {
                    const routeGeoJSON = data.routes[0].geometry;

                    const polyline = L.geoJSON(routeGeoJSON, {
                        style: {
                            color: "#0056b3",
                            weight: 5,
                            opacity: 0.8,
                            dashArray: "8, 8",
                        },
                    }).addTo(map);

                    map.fitBounds(polyline.getBounds());
                } else {
                    dibujarLineaRectaFallback(map, lat1, lng1, lat2, lng2);
                }
            })
            .catch(() => {
                dibujarLineaRectaFallback(map, lat1, lng1, lat2, lng2);
            });
    }

    const paradasDelViaje = [
        { obj: paradaOrigenObj, rol: "ORIGEN" },
        { obj: paradaDestinoObj, rol: "DESTINO" },
    ];

    paradasDelViaje.forEach((item) => {
        if (item.obj) {
            const marker = L.marker([item.obj.lat, item.obj.lng], {
                icon: pinOrigenRojo,
            })
                .addTo(map)
                .bindPopup(`<b>${item.obj.nombre}</b><br>Rol: ${item.rol}`);

            marker.openPopup();
        }
    });

    setTimeout(() => {
        map.invalidateSize();
    }, 100);

    mapasInicializados[index] = map;
}

function dibujarLineaRectaFallback(map, lat1, lng1, lat2, lng2) {
    const polyline = L.polyline(
        [
            [lat1, lng1],
            [lat2, lng2],
        ],
        {
            color: "#0056b3",
            weight: 5,
            dashArray: "8, 8",
        },
    ).addTo(map);
    map.fitBounds(polyline.getBounds());
}