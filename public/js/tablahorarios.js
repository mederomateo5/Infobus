const horarios = [
    {
        origen: "Montevideo",
        destino: "Punta del Este",
        fecha: "2026-07-24",
        asientosLibres: 20,
        agencia: "COPSA",
        precio: 450
    },
    {
        origen: "Montevideo",
        destino: "Paysandú",
        fecha: "2026-07-16",
        asientosLibres: 15,
        agencia: "Turil",
        precio: 850
    },
    {
        origen: "Montevideo",
        destino: "Paysandú",
        fecha: "2026-07-16",
        asientosLibres: 15,
        agencia: "Turil",
        precio: 853
    },
    {
        origen: "Montevideo",
        destino: "Paysandú",
        fecha: "2026-07-16",
        asientosLibres: 15,
        agencia: "Turil",
        precio: 852
    },
    {
        origen: "Salto",
        destino: "Montevideo",
        fecha: "2026-07-17",
        asientosLibres: 10,
        agencia: "CUT",
        precio: 900
    },
    {
        origen: "Paysandú",
        destino: "Montevideo",
        fecha: "2026-07-20",
        asientosLibres: 25,
        agencia: "Turil",
        precio: 850
    }
];

const form = document.getElementById("formBusqueda");
const tbody = document.getElementById("tablaHorarios");
const mensaje = document.getElementById("mensaje");
const contenedorHorarios = document.getElementById("contenedorHorarios");

form.addEventListener("submit", function (e) {

    e.preventDefault();

    const origen = document.getElementById("origen").value;
    const destino = document.getElementById("destino").value;
    const fecha = document.getElementById("fecha").value;
    const pasajeros = parseInt(document.getElementById("numPasajeros").value);

    tbody.innerHTML = "";
    mensaje.textContent = "";

    const viajesEncontrados = horarios.filter(viaje =>
        viaje.origen === origen &&
        viaje.destino === destino &&
        viaje.fecha === fecha &&
        viaje.asientosLibres >= pasajeros
    );

    if (viajesEncontrados.length === 0) {

        contenedorHorarios.style.display = "none";

        mensaje.textContent =
            "No hay viajes con esas especificaciones.";

        return;
    }

    contenedorHorarios.style.display = "block";

    viajesEncontrados.forEach(viaje => {

        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${viaje.origen}</td>
            <td>${viaje.destino}</td>
            <td>${viaje.fecha}</td>
            <td>${viaje.asientosLibres}</td>
            <td>${viaje.agencia}</td>
            <td>$${viaje.precio}</td>
        `;

        tbody.appendChild(fila);
    });
});