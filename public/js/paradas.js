// Busca el botón "btnUbicacion" que tenemos en el HTML y lo guarda en una variable.
const btnUbicacion = document.getElementById("btnUbicacion");

// Crea el mapa dentro del elemento HTML que tiene id="mapa".
const mapa = L.map("mapa").setView([-34.4, -57.45], 13);

// Agrega las imágenes que forman el mapa (OpenStreetMap).
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
}).addTo(mapa);

let latUsuario = null;
let lngUsuario = null;
let marcadorUsuario = null;
let paradas = [];
let marcadoresParadas = [];

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

// Definición de los pines según los 3 colores pedidos + el rojo de usuario
const pinRojoUsuario = crearPinColor("#dc3545");   // 🔴 Usuario
const pinUrbano = crearPinColor("#28a745");       // 🟢 Solo Urbanos
const pinInterurbano = crearPinColor("#007bff");  // 🔵 Solo Interurbanos
const pinAmbos = crearPinColor("#ffc107");        // 🟠 Ambos (o podés cambiar el hex si preferís otro)

// Detecta cuando el usuario hace clic en el botón.
btnUbicacion.addEventListener("click", obtenerUbicacion);

function obtenerUbicacion() {
    btnUbicacion.textContent = "⏳ Buscando ubicación...";
    btnUbicacion.disabled = true;


    navigator.geolocation.getCurrentPosition(
        (position) => {
            mostrarUbicacion(position);
            btnUbicacion.textContent = "📍 Ubicación encontrada";
            btnUbicacion.classList.replace("btn-primary", "btn-success");
            btnUbicacion.disabled = false;
        },
        (error) => {
            mostrarError(error);
            btnUbicacion.textContent = "📍 Encontrar mi ubicación";
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

    // Marcador del usuario en ROJO tradicional
    marcadorUsuario = L.marker([latitud, longitud], { icon: pinRojoUsuario })
        .addTo(mapa)
        .bindPopup("<strong>📍 Tu ubicación actual</strong>")
        .openPopup();

    calcularParadasCercanas();
}

function mostrarError(error) {

    alert("No se pudo obtener la ubicación. Asegúrate de tener el GPS activado y dar permisos al navegador.");
    console.log("Error de ubicación:", error);
}

// Cargar las paradas desde el JSON
fetch("../data/paradas.json")
    .then(response => response.json())
    .then(datos => {
        paradas = datos;
        paradas.forEach(parada => {
            // Asignación de icono según el tipo que tenga en el JSON
            let iconoSeleccionado = pinInterurbano; // Por defecto

            if (parada.tipo === "urbano") {
                iconoSeleccionado = pinUrbano;
            } else if (parada.tipo === "ambos") {
                iconoSeleccionado = pinAmbos;
            }

            const marcador = L.marker([parada.lat, parada.lng], { icon: iconoSeleccionado })
                .addTo(mapa)
                .bindPopup(`<strong>${parada.nombre}</strong><br>Tipo: ${parada.tipo || 'interurbano'}`);
            
            marcadoresParadas.push(marcador);
        });
    })
    .catch(error => {
        console.log("Error al cargar las paradas:", error);
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

    // Seleccionar las 6 más cercanas
    const paradasCercanas = paradasConDistancia.slice(0, 6);
    
    const tbody = document.getElementById("tablaParadasCercanas");
    tbody.innerHTML = "";

    // Mostrar la sección de la tabla de resultados
    const resultadosParadas = document.getElementById("resultadosParadas");
    resultadosParadas.style.display = "block";

    paradasCercanas.forEach(parada => {
        const fila = document.createElement("tr");

        // Columna 1: Nombre de la Parada con etiqueta según su tipo
        const colNombre = document.createElement("td");
        let badgeTipo = '';
        if (parada.tipo === "urbano") {
            badgeTipo = ' <span style="background:#28a745; color:white; padding:2px 6px; border-radius:4px; font-size:11px;">Urbano</span>';
        } else if (parada.tipo === "ambos") {
            badgeTipo = ' <span style="background:#ffc107; color:black; padding:2px 6px; border-radius:4px; font-size:11px;">Ambos</span>';
        } else {
            badgeTipo = ' <span style="background:#007bff; color:white; padding:2px 6px; border-radius:4px; font-size:11px;">Interurbano</span>';
        }


        colNombre.innerHTML = `🚏 ${parada.nombre}${badgeTipo}`;

        // Columna 2: Distancia
        const colDistancia = document.createElement("td");
        colDistancia.textContent = `A ${Math.round(parada.distancia)} metros`;

        // Columna 3: Botón de acción para ver en el mapa
        const colAccion = document.createElement("td");
        const btnVer = document.createElement("button");
        btnVer.textContent = "Ver Ruta";
        btnVer.className = "btn btn-sm btn-primary";
        
        btnVer.addEventListener("click", () => {
            mapa.setView([parada.lat, parada.lng], 17);

            const marcador = marcadoresParadas.find(m => {
                const posicion = m.getLatLng();
                return (posicion.lat === parada.lat && posicion.lng === parada.lng);
            });

            if (marcador) {
                marcador.openPopup();
            }

            // Desplazamiento suave hacia el mapa
            document.getElementById("mapa").scrollIntoView({ behavior: "smooth", block: "center" });
        });

        colAccion.appendChild(btnVer);

        // Agregar celdas a la fila
        fila.appendChild(colNombre);
        fila.appendChild(colDistancia);
        fila.appendChild(colAccion);

        // Agregar fila a la tabla
        tbody.appendChild(fila);
    });

}