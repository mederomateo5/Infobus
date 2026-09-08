// ============================================================================
// GESTIÓN Y BÚSQUEDA DE RUTAS / RECORRIDOS
// ============================================================================

function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-3">
          No se encontraron tramos de recorrido asignados.
        </td>
      </tr>
    `;
    return;
  }

  datos.resultados.forEach((recorrido) => {
    const tr = document.createElement("tr");

    const tdCoche = document.createElement("td");
    tdCoche.textContent = `Coche #${recorrido.n_omnibus}`;
    tdCoche.className = "px-3";

    const tdCompania = document.createElement("td");
    tdCompania.textContent = recorrido.compania_nombre ?? "-";

    const tdParada = document.createElement("td");
    tdParada.textContent = recorrido.parada_nombre ?? "-";

    const tdOrden = document.createElement("td");
    tdOrden.textContent = `Posición #${recorrido.orden}`;

    const tdAcciones = document.createElement("td");
    tdAcciones.className = "text-center px-3 py-2";

    const btnBaja = document.createElement("button");
    btnBaja.textContent = "Eliminar";
    btnBaja.className = "btn btn-sm btn-danger me-2";

    btnBaja.addEventListener("click", () => {
      if (confirm(`¿Quitar la parada "${recorrido.parada_nombre}" del coche #${recorrido.n_omnibus}?`)) {
        const formData = new FormData();
        formData.append("omnibus_id", recorrido.omnibus_id);
        formData.append("parada_id", recorrido.parada_id);

        fetch("baja/baja_ruta.php", {
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
            alert("Ocurrió un error al quitar la parada del recorrido.");
          });
      }
    });

    const btnEditar = document.createElement("button");
    btnEditar.textContent = "Editar";
    btnEditar.className = "btn btn-sm btn-warning";
    btnEditar.addEventListener("click", () => {
      window.location.href = `actualizar_ruta_gui.php?omnibus_id=${recorrido.omnibus_id}&parada_id=${recorrido.parada_id}`;
    });

    tr.appendChild(tdCoche);
    tr.appendChild(tdCompania);
    tr.appendChild(tdParada);
    tr.appendChild(tdOrden);

    tdAcciones.appendChild(btnBaja);
    tdAcciones.appendChild(btnEditar);
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
      <td colspan="5" class="text-center py-3">
        Buscando...
      </td>
    </tr>
  `;

  fetch("buscador_rutas.php", {
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
          <td colspan="5" class="text-center text-danger py-3">
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
      <td colspan="5" class="text-center py-3">
        Cargando recorridos...
      </td>
    </tr>
  `;

  fetch("buscador_rutas.php", {
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
      console.error("Error al cargar recorridos:", error);
      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-danger py-3">
            Ocurrió un error al cargar los datos.
          </td>
        </tr>
      `;
    });
}