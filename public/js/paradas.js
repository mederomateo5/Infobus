// Busca el botón "btnUbicacion" que tenemos en el HTML y lo guarda en una variable.
const btnUbicacion = document.getElementById("btnUbicacion");


// Crea el mapa dentro del elemento HTML que tiene id="mapa".
const mapa = L.map("mapa").setView([-34.4, -57.45], 13);


// Agrega las imágenes que forman el mapa.
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {

  attribution: "&copy; OpenStreetMap contributors"

}).addTo(mapa);


// Creamos variables para guardar la latitud y longitud del usuario.
let latUsuario = null;
let lngUsuario = null;


// Arreglo donde vamos a guardar las paradas obtenidas desde la base de datos.
// El otro arreglo guarda los marcadores de Leaflet.
let paradas = [];
let marcadoresParadas = [];


// Detecta cuando el usuario hace clic en el botón.
btnUbicacion.addEventListener("click", obtenerUbicacion);


// Esta función se ejecuta cuando el usuario presiona el botón.
function obtenerUbicacion() {

  navigator.geolocation.getCurrentPosition(
    mostrarUbicacion,
    mostrarError
  );
}


// Esta función recibe la ubicación que proporciona el navegador.
function mostrarUbicacion(position) {

  const latitud = position.coords.latitude;
  const longitud = position.coords.longitude;


  // Guardamos la ubicación del usuario.
  latUsuario = latitud;
  lngUsuario = longitud;


  // Mueve el mapa hasta la ubicación del usuario.
  mapa.setView([latitud, longitud], 16);


  // Crea un marcador en la ubicación del usuario.
  L.marker([latitud, longitud])
    .addTo(mapa)
    .bindPopup("📍 Tu ubicación")
    .openPopup();


  // Calculamos las paradas más cercanas.
  calcularParadasCercanas();
}


// Esta función se ejecuta si el navegador no puede obtener
// la ubicación del usuario.
function mostrarError(error) {

  console.log("No se pudo obtener la ubicación");

}


// ============================================================
// OBTENER PARADAS DESDE LA BASE DE DATOS
// ============================================================

// En lugar de leer paradas.json,
// hacemos una petición a PHP.
//
// PHP consulta la base de datos y devuelve las paradas
// en formato JSON.
fetch("obtener_paradas.php")

  // Convierte la respuesta JSON
  // en datos que JavaScript puede utilizar.
  .then(response => {

    if (!response.ok) {
      throw new Error("Error al obtener las paradas");
    }

    return response.json();

  })

  // Guardamos las paradas obtenidas.
  .then(datos => {

    paradas = datos;

    console.log("Paradas cargadas desde la base de datos:", paradas);


    // Creamos un marcador para cada parada.
    paradas.forEach(parada => {

      const marcador = L.marker([
        parada.lat,
        parada.lng
      ])
        .addTo(mapa)
        .bindPopup(parada.nombre);


      marcadoresParadas.push(marcador);

    });

  })

  // Si ocurre algún error.
  .catch(error => {

    console.log("Error al cargar las paradas:", error);

  });


// ============================================================
// CALCULAR PARADAS CERCANAS
// ============================================================

function calcularParadasCercanas() {


  // Recorremos todas las paradas.
  const paradasConDistancia = paradas.map(parada => {


    // Calculamos la distancia entre el usuario y la parada.
    const distancia = mapa.distance(
      [latUsuario, lngUsuario],
      [parada.lat, parada.lng]
    );


    // Devolvemos los datos de la parada
    // junto con la distancia calculada.
    return {
      ...parada,
      distancia: distancia
    };

  });


  // Ordenamos las paradas desde la más cercana
  // hasta la más lejana.
  paradasConDistancia.sort(
    (a, b) => a.distancia - b.distancia
  );


  // Seleccionamos las primeras 5.
  const paradasCercanas = paradasConDistancia.slice(0, 5);


  // Buscamos el elemento donde mostraremos las paradas.
  const lista = document.getElementById("listaParadas");


  // Limpiamos la lista anterior.
  lista.innerHTML = "";


  // Recorremos las 5 paradas más cercanas.
  paradasCercanas.forEach(parada => {


    // Creamos una tarjeta de Bootstrap.
    const tarjeta = document.createElement("div");

    tarjeta.classList.add(
      "card",
      "mb-3"
    );


    // Creamos el contenido de la tarjeta.
    const contenido = document.createElement("div");

    contenido.classList.add("card-body");


    // Creamos el nombre de la parada.
    const nombre = document.createElement("h5");

    nombre.classList.add("card-title");

    nombre.textContent = `🚏 ${parada.nombre}`;


    // Creamos el texto de distancia.
    const distancia = document.createElement("p");

    distancia.classList.add("card-text");

    distancia.textContent =
      `📍 A ${Math.round(parada.distancia)} metros`;


    // Metemos el nombre y la distancia dentro de la tarjeta.
    contenido.appendChild(nombre);
    contenido.appendChild(distancia);


    // Metemos el contenido dentro de la tarjeta.
    tarjeta.appendChild(contenido);


    // Cuando hacemos clic en la tarjeta,
    // el mapa se mueve hasta la parada.
    tarjeta.addEventListener("click", () => {


      mapa.setView(
        [parada.lat, parada.lng],
        17
      );


      const marcador = marcadoresParadas.find(marcador => {

        const posicion = marcador.getLatLng();


        return (
          posicion.lat === parada.lat &&
          posicion.lng === parada.lng
        );

      });


      if (marcador) {

        marcador.openPopup();

      }

    });


    // Agregamos la tarjeta a la lista.
    lista.appendChild(tarjeta);

  });

}