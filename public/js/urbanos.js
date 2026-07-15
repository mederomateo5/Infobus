const selectUbicacion = document.getElementById("ubicacion");
const resultadosUrbanos = document.getElementById("resultadosUrbanos");
const tablaHorariosUrbanos = document.getElementById("tablaHorariosUrbanos");
const mensajeUrbano = document.getElementById("mensajeUrbano");

// Variables para almacenar los datos de rutas y paradas, así como el estado de los mapas inicializados
let rutasData = {};       
let paradasData = [];     
let mapasInicializados = {}; 

// Cargar archivos de configuración geográfica locales (rutas y paradas)
const lineasPorUbicacion = {
    "juan-lacaze": ["urbano", "ruta1"],
    "colonia": ["ruta1"],
    "montevideo": ["urbano"]
};

// Cargar archivos de configuración geográfica locales (rutas y paradas)
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

// Cargar archivos de configuración geográfica locales (rutas y paradas)
Promise.all([
    fetch('js/rutasomnibus.json').then(res => res.json()),
    fetch('js/paradasomnibus.json').then(res => res.json())
]).then(([dataRutas, dataParadas]) => {
    rutasData = dataRutas;
    paradasData = dataParadas;
}).catch(error => console.error("Error al traer mapas urbanos:", error));


// Escucha del cambio en el select de ubicación
selectUbicacion.addEventListener("change", function() {
    const ubicacionSeleccionada = this.value; // Obtenemos la ubicación seleccionada
    
    // Resetear la tabla y el mensaje antes de mostrar nuevos resultados
    tablaHorariosUrbanos.innerHTML = "";
    mensajeUrbano.textContent = "";
    resultadosUrbanos.style.display = "none";
    mapasInicializados = {};

    // Obtenemos las líneas disponibles para la ubicación seleccionada
    const lineasDisponibles = lineasPorUbicacion[ubicacionSeleccionada] || [];

    // Si no hay líneas disponibles, mostramos un mensaje y salimos
    if (lineasDisponibles.length === 0) {
        mensajeUrbano.textContent = "No hay líneas urbanas registradas para esta zona";
        return;
    }

    // Si hay líneas disponibles, mostramos la tabla de resultados
    resultadosUrbanos.style.display = "block";
    
    // Armamos la tabla de horarios urbanos según los ómnibus disponibles
    let globalIndex = 0;

    // Por cada línea disponible, obtenemos sus horarios y creamos filas en la tabla
    lineasDisponibles.forEach(llaveRuta => {
        const infoRuta = rutasData[llaveRuta]; // Obtenemos la información de la ruta desde el JSON cargado
        const listaHorarios = horariosPorLinea[llaveRuta] || []; // Obtenemos los horarios de la línea desde el objeto de horarios

        // Si no hay horarios para la línea, mostramos un mensaje y continuamos con la siguiente línea
        listaHorarios.forEach(h => {
            // Creamos una fila para cada horario y la agregamos a la tabla
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${h.salida}</td>
                <td>${h.llegada}</td>
                <td>${h.frecuencia}</td>
                <td>${h.empresa}</td>
                <td>
                    <!-- Guardamos la línea en 'data-ruta' para saber qué recorrido pintar al abrir el mapa -->
                    <button class="btn-mapa" data-index="${globalIndex}" data-ruta="${llaveRuta}">▼</button>
                </td>
            `;
            tablaHorariosUrbanos.appendChild(fila);

            // Creamos una fila adicional para el contenedor del mapa, que estará inicialmente oculta
            const filaMapa = document.createElement("tr");
            filaMapa.className = "fila-mapa";
            filaMapa.innerHTML = `
                <td colspan="5">
                    <div id="mapa-urbano-${globalIndex}" class="map-container"></div>
                </td>
            `;
            tablaHorariosUrbanos.appendChild(filaMapa);
            
            globalIndex++; // Incrementamos el índice global para la siguiente fila de mapa
        });
    });
});

// Escucha del click en la tabla de horarios urbanos para abrir/cerrar mapas
tablaHorariosUrbanos.addEventListener("click", function(e) {
    const btn = e.target.closest(".btn-mapa"); // Si el click no fue en un botón de mapa, salimos
    if (!btn) return; 

    // Obtenemos el índice y la llave de la ruta desde los atributos del botón
    const index = btn.getAttribute("data-index");
    const llaveRuta = btn.getAttribute("data-ruta"); 
    const contenedorMapa = document.getElementById(`mapa-urbano-${index}`);
    
    // Alternamos la clase 'abierto' del botón para cambiar su apariencia
    btn.classList.toggle("abierto"); 
    
    // Alternamos la visibilidad del contenedor del mapa
    if (contenedorMapa.style.display === "block") {
        contenedorMapa.style.display = "none"; 
    } else {
        contenedorMapa.style.display = "block"; 
        
        // Si el mapa del índice actual no existe, lo inicializamos
        if (!mapasInicializados[index]) {
            inicializarMapaUrbanoFila(index, llaveRuta);
        }
    }
});

// Función para inicializar el mapa de una fila urbana específica
function inicializarMapaUrbanoFila(index, llaveRuta) {
    const idContenedor = `mapa-urbano-${index}`;
    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const datosDeRuta = rutasData[llaveRuta];
    
    if (datosDeRuta && datosDeRuta.coordinates) {
        // Invertimos las coordenadas de latitud y longitud para que se dibuje correctamente en el mapa
        const coordenadasConvertidas = datosDeRuta.coordinates.map(coord => [coord[1], coord[0]]);
        
        // Creamos la línea de la ruta en el mapa con el color especificado en los datos de la ruta
        const lineaRuta = L.polyline(coordenadasConvertidas, {
            color: datosDeRuta.color || '#3469b2',
            weight: 4
        }).addTo(map);

        // Agregamos marcadores para cada parada de la ruta, mostrando el nombre de la 
        // parada y la línea a la que pertenece
        paradasData.forEach(parada => {
            L.marker([parada.lat, parada.lng])
                .addTo(map)
                .bindPopup(`<b>${parada.nombre}</b><br>Línea: ${datosDeRuta.nombre}`);
        });

        // Ajustamos el zoom del mapa para que se enfoque en la ruta trazada
        map.fitBounds(lineaRuta.getBounds());
    }

    mapasInicializados[index] = map;

    // Forzamos una actualización del tamaño del mapa después de un pequeño retraso para 
    // asegurar que se renderice correctamente
    setTimeout(() => {
        map.invalidateSize();
    }, 50);
}