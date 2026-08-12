function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");

  // Limpiar resultados anteriores
  contenedorResultado.innerHTML = "";

  // Verificar si hubo resultados
  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center;">
          No se encontraron resultados.
        </td>
      </tr>
    `;
    return;
  }

  // Crear las filas
  datos.resultados.forEach((usuario) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = usuario.id ?? "Sin ID";

    const tdNombre = document.createElement("td");
    tdNombre.textContent = usuario.nombre ?? "Sin nombre";

    const tdApellido = document.createElement("td");
    tdApellido.textContent = usuario.apellido ?? "Sin apellido";

    const tdEmail = document.createElement("td");
    tdEmail.textContent = usuario.email ?? "Sin correo";

    const tdTelefono = document.createElement("td");
    tdTelefono.textContent = usuario.telefono ?? "Sin teléfono";

    const tdTipo = document.createElement("td");
    tdTipo.textContent = usuario.tipo ?? "Sin tipo";

    tr.appendChild(tdId);
    tr.appendChild(tdNombre);
    tr.appendChild(tdApellido);
    tr.appendChild(tdEmail);
    tr.appendChild(tdTelefono);
    tr.appendChild(tdTipo);

    contenedorResultado.appendChild(tr);
  });
}

function buscar() {
  const texto = document.getElementById("busqueda").value.trim();
  const contenedorResultado = document.getElementById("resultado");

  // Si está vacío, limpiar la tabla
  if (texto === "") {
    contenedorResultado.innerHTML = "";
    return;
  }

  // Mostrar mensaje de carga
  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="6" style="text-align: center;">
        Buscando...
      </td>
    </tr>
  `;

  // Petición a PHP
  fetch("buscador.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      busqueda: texto,
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
      console.error("Error al buscar:", error);

      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="6" style="text-align: center; color: red;">
            Ocurrió un error al realizar la búsqueda.
          </td>
        </tr>
      `;
    });
}

function mostrarTodo() {
  const contenedorResultado = document.getElementById("resultado");

  // Mostrar mensaje de carga
  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="6" style="text-align: center;">
        Cargando usuarios...
      </td>
    </tr>
  `;

  // Petición a PHP
  fetch("buscador.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      mostrarTodo: true,
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

function mostrarResultadosViajes(datos) {
  const contenedorResultado = document.getElementById("resultado");

  // Limpiar resultados anteriores
  contenedorResultado.innerHTML = "";

  // Verificar si hubo resultados
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

  // Crear las filas
  datos.resultados.forEach((viaje) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = viaje.id ?? "Sin ID";

    const tdOmnibus = document.createElement("td");
    tdOmnibus.textContent = viaje.omnibus_id ?? "Sin ómnibus";

    const tdOrigen = document.createElement("td");
    tdOrigen.textContent = viaje.origen ?? "Sin origen";

    const tdDestino = document.createElement("td");
    tdDestino.textContent = viaje.destino ?? "Sin destino";

    const tdPrecio = document.createElement("td");
    tdPrecio.textContent = viaje.precio ?? "Sin precio";

    const tdFecha = document.createElement("td");
    tdFecha.textContent = viaje.fecha ?? "Sin fecha";

    const tdHora = document.createElement("td");
    tdHora.textContent = viaje.hora ?? "Sin hora";

    const tdHoraLlegada = document.createElement("td");
    tdHoraLlegada.textContent = viaje.hora_llegada ?? "Sin hora de llegada";

    const tdEstado = document.createElement("td");
    // ya_salio suele venir como 0/1 desde la base de datos
    tdEstado.textContent = (Number(viaje.ya_salio) === 1) ? "Ya salió" : "Pendiente";

    tr.appendChild(tdId);
    tr.appendChild(tdOmnibus);
    tr.appendChild(tdOrigen);
    tr.appendChild(tdDestino);
    tr.appendChild(tdPrecio);
    tr.appendChild(tdFecha);
    tr.appendChild(tdHora);
    tr.appendChild(tdHoraLlegada);
    tr.appendChild(tdEstado);

    contenedorResultado.appendChild(tr);
  });
}

function mostrarTodoViajes() {
  const contenedorResultado = document.getElementById("resultado");

  // Mostrar mensaje de carga
  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="10" style="text-align: center;">
        Cargando viajes...
      </td>
    </tr>
  `;

  // Petición a PHP
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
