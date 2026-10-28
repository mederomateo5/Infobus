// Formulario, fetch y renderizado de tablas

let listaViajesGlobal = [];

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formBusqueda");
    const tbody = document.getElementById("tablaHorarios");
    const mensaje = document.getElementById("mensaje");
    const contenedorHorarios = document.getElementById("contenedorHorarios");

    // Lógica del panel desplegable de filtros
    const btnToggleFiltros = document.getElementById("btnToggleFiltros");
    const panelFiltros = document.getElementById("panelFiltros");

    if (btnToggleFiltros && panelFiltros) {
        btnToggleFiltros.addEventListener("click", (e) => {
            e.stopPropagation();
            panelFiltros.style.display = panelFiltros.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", (e) => {
            if (!panelFiltros.contains(e.target) && e.target !== btnToggleFiltros) {
                panelFiltros.style.display = "none";
            }
        });
    }

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const origen = document.getElementById("origen").value;
            const destino = document.getElementById("destino").value;
            const fecha = document.getElementById("fecha").value;
            const pasajeros = parseInt(document.getElementById("numPasajeros").value);

            if (origen === destino) {
                alert("El origen y el destino no pueden ser iguales.");
                return;
            }

            if (tbody) tbody.innerHTML = "";
            if (mensaje) mensaje.textContent = "Buscando viajes disponibles...";
            mapasInicializados = {};

            fetch(
                `buscador/buscar_viajes.php?origen=${encodeURIComponent(origen)}&destino=${encodeURIComponent(destino)}&fecha=${encodeURIComponent(fecha)}&pasajeros=${pasajeros}`,
            )
                .then((res) => res.json())
                .then((viajesEncontrados) => {
                    if (mensaje) mensaje.textContent = "";

                    if (viajesEncontrados && viajesEncontrados.error) {
                        if (contenedorHorarios) contenedorHorarios.style.display = "none";
                        if (mensaje) mensaje.textContent = viajesEncontrados.error;
                        return;
                    }

                    if (!viajesEncontrados || viajesEncontrados.length === 0) {
                        if (contenedorHorarios) contenedorHorarios.style.display = "none";
                        if (mensaje) mensaje.textContent = "No se encontraron viajes con los criterios solicitados.";
                        return;
                    }

                    listaViajesGlobal = viajesEncontrados;
                    poblarFiltroAgencias(viajesEncontrados);
                    renderizarTabla(viajesEncontrados);
                })
                .catch((error) => {
                    if (contenedorHorarios) contenedorHorarios.style.display = "none";
                    if (mensaje) mensaje.textContent = "Error de red al intentar conectar con el servidor.";
                    console.error("Fetch error:", error);
                });
        });
    }

    function poblarFiltroAgencias(viajes) {
        const selectAgencia = document.getElementById("filtroAgencia");
        if (!selectAgencia) return;
        selectAgencia.innerHTML = '<option value="">Todas las agencias</option>';
        
        const agenciasUnicas = [...new Set(viajes.map(v => v.agencia))];
        agenciasUnicas.forEach(agencia => {
            const opt = document.createElement("option");
            opt.value = agencia;
            opt.textContent = agencia;
            selectAgencia.appendChild(opt);
        });
    }

    window.renderizarTabla = function (viajes) {
        if (!tbody || !contenedorHorarios) return;
        tbody.innerHTML = "";
        contenedorHorarios.style.display = "block";
        const ahora = new Date();

        const agenciaFiltro = document.getElementById("filtroAgencia").value;
        const precioMaxFiltro = parseFloat(document.getElementById("filtroPrecio").value) || Infinity;
        const hitoFiltro = document.getElementById("filtroHito").value;
        const mostrarFinalizados = document.getElementById("chkMostrarFinalizados").checked;

        let contadorMostrados = 0;

        viajes.forEach((viaje, index) => {
            const estimado = calcularEstimado(viaje.hora_llegada, ahora);
            const esFinalizado = estimado === "Viaje finalizado";

            if (!mostrarFinalizados && esFinalizado) return;
            if (agenciaFiltro && viaje.agencia !== agenciaFiltro) return;
            if (viaje.precio > precioMaxFiltro) return;
            if (hitoFiltro === "directo" && !viaje.es_directo) return;

            contadorMostrados++;
            const fila = document.createElement("tr");
            fila.className = "text-center align-middle";

            const esFavorito = viaje.es_favorito ? true : false;
            const claseEstrella = esFavorito ? "fas fa-star text-primary" : "far fa-star text-primary";

            // Construcción segura de celdas mediante DOM nodes en lugar de innerHTML directo
            const tdHora = document.createElement("td");
            tdHora.textContent = viaje.hora;

            const tdLlegada = document.createElement("td");
            tdLlegada.textContent = viaje.hora_llegada;

            const tdEstimado = document.createElement("td");
            tdEstimado.textContent = estimado;

            const tdAsientos = document.createElement("td");
            tdAsientos.textContent = viaje.asientosLibres;

            const tdAgencia = document.createElement("td");
            tdAgencia.textContent = viaje.agencia;

            const tdPrecio = document.createElement("td");
            tdPrecio.textContent = `$${viaje.precio}`;

            // Botón Favorito
            const tdFav = document.createElement("td");
            const btnFav = document.createElement("button");
            btnFav.className = "btn btn-sm bg-transparent border-0 shadow-none btn-favorito p-0";
            btnFav.style.fontSize = "1.2rem";
            btnFav.setAttribute("data-id", viaje.id);
            const iFav = document.createElement("i");
            iFav.className = claseEstrella;
            btnFav.appendChild(iFav);
            tdFav.appendChild(btnFav);

            // Botón Mapa
            const tdMapaBtn = document.createElement("td");
            const btnMapa = document.createElement("button");
            btnMapa.className = "btn btn-sm btn-outline-primary border-0 btn-mapa fw-bold";
            btnMapa.style.transition = "transform 0.3s ease";
            btnMapa.textContent = "▼";
            btnMapa.setAttribute("data-index", index);
            btnMapa.setAttribute("data-ruta", viaje.ruta_key);
            tdMapaBtn.appendChild(btnMapa);

            // Botón Comprar
            const tdComprar = document.createElement("td");
            const btnComprar = document.createElement("button");
            btnComprar.className = "btn btn-sm btn-primary px-3 py-1 fw-bold btn-comprar";
            btnComprar.textContent = "Comprar";
            btnComprar.setAttribute("data-id", viaje.id);
            btnComprar.setAttribute("data-agencia", viaje.agencia);
            tdComprar.appendChild(btnComprar);

            fila.appendChild(tdHora);
            fila.appendChild(tdLlegada);
            fila.appendChild(tdEstimado);
            fila.appendChild(tdAsientos);
            fila.appendChild(tdAgencia);
            fila.appendChild(tdPrecio);
            fila.appendChild(tdFav);
            fila.appendChild(tdMapaBtn);
            fila.appendChild(tdComprar);

            tbody.appendChild(fila);

            // Fila contenedor del mapa
            const filaMapa = document.createElement("tr");
            filaMapa.className = "bg-body";
            const tdContenedorMapa = document.createElement("td");
            tdContenedorMapa.setAttribute("colspan", "9");
            tdContenedorMapa.className = "p-0 border-bottom border-primary";

            const divMapa = document.createElement("div");
            divMapa.id = `mapa-${index}`;
            divMapa.className = "rounded shadow-sm";
            divMapa.style.display = "none";
            divMapa.style.height = "350px";
            divMapa.style.width = "100%";

            tdContenedorMapa.appendChild(divMapa);
            filaMapa.appendChild(tdContenedorMapa);
            tbody.appendChild(filaMapa);
        });

        if (contadorMostrados === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-muted py-3">No hay viajes que coincidan con los filtros seleccionados.</td></tr>`;
        }
    }

    const btnAplicarFiltros = document.getElementById("btnAplicarFiltros");
    if (btnAplicarFiltros) {
        btnAplicarFiltros.addEventListener("click", () => {
            renderizarTabla(listaViajesGlobal);
            if (panelFiltros) panelFiltros.style.display = "none";
        });
    }

    const btnLimpiarFiltros = document.getElementById("btnLimpiarFiltros");
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener("click", () => {
            document.getElementById("filtroAgencia").value = "";
            document.getElementById("filtroPrecio").value = "";
            document.getElementById("filtroHito").value = "";
            document.getElementById("chkMostrarFinalizados").checked = false;
            renderizarTabla(listaViajesGlobal);
            if (panelFiltros) panelFiltros.style.display = "none";
        });
    }
});