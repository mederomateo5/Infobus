<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
        <link rel="stylesheet" href="css/marcelo_index_style.css">
</head>
<body>

    <div class=padre>
        <h1 >Encuentra tu próximo viaje</h1>
        <div class=hijo>
            <form class=form>
                <div class=options>
                    <label for="origen">Origen</label>
                    <select name="origen" id="origen" >
                        <option value="" disabled selected hidden>Selecciona un origen</option>
                        <option value="">Opción 1</option>
                        <option value="">Opción 2</option>
                        <option value="">Opción 3</option>
                    </select>
                </div>
                <div class=options>
                    <label for="destino">Destino</label>
                    <select name="destino" id="destino" >
                        <option value="" disabled selected hidden>Selecciona un destino</option>
                        <option value="">Opción 1</option>
                        <option value="">Opción 2</option>
                        <option value="">Opción 3</option>
                    </select>
                </div class=options>
                <div>
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha">
                </div class=options>
                <div>
                    <label for="numPasajeros">Cantidad de pasajeros</label>
                    <input type="text" name="numPasajeros" id="numPasajeros" min=1>
                </div>
            </form>
        </div>
    </div>

    <div class=destinos>
        <h1>Destinos Populares</h1>
        <div class=destinos_img>
            <img src="img/placeholder.png" alt="">
            <img src="img/placeholder.png" alt="">
            <img src="img/placeholder.png" alt="">
            <img src="img/placeholder.png" alt="">
        </div>
    </div>
</body>
</html>