const form = document.getElementById("formBusqueda");
const tbody = document.getElementById("tablaHorarios");
const mensaje = document.getElementById("mensaje");
const contenedorHorarios = document.getElementById("contenedorHorarios");

let horarios = [];       
let rutasData = {};      
let paradasData = [];    
let mapasInicializados = {}; //que mapas ya creamos y no duplicarlos

//traemos la info de los 3 archivos json
Promise.all([
    fetch('js/horariosomnibus.json').then(res => res.json()),
    fetch('js/rutasomnibus.json').then(res => res.json()),
    fetch('js/paradasomnibus.json').then(res => res.json())
]).then(([dataHorarios, dataRutas, dataParadas]) => {
    //si todo sale bien guardamos la info en las variables de arriba
    horarios = dataHorarios;
    rutasData = dataRutas;
    paradasData = dataParadas;
}).catch(error => console.error("Hubo un problema cargando los JSON:", error));

//cuando el usuario le da al botón de buscar
form.addEventListener("submit", function (e) {
    e.preventDefault(); //se frena el envío para que no se recargue la página

    //guardamos las especificaciones que el usuario puso en variables
    const origen = document.getElementById("origen").value;
    const destino = document.getElementById("destino").value;
    const fecha = document.getElementById("fecha").value;
    const pasajeros = parseInt(document.getElementById("numPasajeros").value);

    //borramos todo lo que se estaba mostrando antes
    tbody.innerHTML = "";
    mensaje.textContent = "";
    mapasInicializados = {}; 

    //se filtran los horarios para dejar solo los que coinciden con la busqueda
    const viajesEncontrados = horarios.filter(viaje =>
        viaje.origen === origen &&
        viaje.destino === destino &&
        viaje.fecha === fecha &&
        viaje.asientosLibres >= pasajeros
    );

    //si el filtro quedó vacío escondemos la tabla
    if (viajesEncontrados.length === 0) {
        contenedorHorarios.style.display = "none";
        mensaje.textContent = "No encontramos viajes que coincidan con lo que buscaste";
        return;
    }

    //si encontramos viajes se muestra la tabla
    contenedorHorarios.style.display = "block";

    //creamos las filas de la tabla con los viajes encontrados
    viajesEncontrados.forEach((viaje, index) => {
        //la fila con los datos visibles del viaje
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${viaje.origen}</td>
            <td>${viaje.destino}</td>
            <td>${viaje.fecha}</td>
            <td>${viaje.asientosLibres}</td>
            <td>${viaje.agencia}</td>
            <td>$${viaje.precio}</td>
            <td>
                <!-- le ponemos un 'data-index' para saber qué mapa abrir después -->
                <button class="btn-mapa" data-index="${index}">▼</button>
            </td>
        `;
        tbody.appendChild(fila);

        //la fila de abajo que va a estar escondida esperando al mapa
        const filaMapa = document.createElement("tr");
        filaMapa.className = "fila-mapa";
        filaMapa.innerHTML = `
            <td colspan="7">
                <!-- usamos el index para que cada div de mapa tenga un id único -->
                <div id="mapa-${index}" class="map-container"></div>
            </td>
        `;
        tbody.appendChild(filaMapa);
    });
});

//abrir y cerrar mapas al hacer click en la flechita
//escuchamos clicks en toda la tabla, pero nos fijamos si fue en un botón de mapa
tbody.addEventListener("click", function(e) {
    //nos aseguramos de que el click haya sido en el botón de la flechita
    const btn = e.target.closest(".btn-mapa");
    if (!btn) return; //si tocaron otra cosa, no pasa nada

    const index = btn.getAttribute("data-index"); 
    const contenedorMapa = document.getElementById(`mapa-${index}`);
    
    //cambiamos la clase del botón para que cambie la flechita
    btn.classList.toggle("abierto");
    
    //si el mapa estaba abierto, se cierra y si estaba cerrado, se abre
    if (contenedorMapa.style.display === "block") {
        contenedorMapa.style.display = "none";
    } else {
        //si estaba escondido, lo mostramos
        contenedorMapa.style.display = "block";
        
        //si es la primera vez que se abre este mapa, lo inicializamos
        if (!mapasInicializados[index]) {
            inicializarMapaTabla(index);
        }
    }
});

//función para dibujar el mapa
function inicializarMapaTabla(index) {
    const idContenedor = `mapa-${index}`;
    
    //creamos el mapa y lo metemos en su div correspondiente
    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);
    
    //cargamos el diseño de mapa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    //geojson usa [longitud, latitud] pero Leaflet usa [latitud, longitud]
    //con el .map() se da vuelta el orden de las coordenadas
    const rutaPrueba = rutasData.urbano.coordinates.map(coord => [coord[1], coord[0]]);
    
    //dibujamos la línea roja del recorrido
    const polyline = L.polyline(rutaPrueba, { 
        color: 'red', 
        weight: 4 
    }).addTo(map);

    //se ponen los icons de las paradas con sus nombres
    paradasData.forEach(parada => {
        L.marker([parada.lat, parada.lng])
         .addTo(map)
         .bindPopup(`<b>${parada.nombre}</b>`);
    });

    //hacemos que el mapa se ajuste para mostrar toda la ruta
    map.fitBounds(polyline.getBounds());
    
    //guardamos el mapa en el objeto para no duplicarlo si se vuelve a abrir
    mapasInicializados[index] = map;
}