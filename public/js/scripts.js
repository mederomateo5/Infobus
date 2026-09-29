const form = document.getElementById("formBusqueda");
const tbody = document.getElementById("tablaHorarios");
const mensaje = document.getElementById("mensaje");
const contenedorHorarios = document.getElementById("contenedorHorarios");

let rutasData = {};
let paradasData = [];
let mapasInicializados = {};

// --- FUNCIÓN PARA CREAR PINES DE COLORES CON CSS ---
function crearPinColor(colorHex) {
    return L.divIcon({
        className: "custom-pin",
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
        popupAnchor: [0, -30],
    });
}

const pinOrigenRojo = crearPinColor("#dc3545");

// ============================================================
// Cargar configuraciones y paradas
// ============================================================

Promise.all([
    fetch("js/rutasomnibus.json").then((res) => {
        if (!res.ok) throw new Error("No se pudo cargar rutasomnibus.json");
        return res.json();
    }),
    fetch("obtener_paradas.php").then((res) => {
        if (!res.ok) throw new Error("No se pudieron cargar las paradas");
        return res.json();
    }),
])
    .then(([dataRutas, dataParadas]) => {
        rutasData = dataRutas;
        paradasData = dataParadas;
    })
    .catch((error) => {
        console.error("Error cargando configuraciones geográficas:", error);
    });

// ============================================================
// Búsqueda de viajes
// ============================================================

form.addEventListener("submit", function (e) {
    e.preventDefault();

    const origen = document.getElementById("origen").value;
    const destino = document.getElementById("destino").value;
    const fecha = document.getElementById("fecha").value;
    const pasajeros = parseInt(document.getElementById("numPasajeros").value);

    tbody.innerHTML = "";
    mensaje.textContent = "Buscando viajes disponibles...";
    mapasInicializados = {};

    fetch(
        `buscador/buscar_viajes.php?origen=${encodeURIComponent(origen)}&destino=${encodeURIComponent(destino)}&fecha=${encodeURIComponent(fecha)}&pasajeros=${pasajeros}`,
    )
        .then((res) => res.json())
        .then((viajesEncontrados) => {
            mensaje.textContent = "";

            if (viajesEncontrados && viajesEncontrados.error) {
                contenedorHorarios.style.display = "none";
                mensaje.textContent = viajesEncontrados.error;
                return;
            }

            if (!viajesEncontrados || viajesEncontrados.length === 0) {
                contenedorHorarios.style.display = "none";
                mensaje.textContent =
                    "No se encontraron viajes con los criterios solicitados.";
                return;
            }

            contenedorHorarios.style.display = "block";

            viajesEncontrados.forEach((viaje, index) => {
                const fila = document.createElement("tr");
                fila.className = "text-center align-middle";

                // Definir si la estrella debe estar rellena o vacía según la base de datos
                const esFavorito = viaje.es_favorito ? true : false;
                const estiloEstrella = esFavorito
                    ? "fas fa-star text-primary"
                    : "far fa-star text-primary";

                fila.innerHTML = `
                    <td>${viaje.origen}</td>
                    <td>${viaje.destino}</td>
                    <td>${viaje.fecha}</td>
                    <td>${viaje.asientosLibres}</td>
                    <td>${viaje.agencia}</td>
                    <td>$${viaje.precio}</td>
                    <td>
                        <button class="btn btn-sm bg-transparent border-0 shadow-none btn-favorito p-0" data-id="${viaje.id}" style="font-size: 1.2rem;">
                            <i class="${estiloEstrella}"></i>
                        </button>
                    </td>
                    <td>
                        <button
                            class="btn btn-sm btn-outline-primary border-0 btn-mapa fw-bold"
                            data-index="${index}"
                            data-ruta="${viaje.ruta_key}"
                            style="transition: transform 0.3s ease;">
                            ▼
                        </button>
                    </td>
                `;

                tbody.appendChild(fila);

                const filaMapa = document.createElement("tr");
                filaMapa.className = "bg-white";
                filaMapa.innerHTML = `
                    <td colspan="8" class="p-0 border-bottom border-primary">
                        <div
                            id="mapa-${index}"
                            class="rounded shadow-sm"
                            style="display: none; height: 350px; width: 100%;">
                        </div>
                    </td>
                `;

                tbody.appendChild(filaMapa);
            });
        })
        .catch((error) => {
            contenedorHorarios.style.display = "none";
            mensaje.textContent =
                "Error de red al intentar conectar con el servidor.";
            console.error("Fetch error:", error);
        });
});

