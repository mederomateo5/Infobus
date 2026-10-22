<?php
require_once '../private/config/config.php';
require_once '../private/config/database.php';
require_once __DIR__ . '/../private/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensajeExito = '';$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $lat = trim($_POST['lat'] ?? '');
    $lng = trim($_POST['lng'] ?? '');

    if (empty($nombre) || empty($lat) || empty($lng)) {$mensajeError = 'Por favor, ingresá un nombre y hacé clic en el mapa para marcar la ubicación de la parada.';
    } else {
        // Asegurar 7 decimales exactos
        $latFormateado = number_format((float)$lat, 7, '.', '');
        $lngFormateado = number_format((float)$lng, 7, '.', '');

        // Envío de correo usando la configuración SMTP existente (ej. Gmail)
        $cfg = require __DIR__ . '/../private/config/mail.php';$mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';$mail->SMTPAuth   = true;
            $mail->Username   = $cfg['usuario'];$mail->Password   = $cfg['clave'];$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($cfg['usuario'],$cfg['nombre']);
            $mail->addAddress($cfg['usuario']); // Envía al correo del administrador del sistema
            $mail->Subject = 'Nueva sugerencia de parada - InfoBus';$mail->Body    = "Se ha recibido una nueva sugerencia de parada:\n\n"
                           . "Nombre de la parada: $nombre\n"
                           . "Latitud: $latFormateado\n"
                           . "Longitud: $lngFormateado\n";
            $mail->send();$mensajeExito = '¡Parada sugerida enviada con éxito! Muchas gracias por colaborar.';
        } catch (Exception $e) {
            error_log('Error al enviar correo de sugerencia: ' . $mail->ErrorInfo);$mensajeError = 'No pudimos enviar el correo en este momento. Probá de nuevo en unos minutos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerir Parada</title>
    <?php include '../private/includes/head.php'; ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>

<style>
    body {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            url("assets/img/fondo.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>

<body class="bg-light">
    <?php include '../private/includes/header.php'; ?>

    <div class="container py-4" style="max-width: 1100px;">
        <!-- Botón para regresar -->
        <div class="d-flex justify-content-start mb-3">
            <a href="paradas.php" class="btn btn-primary fw-semibold">&larr; Volver a Paradas</a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h1 class="h3 mb-3 text-dark fw-bold text-center">Sugerir Nueva Parada</h1>
                <p class="text-secondary text-center mb-4">Hacé clic en el mapa para ubicar el punto exacto de la parada que querés proponer.</p>

                <?php if ($mensajeExito): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensajeExito) ?></div>
                <?php endif; ?>
                <?php if ($mensajeError): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Mapa limitado a Uruguay -->
                    <div id="mapaSugerencia" class="rounded shadow-sm mb-4" style="width: 100%; height: 450px; z-index: 1;"></div>

                    <!-- Campos ocultos para latitud y longitud -->
                    <input type="hidden" id="lat" name="lat" value="<?= htmlspecialchars($_POST['lat'] ?? '') ?>">
                    <input type="hidden" id="lng" name="lng" value="<?= htmlspecialchars($_POST['lng'] ?? '') ?>">

                    <!-- Nombre de la parada -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre de la Parada</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Terminal Juan Lacaze o Ruta 54" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Enviar Sugerencia</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Límites geográficos de Uruguay idénticos a los del mapa general
        const uruguayBounds = L.latLngBounds(
            [-35.85, -58.5],
            [-30.05, -53.0]
        );

        const mapa = L.map("mapaSugerencia", {
            center: [-34.4188, -57.4523],
            zoom: 13,
            minZoom: 7,
            maxBounds: uruguayBounds,
            maxBoundsViscosity: 1.0
        });

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(mapa);

        let marcadorSugerencia = null;
        const inputLat = document.getElementById("lat");
        const inputLng = document.getElementById("lng");

        // Mantener marcador si hubo error de validación en POST
        if (inputLat.value && inputLng.value) {
            const latIni = parseFloat(inputLat.value);
            const lngIni = parseFloat(inputLng.value);
            marcadorSugerencia = L.marker([latIni, lngIni]).addTo(mapa);
            mapa.setView([latIni, lngIni], 16);
        }

        // Evento de clic en el mapa para capturar coordenadas con 7 decimales
        mapa.on("click", function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            inputLat.value = lat.toFixed(7);
            inputLng.value = lng.toFixed(7);

            if (marcadorSugerencia) {
                marcadorSugerencia.setLatLng([lat, lng]);
            } else {
                marcadorSugerencia = L.marker([lat, lng]).addTo(mapa);
            }

            marcadorSugerencia.bindPopup(`<strong>Ubicación seleccionada</strong><br>Lat: ${inputLat.value}<br>Lng: ${inputLng.value}`).openPopup();
        });
    </script>
</body>
</html>