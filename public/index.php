<?php
require_once '../config/config.php';
// Incluir la conexión a la base de datos
require_once '../config/database.php';

// Obtener orígenes disponibles en la base de datos (excluyendo el ómnibus urbano ID 4)
try {
    $stmt_origenes = $pdo->query("
        SELECT DISTINCT p.nombre 
        FROM viajes v 
        JOIN paradas p ON v.origen_id = p.id 
        WHERE v.ya_salio = 0 AND v.omnibus_id != 4
        ORDER BY p.nombre ASC
    ");
    $origenes = $stmt_origenes->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $origenes = [];
}

// Obtener destinos disponibles en la base de datos (excluyendo el ómnibus urbano ID 4)
try {
    $stmt_destinos = $pdo->query("
        SELECT DISTINCT p.nombre 
        FROM viajes v 
        JOIN paradas p ON v.destino_id = p.id 
        WHERE v.ya_salio = 0 AND v.omnibus_id != 4
        ORDER BY p.nombre ASC
    ");
    $destinos = $stmt_destinos->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $destinos = [];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio - Interurbanos</title>

    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php include '../includes/head.php'; ?>
</head>

<style>
    body {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            url("../public/img/fondo.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>

<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container py-4" style="max-width: 1100px;">

        <!-- Botón para ir a la página de urbanos -->
        <div class="d-flex justify-content-end mb-3">
            <a href="urbanos.php" class="btn btn-primary fw-semibold">Ir a Ómnibus Urbanos &rarr;</a>
        </div>

        <!-- Tarjeta del Formulario de Búsqueda -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h1 class="h3 mb-4 text-dark fw-bold text-center">Encuentra tu próximo viaje Interurbano</h1>

                <form id="formBusqueda">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="origen" class="form-label fw-semibold">Origen</label>
                            <select name="origen" id="origen" class="form-select" required>
                                <option value="" disabled selected hidden>Selecciona un origen</option>
                                <?php foreach ($origenes as $ori) { ?>
                                    <option value="<?= htmlspecialchars($ori) ?>"><?= htmlspecialchars($ori) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="destino" class="form-label fw-semibold">Destino</label>
                            <select name="destino" id="destino" class="form-select" required>
                                <option value="" disabled selected hidden>Selecciona un destino</option>
                                <?php foreach ($destinos as $des) { ?>
                                    <option value="<?= htmlspecialchars($des) ?>"><?= htmlspecialchars($des) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="fecha" class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="numPasajeros" class="form-label fw-semibold">Cantidad de pasajeros</label>
                            <input type="number" name="numPasajeros" id="numPasajeros" class="form-control" min="1" required>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mensaje de error o información -->
        <p id="mensaje" class="text-danger text-center fw-bold"></p>

        <!-- Contenedor para mostrar los resultados de la búsqueda -->
        <div class="card shadow-sm border-0 mb-4" id="contenedorHorarios" style="display:none;">
            <div class="card-body p-4">
                <h2 class="h4 mb-3 text-primary fw-bold">Viajes encontrados</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Fecha</th>
                                <th>Asientos Disp.</th>
                                <th>Agencia</th>
                                <th>Precio</th>
                                <th>Marcar como Favorito</th>
                                <th>Ruta</th>
                            </tr>
                        <tbody id="tablaHorarios"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Contenedor de destinos populares -->
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-1">Compra tus pasajes aquí</h2>
                <p class="text-muted small mb-4">(Da click encima del logo de la compañía en la que deseas viajar)</p>
                <div class="d-flex justify-content-center flex-wrap gap-4 align-items-center">
                    <a href="https://tab.com.uy/#comprar_pasaje" target="_blank" rel="noopener noreferrer">
                        <img src="<?= BASE_URL ?>img/tab.png" alt="TAB" class="img-fluid rounded shadow-sm" style="width: 180px; height: auto;">
                    </a>

                    <a href="https://www.cot.com.uy/inicio" target="_blank" rel="noopener noreferrer">
                        <img src="<?= BASE_URL ?>img/cot.png" alt="COT" class="img-fluid rounded shadow-sm" style="width: 180px; height: auto;">
                    </a>

                    <a href="https://www.turil.com.uy/index.php" target="_blank" rel="noopener noreferrer">
                        <img src="<?= BASE_URL ?>img/turil.svg" alt="TURIL" class="img-fluid rounded shadow-sm" style="width: 180px; height: auto;">
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= BASE_URL ?>js/scripts.js"></script>
</body>

</html>