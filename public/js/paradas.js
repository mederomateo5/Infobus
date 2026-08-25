const btnUbicacion = document.getElementById("btnUbicacion");

const mapa = L.map("mapa").setView([-34.4, -57.45], 13); // Acá se crea el mapa con coordenadas y zoom ("13")
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { // Esto agrega las imágenes que forman el mapa. (OpenStreetMap)
  attribution: "&copy; OpenStreetMap contributors",
}).addTo(mapa);

btnUbicacion.addEventListener("click", obtenerUbicacion);

function obtenerUbicacion() {
  navigator.geolocation.getCurrentPosition(mostrarUbicacion, mostrarError);
}

function mostrarUbicacion(position) {
  const latitud = position.coords.latitude;
  const longitud = position.coords.longitude;

  mapa.setView([latitud, longitud], 16); // Esto mueve el mapa hasta las coordenadas y poné el zoom en 16."

  L.marker([latitud, longitud]) // Esto crea un marcador en esas coordenadas.
    .addTo(mapa) // Lo agrega al mapa
    .bindPopup("📍 Tu ubicación") // Esto hace que cuando toquemos el marcador aparezca un cartelito
    .openPopup(); // Y esto lo abre automaticamente

}

function mostrarError(error) {
  console.log("No se pudo obtener la ubicación");
}

fetch("../data/paradas.json") // Fetch para sacar las paradas
    .then(response => response.json())
    .then(paradas => {

        console.log(paradas);

    })
    .catch(error => {

        console.log("Error al cargar las paradas:", error);

    });
