const selectUbicacion = document.getElementById("ubicacion");
const resultadosUrbanos = document.getElementById("resultadosUrbanos");
const tablaHorariosUrbanos = document.getElementById("tablaHorariosUrbanos");
const mensajeUrbano = document.getElementById("mensajeUrbano");

//variables para almacenar los datos de rutas y paradas
let rutasData = {};       
let paradasData = [];     
let mapasInicializados = {}; 

//definimos que omnibus pasan por cada pueblo
const lineasPorUbicacion = {
    "juan-lacaze": ["urbano", "ruta1"],
    "colonia": ["ruta1"],
    "montevideo": ["urbano"]
};

//definimos los horarios para probar cada línea
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

//traemos los datos de rutas y paradas desde los archivos json
Promise.all([
    fetch('js/rutasomnibus.json').then(res => res.json()),
    fetch('js/paradasomnibus.json').then(res => res.json())
]).then(([dataRutas, dataParadas]) => {
    rutasData = dataRutas;
    paradasData = dataParadas;
}).catch(error => console.error("Error al traer mapas urbanos:", error));


//cuando el usuario cambia la ubicación en el select
selectUbicacion.addEventListener("change", function() {
    const ubicacionSeleccionada = this.value; //guardamos la ubicación que eligió
    
    //borramos todo lo que se estaba mostrando antes
    tablaHorariosUrbanos.innerHTML = "";
    mensajeUrbano.textContent = "";
    resultadosUrbanos.style.display = "none";
    mapasInicializados = {};

    //buscamos que líneas le corresponden a ese lugar
    const lineasDisponibles = lineasPorUbicacion[ubicacionSeleccionada] || [];

    //si no hay omnibus disponibles, se muestra un mensaje
    if (lineasDisponibles.length === 0) {
        mensajeUrbano.textContent = "No hay líneas urbanas registradas para esta zona";
        return;
    }

    //si hay omnibus disponibles, se arma la tabla con los horarios
    resultadosUrbanos.style.display = "block";
    
    //creamos la fila de encabezados de la tabla
    let globalIndex = 0;

    //armamos la tabla de horarios urbanos según los omnibus disponibles
    lineasDisponibles.forEach(llaveRuta => {
        const infoRuta = rutasData[llaveRuta]; //trae el nombre y color de la línea
        const listaHorarios = horariosPorLinea[llaveRuta] || []; //y los horarios de esa línea

        //creamos una fila por cada horario disponible
        listaHorarios.forEach(h => {
            //fila con los datos del horario y el botón para abrir el mapa
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

            //fila que tendrá el mapa, oculta al principio
            const filaMapa = document.createElement("tr");
            filaMapa.className = "fila-mapa";
            filaMapa.innerHTML = `
                <td colspan="5">
                    <div id="mapa-urbano-${globalIndex}" class="map-container"></div>
                </td>
            `;
            tablaHorariosUrbanos.appendChild(filaMapa);
            
            globalIndex++; //sumamos uno para el siguiente mapa
        });
    });
});

//cuando el usuario hace click en un botón de mapa
tablaHorariosUrbanos.addEventListener("click", function(e) {
    const btn = e.target.closest(".btn-mapa"); //nos aseguramos de que el click haya sido en un botón de mapa
    if (!btn) return; 

    //obtenemos el index y la ruta de la fila que se quiere abrir
    const index = btn.getAttribute("data-index");
    const llaveRuta = btn.getAttribute("data-ruta"); 
    const contenedorMapa = document.getElementById(`mapa-urbano-${index}`);
    
    //cambiamos la clase del botón para que se vea abierto o cerrado
    btn.classList.toggle("abierto"); 
    
    //mostramos u ocultamos el contenedor del mapa según corresponda
    if (contenedorMapa.style.display === "block") {
        contenedorMapa.style.display = "none"; 
    } else {
        contenedorMapa.style.display = "block"; 
        
        //si el mapa no estaba inicializado, lo inicializamos
        if (!mapasInicializados[index]) {
            inicializarMapaUrbanoFila(index, llaveRuta);
        }
    }
});

//función para dibujar el mapa del urbano
function inicializarMapaUrbanoFila(index, llaveRuta) {
    const idContenedor = `mapa-urbano-${index}`;
    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const datosDeRuta = rutasData[llaveRuta];
    
    if (datosDeRuta && datosDeRuta.coordinates) {
        //geojson usa [longitud, latitud] pero Leaflet usa [latitud, longitud]
        //por eso las invertimos, para que se dibuje correctamente en el mapa
        const coordenadasConvertidas = datosDeRuta.coordinates.map(coord => [coord[1], coord[0]]);
        
        //dibujamos la línea de la ruta con el color correspondiente
        const lineaRuta = L.polyline(coordenadasConvertidas, {
            color: datosDeRuta.color || '#3469b2',
            weight: 4
        }).addTo(map);

        //agregamos los iconos de las paradas con su nombre y el omnibus al que pertenecen
        paradasData.forEach(parada => {
            L.marker([parada.lat, parada.lng])
                .addTo(map)
                .bindPopup(`<b>${parada.nombre}</b><br>Línea: ${datosDeRuta.nombre}`);
        });

        //hacemos que el mapa se ajuste automáticamente para mostrar toda la ruta
        map.fitBounds(lineaRuta.getBounds());
    }

    mapasInicializados[index] = map;

    //cuando el mapa se dibuja dentro de un contenedor que estaba oculto, a veces no se ve bien
    //por eso usamos un pequeño retraso para forzar a Leaflet a recalcular el tamaño del mapa
    setTimeout(() => {
        map.invalidateSize();
    }, 50);
}