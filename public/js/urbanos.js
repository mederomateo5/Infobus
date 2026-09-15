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

// Configuración de líneas por ubicación
const lineasPorUbicacion = {
    "juan-lacaze": ["urbano", "ruta1"],
    "colonia": ["ruta1"],
    "montevideo": ["urbano"]
};


const horariosPorLinea = {
    urbano: [
        { salida: "07:00", llegada: "07:25", frecuencia: "Lunes a Viernes", empresa: "Coche 14" },
        { salida: "12:15", llegada: "12:40", frecuencia: "Diario", empresa: "Coche 14" },
        { salida: "18:30", llegada: "18:55", frecuencia: "Lunes a Viernes", empresa: "Coche 02" }
    ],
    ruta1: [
        { salida: "06:00", llegada: "06:45", frecuencia: "Cada 1 hora", empresa: "Empresa Local" },
        { salida: "08:00", llegada: "08:45", frecuencia: "Cada 1 hora", empresa: "Empresa Local" },
        { salida: "14:00", llegada: "14:45", frecuencia: "Diario", empresa: "Empresa Local" }
    ]
};

// --- CARGA DIRECTA CON RUTAS RELATIVAS EXACTAS ---
Promise.all([
    fetch('./js/rutasomnibus.json').then(res => {
        if (!res.ok) throw new Error("No se pudo cargar rutasomnibus.json");
        return res.json();
    }),
    fetch('../data/paradas.json').then(res => {
        if (!res.ok) throw new Error("No se pudo cargar paradas.json");
        return res.json();
    })
]).then(([dataRutas, dataParadas]) => {
    rutasData = dataRutas;
    paradasData = dataParadas;
    console.log("¡Datos cargados con éxito!", { rutas: dataRutas, paradas: dataParadas });
}).catch(error => {
    console.error("Error al traer los datos de rutas y paradas:", error);
});

// Escucha del cambio en el select de ubicación
selectUbicacion.addEventListener("change", function() {
    const ubicacionSeleccionada = this.value; 
    
    tablaHorariosUrbanos.innerHTML = "";
    mensajeUrbano.textContent = "";
    resultadosUrbanos.style.display = "none";
    mapasInicializados = {};

    const lineasDisponibles = lineasPorUbicacion[ubicacionSeleccionada] || [];

    if (lineasDisponibles.length === 0) {
        mensajeUrbano.textContent = "No hay líneas urbanas registradas para esta zona";
        return;
    }

    resultadosUrbanos.style.display = "block";
    let globalIndex = 0;
    const ahora = new Date(); // misma hora de referencia para toda la tabla renderizada

    lineasDisponibles.forEach(llaveRuta => {
        const listaHorarios = horariosPorLinea[llaveRuta] || []; 

        listaHorarios.forEach(h => {

            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${h.salida}</td>
                <td>${h.llegada}</td>
                <td>${estimado}</td>
                <td>${h.frecuencia}</td>
                <td>${h.empresa}</td>
                <td>
                    <button class="btn-mapa" data-index="${globalIndex}" data-ruta="${llaveRuta}">▼</button>
                </td>
            `;
            tablaHorariosUrbanos.appendChild(fila);

            const filaMapa = document.createElement("tr");
            filaMapa.className = "fila-mapa";
            filaMapa.innerHTML = `
                <td colspan="5">
                    <div id="mapa-urbano-${globalIndex}" class="map-container"></div>
                </td>
            `;
            tablaHorariosUrbanos.appendChild(filaMapa);
            
            globalIndex++; 
        });
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
function inicializarMapaUrbanoFila(index, llaveRuta) {
    const idContenedor = `mapa-urbano-${index}`;
    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const datosDeRuta = rutasData[llaveRuta];
    
    if (datosDeRuta && datosDeRuta.coordinates) {
        const coordenadasConvertidas = datosDeRuta.coordinates.map(coord => [coord[1], coord[0]]);
        
        const lineaRuta = L.polyline(coordenadasConvertidas, {
            color: datosDeRuta.color || '#3469b2',
            weight: 4
        }).addTo(map);

        // Agregamos TODAS las paradas del paradas.json con sus respectivos pines de colores
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
                    .bindPopup(`<b>${parada.nombre}</b><br>Tipo: ${badgeTexto}<br>Línea: ${datosDeRuta.nombre || 'Urbano'}`);
            });
        }

        map.fitBounds(lineaRuta.getBounds());
    }

    mapasInicializados[index] = map;

    setTimeout(() => {
        map.invalidateSize();
    }, 150);
}