const selectUbicacion = document.getElementById("ubicacion");
const resultadosUrbanos = document.getElementById("resultadosUrbanos");
const tablaHorariosUrbanos = document.getElementById("tablaHorariosUrbanos");
const mensajeUrbano = document.getElementById("mensajeUrbano");

// Variables para almacenar los datos de rutas y paradas, así como el estado de los mapas inicializados
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

// --- Helpers para calcular el estimado en base a la hora actual del navegador ---
function horaAMinutos(hora) {
    const [h, m] = hora.split(":").map(Number);
    return h * 60 + m;
}

function minutosAHora(minutos) {
    const h = Math.floor(minutos / 60) % 24;
    const m = minutos % 60;
    return `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`;
}

function calcularEstimado(llegada, ahora = new Date()) {
    const minutosActuales = ahora.getHours() * 60 + ahora.getMinutes();
    const minutosLlegada = horaAMinutos(llegada);
    const diferencia = minutosLlegada - minutosActuales;

    if (diferencia < -5) return "Viaje finalizado";
    if (diferencia >= -5 && diferencia <= 0) return "Llegando ahora";

    if (diferencia > 60) {
        const horas = Math.floor(diferencia / 60);
        const mins = diferencia % 60;
        return mins > 0 ? `${horas}h ${mins}min` : `${horas}h`;
    }

    return `${diferencia} min`;
}


// Escucha del cambio en el select de ubicación
selectUbicacion.addEventListener("change", function() {
    const ubicacionSeleccionada = this.value; 
    
    tablaHorariosUrbanos.innerHTML = "";
    mensajeUrbano.textContent = "Buscando viajes urbanos...";
    resultadosUrbanos.style.display = "none";
    mapasInicializados = {};

    if (!ubicacionSeleccionada) {
        mensajeUrbano.textContent = "";
        return;
    }

    // Petición AJAX al archivo PHP que consulta la base de datos
    fetch('buscador/buscar_urbanos.php')
        .then(res => {
            if (!res.ok) throw new Error("Error en la respuesta del servidor");
            return res.json();
        })
        .then(viajesUrbanos => {
            mensajeUrbano.textContent = "";

            if (viajesUrbanos.error) {
                mensajeUrbano.textContent = viajesUrbanos.error;
                return;
            }

            if (!viajesUrbanos || viajesUrbanos.length === 0) {
                mensajeUrbano.textContent = "No hay viajes urbanos registrados en la base de datos.";
                return;
            }

            resultadosUrbanos.style.display = "block";
            const ahora = new Date(); // Tomamos la hora actual de referencia

            // Recorrer los viajes devueltos por la base de datos e insertarlos en la tabla
            viajesUrbanos.forEach((viaje, index) => {
                const estimado = calcularEstimado(viaje.hora_llegada, ahora);
                const frecuenciaTexto = viaje.frecuencia || 'Diario'; // Días devueltos desde la BD

                const fila = document.createElement("tr");
                fila.className = "text-center align-middle";
                fila.innerHTML = `
                    <td>${viaje.hora}</td>
                    <td>${viaje.hora_llegada}</td>
                    <td>${estimado}</td>
                    <td>${frecuenciaTexto}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary border-0 btn-mapa fw-bold" data-index="${index}" data-ruta="${viaje.ruta_key}" style="transition: transform 0.3s ease;">▼</button>
                    </td>
                `;
                tablaHorariosUrbanos.appendChild(fila);

                const filaMapa = document.createElement("tr");
                filaMapa.className = "bg-white";
                filaMapa.innerHTML = `
                    <td colspan="5" class="p-0 border-bottom border-primary">
                        <div id="mapa-urbano-${index}" class="rounded shadow-sm" style="display: none; height: 300px; width: 100%;"></div>
                    </td>
                `;
                tablaHorariosUrbanos.appendChild(filaMapa);
            });
        })
        .catch(error => {
            mensajeUrbano.textContent = "Error de red al intentar conectar con el servidor.";
            console.error("Fetch error:", error);
        });
});

// Escucha del click en la tabla de horarios urbanos para abrir/cerrar mapas
tablaHorariosUrbanos.addEventListener("click", function(e) {
    const btn = e.target.closest(".btn-mapa"); 
    if (!btn) return; 

    const index = btn.getAttribute("data-index");
    const llaveRuta = btn.getAttribute("data-ruta"); 
    const contenedorMapa = document.getElementById(`mapa-urbano-${index}`);
    
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
        
        if (mapasInicializados[index]) {
            setTimeout(() => {
                mapasInicializados[index].invalidateSize();
            }, 100);
        } else {
            inicializarMapaUrbanoFila(index, llaveRuta);
        }
    }
});

// Función para inicializar el mapa de una fila urbana específica y pintar todas las paradas
// Función para inicializar el mapa de una fila urbana específica y pintar la ruta animada y paradas
function inicializarMapaUrbanoFila(index, llaveRuta) {
    const idContenedor = `mapa-urbano-${index}`;
    
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
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Resto del código de urbanos...
    fetch('../public/js/recorrido_urbano.geojson') 
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo GeoJSON");
            return response.json();
        })
        .then(geojsonData => {
            let ultimaCapaRuta = null;
            geojsonData.features.forEach(feature => {
                if (feature.geometry.type === "LineString") {
                    const coords = feature.geometry.coordinates.map(c => [c[1], c[0]]);
                    const rutaAnimada = L.polyline.antPath(coords, {
                        color: '#3469b2',
                        weight: 5,
                        opacity: 0.85,
                        pulseColor: '#ffffff',
                        delay: 800,
                        dashArray: [10, 20]
                    }).addTo(map);
                    ultimaCapaRuta = rutaAnimada;
                }
            });
            if (ultimaCapaRuta) {
                map.fitBounds(ultimaCapaRuta.getBounds());
            }
        })
        .catch(error => console.error("Error al cargar el archivo GeoJSON:", error));

    if (paradasData && paradasData.length > 0) {
        paradasData.forEach(parada => {
            let iconoSeleccionado = pinInterurbano; 
            if (parada.tipo === "urbano") {
                iconoSeleccionado = pinUrbano;       
            } else if (parada.tipo === "ambos") {
                iconoSeleccionado = pinAmbos;       
            }
            let badgeTexto = parada.tipo ? parada.tipo.toUpperCase() : 'INTERURBANO';
            L.marker([parada.lat, parada.lng], { icon: iconoSeleccionado })
                .addTo(map)
                .bindPopup(`<b>${parada.nombre}</b><br>Tipo: ${badgeTexto}`);
        });
    }

    mapasInicializados[index] = map;

    setTimeout(() => {
        map.invalidateSize();
    }, 150);
}