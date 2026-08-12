function buscar() {
  // 1. Obtener y limpiar el texto del input
  let texto = document.getElementById("busqueda").value.trim();
  let contenedorResultado = document.getElementById("resultado"); // Ahora apunta al <tbody>

  // Validar si el campo está vacío antes de consultar
  if (texto === "") {
    contenedorResultado.innerHTML = "";
    return;
  }

  // Informar al usuario que la búsqueda está en curso (dentro de una fila de tabla)
  contenedorResultado.innerHTML = `<tr><td colspan="6" style="text-align: center;">Buscando...</td></tr>`;

  // 2. Realizar la petición a PHP
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
      // 3. Limpiar el tbody previo
      contenedorResultado.innerHTML = "";

      // 4. Verificar si hubo respuesta exitosa y si hay resultados
      if (!datos.exito || !datos.resultados || datos.resultados.length === 0) {
        contenedorResultado.innerHTML = `<tr><td colspan="6" style="text-align: center;">No se encontraron resultados.</td></tr>`;
        return;
      }

      // 5. Recorrer el array de resultados y construir las filas de la tabla
      datos.resultados.forEach((usuario) => {
        const tr = document.createElement("tr");

        // Crear celdas e insertar valores con textContent (seguro contra XSS)
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


        // Agregar las celdas a la fila
        tr.appendChild(tdId);
        tr.appendChild(tdNombre);
        tr.appendChild(tdApellido);
        tr.appendChild(tdEmail);
        tr.appendChild(tdTelefono);
        tr.appendChild(tdTipo);

        // Agregar la fila al <tbody>
        contenedorResultado.appendChild(tr);
      });
    })
    .catch((error) => {
      console.error("Error al buscar:", error);
      contenedorResultado.innerHTML =
        `<tr><td colspan="6" style="text-align: center; color: red;">Ocurrió un error al realizar la búsqueda.</td></tr>`;
    });
}