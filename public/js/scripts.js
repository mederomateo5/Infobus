const form = document.getElementById("formBusqueda");
const tbody = document.getElementById("tablaHorarios");
const mensaje = document.getElementById("mensaje");
const contenedorHorarios = document.getElementById("contenedorHorarios");

// Variables para almacenar datos de rutas y paradas, así como el estado de inicialización de los mapas
let rutasData = {};      
let paradasData = [];    
let mapasInicializados = {}; 

// Cargar archivos de configuración geográfica locales (rutas y paradas)
Promise.all([
    fetch('../js/rutasomnibus.json').then(res => res.json()),
    fetch('../js/paradasomnibus.json').then(res => res.json())
]).then(([dataRutas, dataParadas]) => {
    rutasData = dataRutas;
    paradasData = dataParadas;
}).catch(error => console.error("Error cargando configuraciones de mapas JSON:", error));

// Escucha del formulario de búsqueda
form.addEventListener("submit", function (e) {
    e.preventDefault(); 

    const origen = document.getElementById("origen").value;
    const destino = document.getElementById("destino").value;
    const fecha = document.getElementById("fecha").value;
    const pasajeros = parseInt(document.getElementById("numPasajeros").value);

    // Resetear vistas previas
    tbody.innerHTML = "";
    mensaje.textContent = "Buscando viajes disponibles...";
    mapasInicializados = {}; 

    // Petición a la API local de PHP
    fetch(`../buscar_viajes.php?origen=${encodeURIComponent(origen)}&destino=${encodeURIComponent(destino)}&fecha=${encodeURIComponent(fecha)}&pasajeros=${pasajeros}`)
        .then(res => res.json())
        .then(viajesEncontrados => {
            mensaje.textContent = ""; 

            if (!viajesEncontrados || viajesEncontrados.length === 0 || viajesEncontrados.error) {
                contenedorHorarios.style.display = "none";
                mensaje.textContent = viajesEncontrados.error || "No se encontraron viajes con los criterios solicitados.";
                return;
            }

            contenedorHorarios.style.display = "block";

            // Renderizar filas de la tabla de forma dinámica
            viajesEncontrados.forEach((viaje, index) => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${viaje.origen}</td>
                    <td>${viaje.destino}</td>
                    <td>${viaje.fecha}</td>
                    <td>${viaje.asientosLibres}</td>
                    <td>${viaje.agencia}</td>
                    <td>$${viaje.precio}</td>
                    <td>
                        <!-- Guardamos el identificador ruta_key en el atributo data-ruta -->
                        <button class="btn-mapa" data-index="${index}" data-ruta="${viaje.ruta_key}">▼</button>
                    </td>
                `;
                tbody.appendChild(fila);

                // Fila contenedora oculta para el mapa
                const filaMapa = document.createElement("tr");
                filaMapa.className = "fila-mapa";
                filaMapa.innerHTML = `
                    <td colspan="7">
                        <div id="mapa-${index}" class="map-container" style="display: none; height: 300px; width: 100%;"></div>
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

// Manejo de eventos click (Acordeón de mapas)
tbody.addEventListener("click", function(e) {
    const btn = e.target.closest(".btn-mapa");
    if (!btn) return; 

    const index = btn.getAttribute("data-index"); 
    const rutaKey = btn.getAttribute("data-ruta"); 
    const contenedorMapa = document.getElementById(`mapa-${index}`);
    
    btn.classList.toggle("abierto");
    
    // Mostramos u ocultamos el contenedor del mapa según corresponda
    if (contenedorMapa.style.display === "block") {
        contenedorMapa.style.display = "none";
    } else {
        contenedorMapa.style.display = "block";
        
        // Si el mapa del índice actual no existe, se instancia de forma única
        if (!mapasInicializados[index]) {
            inicializarMapaTabla(index, rutaKey);
        }
    }
});

// Inicialización dinámica del mapa Leaflet
function inicializarMapaTabla(index, rutaKey) {
    const idContenedor = `mapa-${index}`;
    
    // Crear mapa centrado por defecto
    const map = L.map(idContenedor).setView([-34.42, -57.44], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Selección dinámica de la ruta según la clave provista de la BD
    const rutaSeleccionada = rutasData[rutaKey] || rutasData["urbano"];
    
    // Si la ruta seleccionada tiene coordenadas, se dibuja la línea en el mapa
    if (rutaSeleccionada && rutaSeleccionada.coordinates) {
        const rutaCoordenadas = rutaSeleccionada.coordinates.map(coord => [coord[1], coord[0]]);
        
        const polyline = L.polyline(rutaCoordenadas, { 
            color: '#0056b3', 
            weight: 5 
        }).addTo(map);

        // Ajustar el zoom automáticamente para enfocar la ruta trazada
        map.fitBounds(polyline.getBounds());
    }

    // Inyectar paradas geográficas
    paradasData.forEach(parada => {
        L.marker([parada.lat, parada.lng])
         .addTo(map)
         .bindPopup(`<b>${parada.nombre}</b>`);
    });

    // Forzar el renderizado correcto de Leaflet dentro de contenedores ocultos/dinámicos
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
    
    mapasInicializados[index] = map;
}