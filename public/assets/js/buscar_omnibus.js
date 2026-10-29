// ============================================================================
// GESTIÓN Y BÚSQUEDA DE ÓMNIBUS
// ============================================================================

function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="6" class="text-center py-3">
          No se encontraron ómnibus registrados.
        </td>
      </tr>
    `;
    return;
  }

  datos.resultados.forEach((omnibus) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = omnibus.id ?? "-";
    tdId.className = "px-3";

    const tdCompania = document.createElement("td");
    tdCompania.textContent = omnibus.compania_nombre ?? "Sin compañía";

    const tdCoche = document.createElement("td");
    tdCoche.textContent = omnibus.n_omnibus ?? "-";

    const tdAsientos = document.createElement("td");
    tdAsientos.textContent = omnibus.asientos ?? "0";

    const tdRutaKey = document.createElement("td");
    tdRutaKey.textContent = omnibus.ruta_key ?? "-";

    const tdAcciones = document.createElement("td");
    tdAcciones.className = "text-center px-3 py-2";

    const btnBaja = document.createElement("button");
    btnBaja.textContent = "Eliminar";
    btnBaja.className = "btn btn-sm btn-danger me-2";

    btnBaja.addEventListener("click", () => {
      if (confirm(`¿Estás seguro de eliminar el coche #${omnibus.n_omnibus} (${omnibus.compania_nombre})?`)) {
        const formData = new FormData();
        formData.append("id", omnibus.id);

        fetch("../baja/baja_omnibus.php", {
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
            alert("Ocurrió un error al intentar eliminar el ómnibus.");
          });
      }
    });

    const btnEditar = document.createElement("button");
    btnEditar.textContent = "Editar";
    btnEditar.className = "btn btn-sm btn-warning";
    btnEditar.addEventListener("click", () => {
      window.location.href = `../actualizar/actualizar_omnibus_gui.php?id=${omnibus.id}`;
    });

    tr.appendChild(tdId);
    tr.appendChild(tdCompania);
    tr.appendChild(tdCoche);
    tr.appendChild(tdAsientos);
    tr.appendChild(tdRutaKey);

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
      <td colspan="6" class="text-center py-3">
        Buscando...
      </td>
    </tr>
  `;

  fetch("../buscador/buscador_omnibus.php", {
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
          <td colspan="6" class="text-center text-danger py-3">
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
      <td colspan="6" class="text-center py-3">
        Cargando ómnibus...
      </td>
    </tr>
  `;

  fetch("../buscador/buscador_omnibus.php", {
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
      console.error("Error al cargar ómnibus:", error);
      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-danger py-3">
            Ocurrió un error al cargar los datos.
          </td>
        </tr>
      `;
    });
}