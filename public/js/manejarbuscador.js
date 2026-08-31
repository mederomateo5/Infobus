// ============================================================================
// FUNCIONES PARA GESTIONAR Y MOSTRAR USUARIOS
// ============================================================================

// Esta función se encarga de pintar la lista de usuarios en la tabla de HTML
// Esta función se encarga de pintar la lista de usuarios en la tabla de HTML y vincular sus acciones
function mostrarResultados(datos) {
  // Buscamos en el HTML el elemento contenedor (por ejemplo, el <tbody>) con id="resultado"
  const contenedorResultado = document.getElementById("resultado");

  // Vaciamos la tabla para borrar resultados de búsquedas anteriores
  contenedorResultado.innerHTML = "";

  // Validamos si la respuesta del servidor no fue exitosa, no tiene la propiedad 'resultados' o viene vacía
  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    // Si no hay datos, insertamos un mensaje informativo indicando que no hay registros
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center;">
          No se encontraron resultados.
        </td>
      </tr>
    `;
    return; // "return" detiene la ejecución para no intentar dibujar celdas vacías
  }

  // Recorremos la lista de usuarios obtenida utilizando el método forEach
  datos.resultados.forEach((usuario) => {
    // Creamos en memoria un nuevo elemento contenedor de fila para la tabla: <tr>
    const tr = document.createElement("tr");

    // Creamos la celda <td> para el ID del usuario
    const tdId = document.createElement("td");
    // Asignamos su valor. El operador '??' asigna "Sin ID" en caso de que usuario.id sea null o undefined
    tdId.textContent = usuario.id ?? "Sin ID";

    // Creamos la celda <td> para el Nombre
    const tdNombre = document.createElement("td");
    tdNombre.textContent = usuario.nombre ?? "Sin nombre";

    // Creamos la celda <td> para el Apellido
    const tdApellido = document.createElement("td");
    tdApellido.textContent = usuario.apellido ?? "Sin apellido";

    // Creamos la celda <td> para el Email
    const tdEmail = document.createElement("td");
    tdEmail.textContent = usuario.email ?? "Sin correo";

    // Creamos la celda <td> para el Teléfono
    const tdTelefono = document.createElement("td");
    tdTelefono.textContent = usuario.telefono ?? "Sin teléfono";

    // Creamos la celda <td> para el Tipo de usuario (ej: administrador, cliente)
    const tdTipo = document.createElement("td");
    tdTipo.textContent = usuario.tipo ?? "Sin tipo";

    // Creamos la celda <td> que contendrá los botones de acción
    const tdAcciones = document.createElement("td");

    // Creamos el elemento HTML <button> para la acción de Eliminar
    const btn1 = document.createElement("button");
    btn1.textContent = "Eliminar"; // Le ponemos la etiqueta visible al botón

    // Escuchamos el evento de clic en el botón Eliminar
    btn1.addEventListener("click", () => {
      // Mostramos una ventana emergente para confirmar la acción antes de borrar
      if (confirm(`¿Estás seguro de dar de baja a ${usuario.nombre ?? "este usuario"}?`)) {
        
        // Instanciamos un objeto FormData para simular el envío de un formulario tradicional HTML
        const formData = new FormData();
        // Agregamos la clave 'id' que PHP leerá mediante $_POST['id']
        formData.append("id", usuario.id);

        // Realizamos la petición HTTP asíncrona hacia el archivo backend
        fetch("baja_usuario.php", {
          method: "POST",   // Especificamos que enviamos la petición vía POST
          body: formData,   // Le pasamos nuestro FormData con el ID
        })
          .then((response) => {
            // Evaluamos si el script PHP respondió redirigiendo a la pantalla del panel
            if (response.redirected) {
              // Si hubo redirección (header('Location:...')), llevamos al navegador hacia esa URL
              window.location.href = response.url;
            } else {
              // Si no hubo redirección, eliminamos directamente la fila <tr> del DOM en el cliente
              tr.remove();
            }
          })
          .catch((error) => {
            // Capturamos cualquier error de conexión o fallo técnico durante la petición
            console.error("Error al dar de baja:", error);
            alert("Ocurrió un error al intentar eliminar el usuario.");
          });
      }
    });

    // Creamos el elemento HTML <button> para la acción de Editar
    const btn2 = document.createElement("button");
    btn2.textContent = "Editar";

    // Insertamos todas las celdas individuales dentro de nuestra fila <tr>
    tr.appendChild(tdId);
    tr.appendChild(tdNombre);
    tr.appendChild(tdApellido);
    tr.appendChild(tdEmail);
    tr.appendChild(tdTelefono);
    tr.appendChild(tdTipo);
    
    // Agregamos ambos botones dentro de la celda de acciones
    tdAcciones.appendChild(btn1);
    tdAcciones.appendChild(btn2);
    
    // Insertamos la celda de acciones dentro de la fila <tr>
    tr.appendChild(tdAcciones);

    // Finalmente, añadimos la fila completa con todos sus datos y botones dentro del <tbody> del HTML
    contenedorResultado.appendChild(tr);
  });
}

// Función para realizar la búsqueda de usuarios ingresada en un cuadro de texto
function buscar() {
  // Obtenemos el texto ingresado en el input id="busqueda" y le quitamos los espacios de los bordes con .trim()
  const texto = document.getElementById("busqueda").value.trim();
  const contenedorResultado = document.getElementById("resultado");

  // Si el usuario borró todo y el input está vacío, limpiamos la tabla y cortamos la función
  if (texto === "") {
    contenedorResultado.innerHTML = "";
    return;
  }

  // Insertamos temporalmente un mensaje de "Buscando..." para dar feedback visual
  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="7" style="text-align: center;">
        Buscando...
      </td>
    </tr>
  `;

  // fetch() realiza una petición HTTP asíncrona hacia el servidor PHP
  fetch("buscador.php", {
    method: "POST", // Enviamos los datos mediante el método POST
    headers: {
      "Content-Type": "application/json", // Especificamos que enviamos información en formato JSON
    },
    body: JSON.stringify({
      busqueda: texto, // Convertimos nuestro objeto JS a texto JSON para enviarlo
    }),
  })
    .then((response) => {
      // Primera promesa: verificamos si el servidor respondió correctamente (código HTTP 200 OK)
      if (!response.ok) {
        throw new Error(`Error en la petición: ${response.status}`);
      }

      return response.json(); // Convertimos la respuesta recibida en JSON a un objeto manipulable por JS
    })
    .then((datos) => {
      // Segunda promesa: tomamos los datos ya procesados y se los pasamos a la función encargada de dibujarlos
      mostrarResultados(datos);
    })
    .catch((error) => {
      // Capturamos cualquier falla en la conexión o en el procesamiento
      console.error("Error al buscar:", error);

      // Le mostramos al usuario un mensaje claro de error dentro de la tabla
      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; color: red;">
            Ocurrió un error al realizar la búsqueda.
          </td>
        </tr>
      `;
    });
}

// Función para solicitar y mostrar la lista completa de usuarios
function mostrarTodo() {
  const contenedorResultado = document.getElementById("resultado");

  // Mostramos mensaje de carga previo
  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="6" style="text-align: center;">
        Cargando usuarios...
      </td>
    </tr>
  `;

  // Realizamos la consulta al archivo PHP solicitando todos los registros
  fetch("buscador.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      mostrarTodo: true, // Le enviamos una señal al servidor indicando que queremos la lista completa
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error en la petición: ${response.status}`);
      }

      return response.json();
    })
    .then((datos) => {
      mostrarResultados(datos);
    })
    .catch((error) => {
      console.error("Error al mostrar usuarios:", error);

      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="6" style="text-align: center; color: red;">
            Ocurrió un error al cargar los usuarios.
          </td>
        </tr>
      `;
    });
}

// ==========================================
// FUNCIONES NUEVAS PARA VIAJES
// (no tocan nada de lo que ya funciona con usuarios)
// ==========================================

// Esta función lee datos de viajes y construye las filas de la tabla de viajes
function mostrarResultadosViajes(datos) {
  const contenedorResultado = document.getElementById("resultado");

  // Limpiamos la tabla
  contenedorResultado.innerHTML = "";

  // Validamos si la respuesta del servidor indica éxito o si hay viajes devueltos
  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="10" style="text-align: center;">
          No se encontraron viajes.
        </td>
      </tr>
    `;
    return;
  }

  // Recorremos el arreglo de viajes devuelto por la base de datos
  datos.resultados.forEach((viaje) => {
    // Creamos la fila para el viaje
    const tr = document.createElement("tr");

    // Celda ID del viaje
    const tdId = document.createElement("td");
    tdId.textContent = viaje.id ?? "Sin ID";

    // Celda Ómnibus asignado
    const tdOmnibus = document.createElement("td");
    tdOmnibus.textContent = viaje.omnibus_id ?? "Sin ómnibus";

    // Celda Origen
    const tdOrigen = document.createElement("td");
    tdOrigen.textContent = viaje.origen ?? "Sin origen";

    // Celda Destino
    const tdDestino = document.createElement("td");
    tdDestino.textContent = viaje.destino ?? "Sin destino";

    // Celda Precio
    const tdPrecio = document.createElement("td");
    tdPrecio.textContent = viaje.precio ?? "Sin precio";

    // Celda Fecha
    const tdFecha = document.createElement("td");
    tdFecha.textContent = viaje.fecha ?? "Sin fecha";

    // Celda Hora
    const tdHora = document.createElement("td");
    tdHora.textContent = viaje.hora ?? "Sin hora";

    // Celda Hora de llegada estimada
    const tdHoraLlegada = document.createElement("td");
    tdHoraLlegada.textContent = viaje.hora_llegada ?? "Sin hora de llegada";

    // Celda Estado del viaje
    const tdEstado = document.createElement("td");
    // Evaluamos el campo 'ya_salio' (que devuelve 1 o 0 de la BD) mediante un operador ternario:
    // Si equivale a 1 asigna "Ya salió", en caso contrario "Pendiente"
    tdEstado.textContent = (Number(viaje.ya_salio) === 1) ? "Ya salió" : "Pendiente";

    // Insertamos todas las celdas creadas en la fila correspondiente
    tr.appendChild(tdId);
    tr.appendChild(tdOmnibus);
    tr.appendChild(tdOrigen);
    tr.appendChild(tdDestino);
    tr.appendChild(tdPrecio);
    tr.appendChild(tdFecha);
    tr.appendChild(tdHora);
    tr.appendChild(tdHoraLlegada);
    tr.appendChild(tdEstado);

    // Agregamos la fila con la información completa al contenedor en la página
    contenedorResultado.appendChild(tr);
  });
}

// Función para solicitar y mostrar la lista completa de viajes
function mostrarTodoViajes() {
  const contenedorResultado = document.getElementById("resultado");

  // Mostramos el estado de carga inicial en la tabla
  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="10" style="text-align: center;">
        Cargando viajes...
      </td>
    </tr>
  `;

  // Consulta asíncrona enviando el parámetro 'mostrarTodoViajes'
  fetch("buscador.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      mostrarTodoViajes: true,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error en la petición: ${response.status}`);
      }

      return response.json();
    })
    .then((datos) => {
      // Le pasamos el resultado a la función encargada de generar la interfaz para los viajes
      mostrarResultadosViajes(datos);
    })
    .catch((error) => {
      console.error("Error al mostrar viajes:", error);

      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="10" style="text-align: center; color: red;">
            Ocurrió un error al cargar los viajes.
          </td>
        </tr>
      `;
    });
}