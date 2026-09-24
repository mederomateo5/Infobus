// ============================================================================
// GESTIÓN Y BÚSQUEDA DE COMPAÑÍAS
// ============================================================================

function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-3">
          No se encontraron compañías registradas.
        </td>
      </tr>
    `;
    return;
  }

  datos.resultados.forEach((compania) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = compania.id ?? "-";
    tdId.className = "px-3";

    const tdNombre = document.createElement("td");
    tdNombre.textContent = compania.nombre ?? "-";

    const tdEmail = document.createElement("td");
    tdEmail.textContent = compania.email ?? "-";

    const tdTelefono = document.createElement("td");
    tdTelefono.textContent = compania.telefono ?? "-";

    const tdAcciones = document.createElement("td");
    tdAcciones.className = "text-center px-3 py-2";

    const btnBaja = document.createElement("button");
    btnBaja.textContent = "Eliminar";
    btnBaja.className = "btn btn-sm btn-danger me-2";

    btnBaja.addEventListener("click", () => {
      if (confirm(`¿Estás seguro de eliminar la compañía ${compania.nombre}?`)) {
        const formData = new FormData();
        formData.append("id", compania.id);

        fetch("baja/baja_compania.php", {
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
            alert("Ocurrió un error al intentar eliminar la compañía.");
          });
      }
    });

    const btnEditar = document.createElement("button");
    btnEditar.textContent = "Editar";
    btnEditar.className = "btn btn-sm btn-warning";
    btnEditar.addEventListener("click", () => {
      window.location.href = `actualizar/actualizar_compania_gui.php?id=${compania.id}`;
    });

    tr.appendChild(tdId);
    tr.appendChild(tdNombre);
    tr.appendChild(tdEmail);
    tr.appendChild(tdTelefono);

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

  fetch("buscador/buscador_compania.php", {
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
        Cargando compañías...
      </td>
    </tr>
  `;

  fetch("buscador/buscador_compania.php", {
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
      console.error("Error al cargar compañías:", error);
      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-danger py-3">
            Ocurrió un error al cargar los datos.
          </td>
        </tr>
      `;
    });
}