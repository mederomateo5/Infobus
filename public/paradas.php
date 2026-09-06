<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Ver paradas</title>
    <?php include '../includes/head.php'; ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"> <!-- Importe de leaflet (mapa) -->
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <main class="container py-5">

        <h1>Ver paradas</h1>

        <button id="btnUbicacion" class="btn btn-primary">Encontrar mi ubicación</button>

        <div id="mapa" class="rounded shadow mt-3" style="height: 500px"></div>

        <h2 class="mt-4">Paradas cercanas</h2>

        <div id="listaParadas"></div>

    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script> <!-- Importe de leaflet (mapa) -->
    <script src="<?= BASE_URL ?>js/paradas.js"></script>
</body>

</html>