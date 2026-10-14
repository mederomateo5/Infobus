// JavaScript para Funciones Auxiliares y de de cálculo

// --- FUNCIÓN PARA CREAR PINES DE COLORES CON CSS ---
function crearPinColor(colorHex) {
    return L.divIcon({
        className: "custom-pin",
        html: `<div style="
            background-color: ${colorHex};
            width: 20px;
            height: 20px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            left: 50%;
            top: 50%;
            margin: -10px 0 0 -10px;
            position: absolute;
            border: 2px solid #FFFFFF;
            box-shadow: 0 3px 6px rgba(0,0,0,0.4);
        "></div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30],
    });
}

const pinOrigenRojo = crearPinColor("#dc3545");

// --- Helpers de tiempo y estimación ---
function horaAMinutos(hora) {
    const [h, m] = hora.split(":").map(Number);
    return h * 60 + m;
}

function minutosAHora(minutos) {
    const h = Math.floor(minutos / 60) % 24;
    const m = minutos % 60;
    return `${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`;
}

function calcularEstimado(llegada, ahora = new Date()) {
    const minutosActuales = ahora.getHours() * 60 + ahora.getMinutes();
    const minutosLlegada = horaAMinutos(llegada);
    const diferencia = minutosLlegada - minutosActuales;

    if (diferencia < -5) return "Viaje finalizado";
    if (diferencia >= -5 && diferencia <= 0) return "Llegando ahora";

    if (diferencia > 60) {
        const horas = Math.floor(diferencia / 60);
        const mins = diferencia % 60;
        return mins > 0 ? `${horas}h ${mins}min` : `${horas}h`;
    }

    return `${diferencia} min`;
}