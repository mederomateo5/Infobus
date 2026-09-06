// ============================================================================
// GESTIÓN DE USUARIOS
// ============================================================================

function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center;">
          No se encontraron resultados.
        </td>
      </tr>
    `;
    return;
  }

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

    const tdAcciones = document.createElement("td");
    tdAcciones.className = "text-center px-3 py-2";

    const btn1 = document.createElement("button");
    btn1.textContent = "Eliminar";
    btn1.className = "btn btn-sm btn-danger me-2";

    btn1.addEventListener("click", () => {
      if (confirm(`¿Estás seguro de dar de baja a ${usuario.nombre ?? "este usuario"}?`)) {
        const formData = new FormData();
        formData.append("id", usuario.id);

        // Ajuste de ruta hacia la carpeta baja/
        fetch("baja/baja_usuario.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => {
            if (response.redirected) {
              window.location.href = response.url;
            } else {
              tr.remove();
            }
          })
          .catch((error) => {
            console.error("Error al dar de baja:", error);
            alert("Ocurrió un error al intentar eliminar el usuario.");
          });
      }
    });

    const btn2 = document.createElement("button");
    btn2.textContent = "Editar";
    btn2.className = "btn btn-sm btn-warning";
    btn2.addEventListener("click", () => {
      window.location.href = `actualizar_usuario_gui.php?id=${usuario.id}`;
    });

    tr.appendChild(tdId);
    tr.appendChild(tdNombre);
    tr.appendChild(tdApellido);
    tr.appendChild(tdEmail);
    tr.appendChild(tdTelefono);
    tr.appendChild(tdTipo);
    
    tdAcciones.appendChild(btn1);
    tdAcciones.appendChild(btn2);
    tr.appendChild(tdAcciones);

    contenedorResultado.appendChild(tr);
  });
}

function buscar() {
  const texto = document.getElementById("busqueda").value.trim();
  const contenedorResultado = document.getElementById("resultado");

  if (texto === "") {
    contenedorResultado.innerHTML = "";
    return;
  }

  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="7" style="text-align: center;">
        Buscando...
      </td>
    </tr>
  `;

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
          <td colspan="7" style="text-align: center; color: red;">
            Ocurrió un error al realizar la búsqueda.
          </td>
        </tr>
      `;
    });
}

function mostrarTodo() {
  const contenedorResultado = document.getElementById("resultado");

  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="6" style="text-align: center;">
        Cargando usuarios...
      </td>
    </tr>
  `;

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
// GESTIÓN DE VIAJES
// ==========================================

function mostrarResultadosViajes(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

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

  contenedorResultado.innerHTML = `
    <tr>
      <td colspan="10" style="text-align: center;">
        Cargando viajes...
      </td>
    </tr>
  `;

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