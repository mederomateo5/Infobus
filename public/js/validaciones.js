function validarViaje(form) {
  const origen = form.origen.value.trim().toLowerCase();
  const destino = form.destino.value.trim().toLowerCase();
  const fecha = form.fecha.value;
  const horaSalida = form.hora.value;
  const horaLlegada = form.hora_llegada.value;

  if (origen === destino) {
    alert("El origen y el destino no pueden ser el mismo.");
    return false;
  }

  // Fecha y hora local
  const ahora = new Date();
  const anio = ahora.getFullYear();
  const mes = String(ahora.getMonth() + 1).padStart(2, "0");
  const dia = String(ahora.getDate()).padStart(2, "0");
  const hoy = `${anio}-${mes}-${dia}`;

  const horasActuales = String(ahora.getHours()).padStart(2, "0");
  const minutosActuales = String(ahora.getMinutes()).padStart(2, "0");
  const horaActualStr = `${horasActuales}:${minutosActuales}`;

  if (fecha < hoy) {
    alert("No podés seleccionar una fecha del pasado.");
    return false;
  }

  if (fecha === hoy && horaSalida < horaActualStr) {
    alert("La hora de salida no puede ser anterior a la hora actual.");
    return false;
  }

  if (horaSalida === horaLlegada) {
    alert("La hora de salida y la de llegada no pueden ser iguales.");
    return false;
  }

  // Validar salidas mayores a llegadas solo a partir de las 23:00 hs
  const [hSalida, mSalida] = horaSalida.split(":").map(Number);
  const [hLlegada, mLlegada] = horaLlegada.split(":").map(Number);

  const minSalida = hSalida * 60 + mSalida;
  const minLlegada = hLlegada * 60 + mLlegada;

  if (minSalida > minLlegada) {
    if (hSalida < 23) {
      alert("La hora de salida no puede ser posterior a la hora de llegada");
      return false;
    }
  }

  return true;
}
