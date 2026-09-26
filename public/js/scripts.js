const form = document.getElementById("formBusqueda");
const tbody = document.getElementById("tablaHorarios");
const mensaje = document.getElementById("mensaje");
const contenedorHorarios = document.getElementById("contenedorHorarios");

// Variables para almacenar datos de rutas y paradas,
// así como el estado de inicialización de los mapas
let rutasData = {};
let paradasData = [];
let mapasInicializados = {};

// --- FUNCIÓN PARA CREAR PINES TRADICIONALES DE COLORES CON CSS ---
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

// Definición de los 3 colores para las paradas
const pinUrbano = crearPinColor("#28a745");       // 🟢 Solo Urbanos
const pinInterurbano = crearPinColor("#007bff");  // 🔵 Solo Interurbanos
const pinAmbos = crearPinColor("#ffc107");        // 🟠 Ambos

// ============================================================
// Cargar archivos de configuración geográfica
// ============================================================

Promise.all([
    fetch('js/rutasomnibus.json').then(res => {
        if (!res.ok) throw new Error("No se pudo cargar rutasomnibus.json");
        return res.json();
    }),
    fetch('../data/paradas.json').then(res => {
        if (!res.ok) throw new Error("No se pudo cargar paradas.json");
        return res.json();
    })
])
    .then(([dataRutas, dataParadas]) => {
        rutasData = dataRutas;
        paradasData = dataParadas;
    })
    .catch(error => {
        console.error("Error cargando configuraciones de mapas JSON:", error);
    });

// ============================================================
// Escucha del formulario de búsqueda
// ============================================================