// ============================================================
// Manejo de Favoritos (Click en la estrella)
// ============================================================

tbody.addEventListener("click", function (e) {
    const btnFav = e.target.closest(".btn-favorito");
    if (btnFav) {
        const viajeId = btnFav.getAttribute("data-id");
        const icono = btnFav.querySelector("i");

        fetch("usuario/marcar_favorito.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ viaje_id: viajeId }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    if (data.favorito) {
                        // Rellenar estrella (favorito activo)
                        icono.classList.remove("far");
                        icono.classList.add("fas");
                    } else {
                        // Vaciar estrella (favorito removido)
                        icono.classList.remove("fas");
                        icono.classList.add("far");
                    }
                } else {
                    alert("No se pudo actualizar el favorito: " + data.mensaje);
                }
            })
            .catch((err) => console.error("Error en favoritos:", err));
        return;
    }

    // Manejo de mapas existente
    const btn = e.target.closest(".btn-mapa");
    if (!btn) return;

    const index = btn.getAttribute("data-index");
    const rutaKey = btn.getAttribute("data-ruta");

    const filaPadre = btn.closest("tr");
    const origenViaje = filaPadre.cells[0].textContent;
    const destinoViaje = filaPadre.cells[1].textContent;

    const contenedorMapa = document.getElementById(`mapa-${index}`);

    btn.classList.toggle("abierto");

    if (btn.classList.contains("abierto")) {
        btn.style.transform = "rotate(180deg)";
    } else {
        btn.style.transform = "rotate(0deg)";
    }

    if (contenedorMapa.style.display === "block") {
        contenedorMapa.style.display = "none";
    } else {
        contenedorMapa.style.display = "block";

        if (!mapasInicializados[index]) {
            inicializarMapaTabla(index, rutaKey, origenViaje, destinoViaje);
        } else {
            setTimeout(() => {
                mapasInicializados[index].invalidateSize();
            }, 100);
        }
    }
});

// ============================================================
// Inicialización del mapa Leaflet
// ============================================================

function inicializarMapaTabla(index, rutaKey, nombreOrigen, nombreDestino) {
    const idContenedor = `mapa-${index}`;

    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);

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

// ============================================================
// Sincronización de selects
// ============================================================
const selectOrigen = document.getElementById("origen");
const selectDestino = document.getElementById("destino");

function actualizarSelects() {
    const origenSeleccionado = selectOrigen.value;
    const destinoSeleccionado = selectDestino.value;

    Array.from(selectOrigen.options).forEach((option) => {
        option.disabled = false;
    });
    Array.from(selectDestino.options).forEach((option) => {
        option.disabled = false;
    });

    if (destinoSeleccionado) {
        const opcionEnOrigen = Array.from(selectOrigen.options).find(
            (opt) => opt.value === destinoSeleccionado,
        );
        if (opcionEnOrigen) opcionEnOrigen.disabled = true;
        if (origenSeleccionado === destinoSeleccionado) selectOrigen.value = "";
    }

    if (origenSeleccionado) {
        const opcionEnDestino = Array.from(selectDestino.options).find(
            (opt) => opt.value === origenSeleccionado,
        );
        if (opcionEnDestino) opcionEnDestino.disabled = true;
        if (destinoSeleccionado === origenSeleccionado) selectDestino.value = "";
    }
}

selectOrigen.addEventListener("change", actualizarSelects);
selectDestino.addEventListener("change", actualizarSelects);
