// ==========================================
// server.js
// ==========================================
const express = require("express");
const { getHorariosPorLinea } = require("./urbanos");

const app = express();

app.get("/api/horarios", (req, res) => {
    res.json(getHorariosPorLinea());
});

app.get("/api/horarios/:linea", (req, res) => {
    const { linea } = req.params;
    const horarios = getHorariosPorLinea();

    if (!horarios[linea]) {
        return res.status(404).json({ error: `Línea "${linea}" no encontrada` });
    }

    res.json(horarios[linea]);
});

app.listen(3000, () => console.log("Servidor corriendo en http://localhost:3000"));