form.addEventListener("submit", function (e) {
    e.preventDefault();

    const origen = document.getElementById("origen").value;
    const destino = document.getElementById("destino").value;
    const fecha = document.getElementById("fecha").value;
    const pasajeros = parseInt(
        document.getElementById("numPasajeros").value
    );

    // Resetear vistas previas
    tbody.innerHTML = "";
    mensaje.textContent = "Buscando viajes disponibles...";
    mapasInicializados = {};

    // ========================================================
    // Petición al archivo PHP que busca los viajes
    // ========================================================

    fetch(
        `buscador/buscar_viajes.php?origen=${encodeURIComponent(origen)}&destino=${encodeURIComponent(destino)}&fecha=${encodeURIComponent(fecha)}&pasajeros=${pasajeros}`
    )
        .then(res => {
            if (!res.ok) {
                throw new Error(`Error HTTP: ${res.status}`);
            }
            return res.json();
        })
        .then(viajesEncontrados => {
            mensaje.textContent = "";

            if (viajesEncontrados && viajesEncontrados.error) {
                contenedorHorarios.style.display = "none";
                mensaje.textContent = viajesEncontrados.error;
                return;
            }

            if (!viajesEncontrados || viajesEncontrados.length === 0) {
                contenedorHorarios.style.display = "none";
                mensaje.textContent = "No se encontraron viajes con los criterios solicitados.";
                return;
            }

            contenedorHorarios.style.display = "block";

            // =================================================
            // Renderizar filas de la tabla
            // =================================================

            viajesEncontrados.forEach((viaje, index) => {
                const fila = document.createElement("tr");
                fila.className = "text-center align-middle";

                fila.innerHTML = `
                    <td>${viaje.origen}</td>
                    <td>${viaje.destino}</td>
                    <td>${viaje.fecha}</td>
                    <td>${viaje.asientosLibres}</td>
                    <td>${viaje.agencia}</td>
                    <td>$${viaje.precio}</td>
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

                // Fila contenedora oculta para el mapa
                const filaMapa = document.createElement("tr");
                filaMapa.className = "bg-white";
                filaMapa.innerHTML = `
                    <td colspan="7" class="p-0 border-bottom border-primary">
                        <div
                            id="mapa-${index}"
                            class="rounded shadow-sm"
                            style="display: none; height: 300px; width: 100%;">
                        </div>
                    </td>
                `;

                tbody.appendChild(filaMapa);
            });
        })
        .catch(error => {
            contenedorHorarios.style.display = "none";
            mensaje.textContent = "Error de red al intentar conectar con el servidor.";
            console.error("Fetch error:", error);
        });
});

// ============================================================
// Manejo de eventos click de los botones de mapas
// ============================================================

tbody.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-mapa");
    if (!btn) return;

    const index = btn.getAttribute("data-index");
    const rutaKey = btn.getAttribute("data-ruta");
    const contenedorMapa = document.getElementById(`mapa-${index}`);

    btn.classList.toggle("abierto");

    if (btn.classList.contains("abierto")) {
        btn.style.transform = "rotate(180deg)";
    } else {
        btn.style.transform = "rotate(0deg)";
    }

    // Mostrar u ocultar el mapa
    if (contenedorMapa.style.display === "block") {
        contenedorMapa.style.display = "none";
    } else {
        contenedorMapa.style.display = "block";

        // Crear el mapa solamente si todavía no existe, o actualizar su tamaño si ya existe
        if (!mapasInicializados[index]) {
            inicializarMapaTabla(index, rutaKey);
        } else {
            setTimeout(() => {
                mapasInicializados[index].invalidateSize();
            }, 100);
        }
    }
});

// ============================================================
// Inicialización dinámica del mapa Leaflet
// ============================================================

function inicializarMapaTabla(index, rutaKey) {
    const idContenedor = `mapa-${index}`;

    // Crear mapa centrado por defecto
    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);

    // Capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // ========================================================
    // Selección dinámica de la ruta
    // ========================================================

    const rutaSeleccionada = rutasData[rutaKey] || rutasData["urbano"];

    // Dibujar ruta
    if (rutaSeleccionada && rutaSeleccionada.coordinates) {
        const rutaCoordenadas = rutaSeleccionada.coordinates.map(coord => [
            coord[1],
            coord[0]
        ]);

        const polyline = L.polyline(rutaCoordenadas, {
            color: rutaSeleccionada.color || '#0056b3',
            weight: 5
        }).addTo(map);

        // Ajustar el zoom automáticamente
        map.fitBounds(polyline.getBounds());
    }

    // ========================================================
    // Agregar paradas al mapa con pines personalizados de colores
    // ========================================================

    if (paradasData && paradasData.length > 0) {
        paradasData.forEach(parada => {
            let iconoSeleccionado = pinInterurbano; // 🔵 Azul por defecto

            if (parada.tipo === "urbano") {
                iconoSeleccionado = pinUrbano;       // 🟢 Verde
            } else if (parada.tipo === "ambos") {
                iconoSeleccionado = pinAmbos;       // 🟠 Naranja
            }

            let badgeTexto = parada.tipo ? parada.tipo.toUpperCase() : 'INTERURBANO';

            L.marker([parada.lat, parada.lng], { icon: iconoSeleccionado })
                .addTo(map)
                .bindPopup(`<b>${parada.nombre}</b><br>Tipo: ${badgeTexto}`);
        });
    }

    // Forzar el renderizado correcto de Leaflet
    setTimeout(() => {
        map.invalidateSize();
    }, 100);

    // Guardar instancia del mapa
    mapasInicializados[index] = map;
}

// ============================================================
// Sincronización de los selects de Origen y Destino
// ============================================================
const selectOrigen = document.getElementById("origen");
const selectDestino = document.getElementById("destino");

function actualizarSelects() {
    const origenSeleccionado = selectOrigen.value;
    const destinoSeleccionado = selectDestino.value;

    // 1. Rehabilitar todas las opciones en ambos selects
    Array.from(selectOrigen.options).forEach(option => {
        option.disabled = false;
    });
    Array.from(selectDestino.options).forEach(option => {
        option.disabled = false;
    });

    // 2. Si hay un destino seleccionado, deshabilitarlo en el origen
    if (destinoSeleccionado) {
        const opcionEnOrigen = Array.from(selectOrigen.options).find(opt => opt.value === destinoSeleccionado);
        if (opcionEnOrigen) {
            opcionEnOrigen.disabled = true;
        }
        // Si el usuario ya tenía seleccionado el mismo origen, lo reseteamos
        if (origenSeleccionado === destinoSeleccionado) {
            selectOrigen.value = "";
        }
    }

    // 3. Si hay un origen seleccionado, deshabilitarlo en el destino (opcional pero recomendado)
    if (origenSeleccionado) {
        const opcionEnDestino = Array.from(selectDestino.options).find(opt => opt.value === origenSeleccionado);
        if (opcionEnDestino) {
            opcionEnDestino.disabled = true;
        }
        // Si el usuario ya tenía seleccionado el mismo destino, lo reseteamos
        if (destinoSeleccionado === origenSeleccionado) {
            selectDestino.value = "";
        }
    }
}

// Escuchar los cambios en ambos selects
selectOrigen.addEventListener("change", actualizarSelects);
selectDestino.addEventListener("change", actualizarSelects);