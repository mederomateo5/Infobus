// ============================================================================
// GESTIÓN DE VIAJES
// ============================================================================

function mostrarResultados(datos) {
  const contenedorResultado = document.getElementById("resultado");
  contenedorResultado.innerHTML = "";

  if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
    contenedorResultado.innerHTML = `
      <tr>
        <td colspan="12" class="text-center py-3">
          No se encontraron viajes registrados.
        </td>
      </tr>
    `;
    return;
  }

  datos.resultados.forEach((viaje) => {
    const tr = document.createElement("tr");

    const tdId = document.createElement("td");
    tdId.textContent = viaje.id ?? "-";
    tdId.className = "px-3";

    const tdOmnibus = document.createElement("td");
    tdOmnibus.textContent = viaje.omnibus_id ?? "-";

    const tdOrigen = document.createElement("td");
    tdOrigen.textContent = viaje.origen_nombre ?? "Sin origen";

    const tdDestino = document.createElement("td");
    tdDestino.textContent = viaje.destino_nombre ?? "Sin destino";

    const tdPrecio = document.createElement("td");
    tdPrecio.textContent = viaje.precio ? `$${viaje.precio}` : "-";

    const tdAsientos = document.createElement("td");
    tdAsientos.textContent = viaje.asientos_disponibles ?? "0";

    const tdDirecto = document.createElement("td");
    tdDirecto.textContent = Number(viaje.es_directo) === 1 ? "Sí" : "No";

    const tdDias = document.createElement("td");
    tdDias.textContent = viaje.dias_semana ?? "Sin días asignados";

    const tdHora = document.createElement("td");
    tdHora.textContent = viaje.hora ?? "-";

    const tdHoraLlegada = document.createElement("td");
    tdHoraLlegada.textContent = viaje.hora_llegada ?? "-";

    const tdEstado = document.createElement("td");
    tdEstado.textContent = Number(viaje.ya_salio) === 1 ? "Ya salió" : "Pendiente";

    const tdAcciones = document.createElement("td");
    tdAcciones.className = "text-end px-3 py-2";

    const btn1 = document.createElement("button");
    btn1.textContent = "Eliminar";
    btn1.className = "btn btn-sm btn-danger me-2";

    btn1.addEventListener("click", () => {
      if (confirm(`¿Estás seguro de eliminar el viaje ID #${viaje.id}?`)) {
        const formData = new FormData();
        formData.append("id", viaje.id);

        fetch("../baja/baja_viaje.php", {
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
            alert("Ocurrió un error al intentar eliminar el viaje.");
          });
      }
    });

    const btn2 = document.createElement("button");
    btn2.textContent = "Editar";
    btn2.className = "btn btn-sm btn-warning";
    btn2.addEventListener("click", () => {
      window.location.href = `../actualizar/actualizar_viaje_gui.php?id=${viaje.id}`;
    });

    tr.appendChild(tdId);
    tr.appendChild(tdOmnibus);
    tr.appendChild(tdOrigen);
    tr.appendChild(tdDestino);
    tr.appendChild(tdPrecio);
    tr.appendChild(tdAsientos);
    tr.appendChild(tdDirecto);
    tr.appendChild(tdDias);
    tr.appendChild(tdHora);
    tr.appendChild(tdHoraLlegada);
    tr.appendChild(tdEstado);

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
      <td colspan="12" class="text-center py-3">
        Buscando...
      </td>
    </tr>
  `;

  fetch("../buscador/buscador_viajes.php", {
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
          <td colspan="12" class="text-center text-danger py-3">
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
      <td colspan="12" class="text-center py-3">
        Cargando viajes...
      </td>
    </tr>
  `;

  fetch("../buscador/buscador_viajes.php", {
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
      console.error("Error al mostrar viajes:", error);
      contenedorResultado.innerHTML = `
        <tr>
          <td colspan="12" class="text-center text-danger py-3">
            Ocurrió un error al cargar los viajes.
          </td>
        </tr>
      `;
    });
}