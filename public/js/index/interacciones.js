//Favoritos, compras, mapas y sincronización de selects

document.addEventListener("DOMContentLoaded", function () {
    const tbody = document.getElementById("tablaHorarios");

    // --- 1. Manejo Global de Compras (Tabla y Top 3) ---
    document.addEventListener("click", function (e) {
        const btnComprar = e.target.closest(".btn-comprar");
        if (btnComprar) {
            e.preventDefault();
            const viajeId = btnComprar.getAttribute("data-id");
            const agencia = (btnComprar.getAttribute("data-agencia") || "").toUpperCase();

            let urlDestino = "";
            if (agencia.includes("COT")) {
                urlDestino = "https://www.cot.com.uy/inicio";
            } else if (agencia.includes("TAB")) {
                urlDestino = "https://tab.com.uy/#comprar_pasaje";
            } else if (agencia.includes("TURIL")) {
                urlDestino = "https://www.turil.com.uy/index.php";
            }

            if (!urlDestino) {
                alert("Sitio de compra no disponible para esta agencia.");
                return;
            }

            const nuevaPestana = window.open("about:blank", "_blank");
            if (!nuevaPestana) {
                alert("Permití las ventanas emergentes para continuar.");
                return;
            }

            const datos = new URLSearchParams();
            datos.append("viaje_id", viajeId);

            fetch("registrar_visita.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: datos,
                credentials: "same-origin"
            })
            .then(async respuesta => {
                if (!respuesta.ok) throw new Error("No se pudo registrar la visita.");
                return respuesta.json();
            })
            .then(resultado => {
                if (!resultado.ok) throw new Error("No se pudo registrar la visita.");
                nuevaPestana.location.href = urlDestino;
            })
            .catch(error => {
                console.error(error);
                nuevaPestana.close();
                alert("No pudimos registrar la visita. Intentá nuevamente.");
            });

            return;
        }
    });

    // --- 2. Favoritos y Mapas en la tabla ---
    if (tbody) {
        tbody.addEventListener("click", function (e) {
            const btnFav = e.target.closest(".btn-favorito");
            if (btnFav) {
                const viajeId = btnFav.getAttribute("data-id");
                const icono = btnFav.querySelector("i");

                fetch("usuario/marcar_favorito.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ viaje_id: viajeId }),
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        if (data.success) {
                            if (data.favorito) {
                                icono.classList.remove("far");
                                icono.classList.add("fas");
                            } else {
                                icono.classList.remove("fas");
                                icono.classList.add("far");
                            }
                        } else {
                            alert("No se pudo actualizar el favorito: " + data.mensaje);
                        }
                    })
                    .catch((err) => console.error("Error en favoritos:", err));
                return;
            }

            const btnMapa = e.target.closest(".btn-mapa");
            if (!btnMapa) return;

            const index = btnMapa.getAttribute("data-index");
            const rutaKey = btnMapa.getAttribute("data-ruta");
            const origenViaje = document.getElementById("origen").value;
            const destinoViaje = document.getElementById("destino").value;
            const contenedorMapa = document.getElementById(`mapa-${index}`);

            btnMapa.classList.toggle("abierto");
            btnMapa.style.transform = btnMapa.classList.contains("abierto") ? "rotate(180deg)" : "rotate(0deg)";

            if (contenedorMapa.style.display === "block") {
                contenedorMapa.style.display = "none";
            } else {
                contenedorMapa.style.display = "block";

                if (!mapasInicializados[index]) {
                    inicializarMapaTabla(index, rutaKey, origenViaje, destinoViaje);
                } else {
                    setTimeout(() => {
                        mapasInicializados[index].invalidateSize();
                    }, 100);
                }
            }
        });
    }

    // --- 3. Sincronización de Selects (Origen y Destino) ---
    const selectOrigen = document.getElementById("origen");
    const selectDestino = document.getElementById("destino");

    function actualizarSelects() {
        if (!selectOrigen || !selectDestino) return;
        const origenSeleccionado = selectOrigen.value;
        const destinoSeleccionado = selectDestino.value;

        Array.from(selectOrigen.options).forEach((option) => {
            option.disabled = false;
        });
        Array.from(selectDestino.options).forEach((option) => {
            option.disabled = false;
        });

        if (destinoSeleccionado) {
            const opcionEnOrigen = Array.from(selectOrigen.options).find(
                (opt) => opt.value === destinoSeleccionado,
            );
            if (opcionEnOrigen) opcionEnOrigen.disabled = true;
            if (origenSeleccionado === destinoSeleccionado) selectOrigen.value = "";
        }

        if (origenSeleccionado) {
            const opcionEnDestino = Array.from(selectDestino.options).find(
                (opt) => opt.value === origenSeleccionado,
            );
            if (opcionEnDestino) opcionEnDestino.disabled = true;
            if (destinoSeleccionado === origenSeleccionado) selectDestino.value = "";
        }
    }

    if (selectOrigen) selectOrigen.addEventListener("change", actualizarSelects);
    if (selectDestino) selectDestino.addEventListener("change", actualizarSelects);
});