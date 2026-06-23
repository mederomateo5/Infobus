<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="css/marcelo_index_style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="padre">
    <h1>Encuentra tu próximo viaje</h1>

    <div class="hijo">
        <form class="form" id="formBusqueda">

            <div class="options">
                <label for="origen">Origen</label>
                <select name="origen" id="origen" required>
                    <option value="" disabled selected hidden>Selecciona un origen</option>
                    <option value="Montevideo">Montevideo</option>
                    <option value="Paysandú">Paysandú</option>
                    <option value="Salto">Salto</option>
                </select>
            </div>

            <div class="options">
                <label for="destino">Destino</label>
                <select name="destino" id="destino" required>
                    <option value="" disabled selected hidden>Selecciona un destino</option>
                    <option value="Montevideo">Montevideo</option>
                    <option value="Paysandú">Paysandú</option>
                    <option value="Punta del Este">Punta del Este</option>
                    <option value="Salto">Salto</option>
                </select>
            </div>

            <div class="options">
                <label for="fecha">Fecha</label>
                <input type="date" name="fecha" id="fecha" required>
            </div>

            <div class="options">
                <label for="numPasajeros">Cantidad de pasajeros</label>
                <input type="number" name="numPasajeros" id="numPasajeros" min="1" required>
            </div>

            <input type="submit" value="Buscar">

        </form>
    </div>

    <p id="mensaje"></p>

    <div class="horarios" id="contenedorHorarios" style="display:none;">

        <h2>Viajes encontrados</h2>

        <table>
            <thead>
                <tr>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Fecha</th>
                    <th>Asientos</th>
                    <th>Agencia</th>
                    <th>Precio</th>
                </tr>
            </thead>

            <tbody id="tablaHorarios"></tbody>
        </table>

    </div>
</div>

<div class="destinos">
    <h1>Destinos Populares</h1>

    <div class="destinos_img">
        <img src="img/placeholder.png" alt="">
        <img src="img/placeholder.png" alt="">
        <img src="img/placeholder.png" alt="">
        <img src="img/placeholder.png" alt="">
    </div>
</div>

<script src="js/tablahorarios.js"></script>

</body>
</html>