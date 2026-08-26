// Busca el botón "btnUbicacion" que tenemos en el HTML y lo guarda en una variable.
const btnUbicacion = document.getElementById("btnUbicacion");


// Crea el mapa dentro del elemento HTML que tiene id="mapa".
// Las coordenadas [-34.4, -57.45] indican dónde comienza centrado el mapa.
// El número 13 indica el nivel de zoom inicial.
const mapa = L.map("mapa").setView([-34.4, -57.45], 13);


// Agrega las imágenes que forman el mapa.
// En este caso utilizamos OpenStreetMap como fuente de los mapas.
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {

  // Indica quién proporciona los datos del mapa.
  attribution: "&copy; OpenStreetMap contributors",

// Agrega esta capa de mapa al mapa que creamos anteriormente.
}).addTo(mapa);


// Creamos variables para guardar la latitud y longitud del usuario.
// Al principio tienen el valor null porque todavía no conocemos su ubicación.
let latUsuario = null;
let lngUsuario = null;


// Creamos un arreglo vacío donde posteriormente vamos a guardar
// todas las paradas que obtenemos desde nuestro archivo JSON.
// Mientras que el otro arreglo es para donde se van a guardar los marcadores de Leaflet.
let paradas = [];
let marcadoresParadas = [];


// Detecta cuando el usuario hace clic en el botón.
// Cuando se hace clic, ejecuta la función obtenerUbicacion().
btnUbicacion.addEventListener("click", obtenerUbicacion);


// Esta función se ejecuta cuando el usuario presiona el botón.
function obtenerUbicacion() {

  // Le pide al navegador la ubicación actual del usuario.
  // Si obtiene correctamente la ubicación, ejecuta mostrarUbicacion().
  // Si ocurre un error, ejecuta mostrarError().
  navigator.geolocation.getCurrentPosition(
    mostrarUbicacion,
    mostrarError
  );
}


// Esta función recibe la ubicación que nos proporciona el navegador.
function mostrarUbicacion(position) {

  // Obtiene la latitud de la ubicación del usuario.
  const latitud = position.coords.latitude;

  // Obtiene la longitud de la ubicación del usuario.
  const longitud = position.coords.longitude;


  // Guardamos la latitud y longitud en nuestras variables
  // para poder utilizarlas después para calcular las distancias.
  latUsuario = latitud;
  lngUsuario = longitud;


  // Mueve el mapa hasta la ubicación del usuario
  // y establece el nivel de zoom en 16.
  mapa.setView([latitud, longitud], 16);


  // Crea un marcador en la ubicación del usuario.
  L.marker([latitud, longitud])

    // Agrega el marcador al mapa.
    .addTo(mapa)

    // Agrega un cartel que dice "Tu ubicación"
    // cuando se selecciona el marcador.
    .bindPopup("📍 Tu ubicación")

    // Abre automáticamente el cartel del marcador.
    .openPopup();


  // Una vez que conocemos la ubicación del usuario,
  // calculamos cuáles son las paradas más cercanas.
  calcularParadasCercanas();
}


// Esta función se ejecuta si el navegador no puede obtener
// la ubicación del usuario.
function mostrarError(error) {

  // Muestra un mensaje de error en la consola del navegador.
  console.log("No se pudo obtener la ubicación");
}


// Utilizamos fetch() para pedir y cargar nuestro archivo JSON
// que contiene todas las paradas.
fetch("../data/paradas.json")

  // Convierte la respuesta obtenida del archivo JSON
  // en datos que JavaScript puede utilizar.
  .then(response => response.json())

  // Cuando el JSON ya fue convertido, recibimos los datos
  // y los guardamos en nuestro arreglo de paradas.
.then(datos => {

    paradas = datos;

    console.log("Paradas cargadas:", paradas);

    paradas.forEach(parada => {

        const marcador = L.marker([parada.lat, parada.lng])
            .addTo(mapa)
            .bindPopup(parada.nombre);

        marcadoresParadas.push(marcador);

    });

})

  // Si ocurre algún error al cargar el archivo JSON,
  // se ejecuta esta parte.
  .catch(error => {

    // Muestra el error en la consola para poder identificar el problema.
    console.log("Error al cargar las paradas:", error);

  });


// Esta función calcula cuáles son las paradas más cercanas
// a la ubicación actual del usuario.
function calcularParadasCercanas() {


  // Recorremos todas las paradas del arreglo.
  // map() crea un nuevo arreglo donde cada parada tendrá
  // también su distancia hasta el usuario.
  const paradasConDistancia = paradas.map(parada => {


    // Calcula la distancia entre la ubicación del usuario
    // y la ubicación de la parada.
    // Leaflet devuelve la distancia en metros.
    const distancia = mapa.distance(
      [latUsuario, lngUsuario],
      [parada.lat, parada.lng]
    );


    // Devuelve los datos originales de la parada
    // y agrega una nueva propiedad llamada "distancia".
    return {
      ...parada,
      distancia: distancia
    };

  });


  // Ordena las paradas desde la más cercana hasta la más lejana.
  // "a" y "b" representan dos paradas que se están comparando.
  // Si el resultado es negativo, "a" queda antes que "b".
  paradasConDistancia.sort((a, b) => a.distancia - b.distancia);


  // Selecciona solamente las primeras 5 paradas del arreglo.
  // Como anteriormente las ordenamos por distancia,
  // estas serán las 5 paradas más cercanas.
  const paradasCercanas = paradasConDistancia.slice(0, 5);


  // Busca en el HTML el elemento que tiene id="listaParadas".
  // Ahí vamos a mostrar las paradas cercanas.
  const lista = document.getElementById("listaParadas");


  // Limpia el contenido anterior de la lista.
  // Esto evita que las paradas se dupliquen si presionamos
  // el botón varias veces.
  lista.innerHTML = "";


  // Recorremos las 5 paradas más cercanas.
paradasCercanas.forEach(parada => {

    // Creamos una tarjeta de Bootstrap
    const tarjeta = document.createElement("div");
    tarjeta.classList.add("card", "mb-3");

    // Creamos el contenido de la tarjeta
    const contenido = document.createElement("div");
    contenido.classList.add("card-body");

    // Creamos el nombre de la parada
    const nombre = document.createElement("h5");
    nombre.classList.add("card-title");

    nombre.textContent = `🚏 ${parada.nombre}`;

    // Creamos el texto de la distancia
    const distancia = document.createElement("p");
    distancia.classList.add("card-text");

    distancia.textContent =
        `📍 A ${Math.round(parada.distancia)} metros`;

    // Metemos el nombre y la distancia dentro de la tarjeta
    contenido.appendChild(nombre);
    contenido.appendChild(distancia);

    // Metemos el contenido dentro de la tarjeta
    tarjeta.appendChild(contenido);

    // Cuando hacemos clic en la tarjeta,
    // el mapa se mueve hasta la parada.
    tarjeta.addEventListener("click", () => {

        mapa.setView([parada.lat, parada.lng], 17);

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

    // Agregamos la tarjeta a la lista
    lista.appendChild(tarjeta);

});

}