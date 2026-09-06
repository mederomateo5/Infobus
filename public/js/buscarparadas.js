// ============================================================================
// GESTIÓN DE PARADAS
// ============================================================================

function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="5" class="text-center">
          No se encontraron resultados.
        </td>
      </tr>
    `;
    return;
  }

  datos.resultados.forEach((parada) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = parada.id ?? "Sin ID";

    const tdNombre = document.createElement("td");
    tdNombre.textContent = parada.nombre ?? "Sin nombre";

    const tdLatitud = document.createElement("td");
    tdLatitud.textContent = parada.latitud ?? "Sin latitud";

    const tdLongitud = document.createElement("td");
    tdLongitud.textContent = parada.longitud ?? "Sin longitud";

    const tdAcciones = document.createElement("td");
    tdAcciones.className = "text-center px-3 py-2";

    // Botón Eliminar
    const btn1 = document.createElement("button");
    btn1.textContent = "Eliminar";
    btn1.className = "btn btn-sm btn-danger me-2";

    btn1.addEventListener("click", () => {
      if (confirm(`¿Estás seguro de eliminar la parada "${parada.nombre ?? 'sin nombre'}"?`)) {
        const formData = new FormData();
        formData.append("id", parada.id);

        // Ajuste de ruta hacia la carpeta baja/
        fetch("baja/baja_parada.php", {
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
            alert("Ocurrió un error al intentar eliminar la parada.");
          });
      }
    });

    // Botón Editar
    const btn2 = document.createElement("button");
    btn2.textContent = "Editar";
    btn2.className = "btn btn-sm btn-warning";

    btn2.addEventListener("click", () => {
      window.location.href = `actualizar_parada_gui.php?id=${parada.id}`;
    });

    // Construcción limpia de la fila HTML
    tr.appendChild(tdId);
    tr.appendChild(tdNombre);
    tr.appendChild(tdLatitud);
    tr.appendChild(tdLongitud);

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
      <td colspan="5" class="text-center">
        Buscando...
      </td>
    </tr>
  `;

  fetch("buscador_paradas.php", {
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
          <td colspan="5" class="text-center text-danger">
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
      <td colspan="5" class="text-center">
        Cargando paradas...
      </td>
    </tr>
  `;

  fetch("buscador_paradas.php", {
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
      console.error("Error al mostrar paradas:", error);
      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="5" class="text-center text-danger">
            Ocurrió un error al cargar las paradas.
          </td>
        </tr>
      `;
    });
}