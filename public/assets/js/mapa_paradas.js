// Busca el botón "btnUbicacion" en el HTML
const btnUbicacion = document.getElementById("btnUbicacion");

// Límites geográficos aproximados de Uruguay para evitar hacer zoom out excesivo
const uruguayBounds = L.latLngBounds(
    [-35.85, -58.5], // Esquina suroeste (lat, lng)
    [-30.05, -53.0]  // Esquina noreste (lat, lng)
);

// Crea el mapa dentro del elemento HTML con id="mapa" aplicando las restricciones de Uruguay
const mapa = L.map("mapa", {
    center: [-34.4188, -57.4523],
    zoom: 13,
    minZoom: 7,                  // Evita que se aleje más allá del nivel 7
    maxBounds: uruguayBounds,    // Restringe el mapa a los límites de Uruguay
    maxBoundsViscosity: 1.0      // Hace que los bordes sean estrictos (no permite arrastrar fuera)
});

// Agrega las teselas de OpenStreetMap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
}).addTo(mapa);

let latUsuario = null;
let lngUsuario = null;
let marcadorUsuario = null;
let paradas = [];
let marcadoresParadas = [];

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

// Definición de pines según el ENUM de la base de datos
const pinRojoUsuario = crearPinColor("#dc3545");   // Usuario
const pinUrbano = crearPinColor("#28a745");       // Urbano
const pinInterurbano = crearPinColor("#007bff");  // Interurbano
const pinAmbos = crearPinColor("#ffc107");        // Urbano/Interurbano

// Detecta el clic en el botón de ubicación
btnUbicacion.addEventListener("click", obtenerUbicacion);

function obtenerUbicacion() {
    btnUbicacion.textContent = "⏳ Buscando ubicación...";
    btnUbicacion.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            mostrarUbicacion(position);
            btnUbicacion.textContent = "Ubicación encontrada";
            btnUbicacion.classList.replace("btn-primary", "btn-success");
            btnUbicacion.disabled = false;
        },
        (error) => {
            mostrarError(error);
            btnUbicacion.textContent = "Encontrar mi ubicación";
            btnUbicacion.disabled = false;
        }
    );
}

function mostrarUbicacion(position) {
    const latitud = position.coords.latitude;
    const longitud = position.coords.longitude;

    latUsuario = latitud;
    lngUsuario = longitud;

    mapa.setView([latitud, longitud], 16);

    if (marcadorUsuario) {
        mapa.removeLayer(marcadorUsuario);
    }

    // Popup de usuario creado de forma segura con nodos DOM
    const popupUsuario = document.createElement("div");
    const strongUsuario = document.createElement("strong");
    strongUsuario.textContent = "Tu ubicación actual";
    popupUsuario.appendChild(strongUsuario);

    marcadorUsuario = L.marker([latitud, longitud], { icon: pinRojoUsuario })
        .addTo(mapa)
        .bindPopup(popupUsuario)
        .openPopup();

    calcularParadasCercanas();
}

function mostrarError(error) {
    alert("No se pudo obtener la ubicación. Asegúrate de tener el GPS activado y dar permisos al navegador.");
    console.log("Error de ubicación:", error);
}

// Cargar las paradas DIRECTAMENTE desde la Base de Datos a través de PHP
fetch("api/obtener_paradas.php")
    .then(response => {
        if (!response.ok) {
            throw new Error("El servidor respondió con un error HTTP: " + response.status);
        }
        return response.json();
    })
    .then(datos => {
        // Validación estricta: aseguramos que 'datos' sea un Array
        if (!Array.isArray(datos)) {
            console.error("El servidor no devolvió una lista válida:", datos);
            throw new Error(datos.mensaje || "Formato de datos inválido recibido del servidor.");
        }

        paradas = datos;
        paradas.forEach(parada => {
            if (parada.nombre.startsWith("[Paso]")) {
                return; 
            }
            let iconoSeleccionado = pinInterurbano; // Por defecto

            if (parada.tipo === "urbano") {
                iconoSeleccionado = pinUrbano;
            } else if (parada.tipo === "urbano/interurbano") {
                iconoSeleccionado = pinAmbos;
            }

            // Creación segura del contenido del popup para evitar XSS almacenado
            const popupContent = document.createElement("div");
            const strong = document.createElement("strong");
            strong.textContent = parada.nombre;
            popupContent.appendChild(strong);
            popupContent.appendChild(document.createElement("br"));
            popupContent.append(`Tipo: ${parada.tipo}`);

            const marcador = L.marker([parada.lat, parada.lng], { icon: iconoSeleccionado })
                .addTo(mapa)
                .bindPopup(popupContent);
            
            marcadoresParadas.push(marcador);
        });
    })
    .catch(error => {
        console.error("Error al cargar las paradas desde la base de datos:", error.message);
    });

function calcularParadasCercanas() {
    const paradasConDistancia = paradas.map(parada => {
        const distancia = mapa.distance(
            [latUsuario, lngUsuario],
            [parada.lat, parada.lng]
        );

        return {
            ...parada,
            distancia: distancia
        };
    });

    // Ordenar de menor a mayor distancia
    paradasConDistancia.sort((a, b) => a.distancia - b.distancia);

    // Seleccionar las 6 más cercanas (o las disponibles si hay menos)
    const paradasCercanas = paradasConDistancia.slice(0, 6);
    
    const tbody = document.getElementById("tablaParadasCercanas");
    tbody.innerHTML = "";

    const resultadosParadas = document.getElementById("resultadosParadas");
    resultadosParadas.style.display = "block";

    // Verificamos si la ruta de la página actual termina en paradas.php
    const esPaginaParadas = window.location.pathname.endsWith("paradas.php");

    paradasCercanas.forEach(parada => {
        const fila = document.createElement("tr");

        const colNombre = document.createElement("td");
        const spanTexto = document.createElement("span");
        spanTexto.textContent = `🚏 ${parada.nombre} `;
        colNombre.appendChild(spanTexto);

        const badge = document.createElement("span");
        badge.style.padding = "2px 6px";
        badge.style.borderRadius = "4px";
        badge.style.fontSize = "11px";

        if (parada.tipo === "urbano") {
            badge.style.background = "#28a745";
            badge.style.color = "white";
            badge.textContent = "Urbano";
        } else if (parada.tipo === "urbano/interurbano") {
            badge.style.background = "#ffc107";
            badge.style.color = "black";
            badge.textContent = "Ambos";
        } else {
            badge.style.background = "#007bff";
            badge.style.color = "white";
            badge.textContent = "Interurbano";
        }

        colNombre.appendChild(badge);

        const colDistancia = document.createElement("td");
        colDistancia.textContent = `A ${Math.round(parada.distancia)} metros`;

        const colAccion = document.createElement("td");
        const btnVer = document.createElement("button");
        
        // Se asigna el texto condicionalmente según la página
        btnVer.textContent = esPaginaParadas ? "Ver Parada" : "Ver Ruta";
        btnVer.className = "btn btn-sm btn-primary";
        
        btnVer.addEventListener("click", () => {
            mapa.setView([parada.lat, parada.lng], 17);

            const marcador = marcadoresParadas.find(m => {
                const posicion = m.getLatLng();
                return (posicion.lat === parseFloat(parada.lat) && posicion.lng === parseFloat(parada.lng));
            });

            if (marcador) {
                marcador.openPopup();
            }

            document.getElementById("mapa").scrollIntoView({ behavior: "smooth", block: "center" });
        });

        colAccion.appendChild(btnVer);

        fila.appendChild(colNombre);
        fila.appendChild(colDistancia);
        fila.appendChild(colAccion);

        tbody.appendChild(fila);
    });
}