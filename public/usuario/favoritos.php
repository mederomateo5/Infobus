<?php
require_once '../../private/config/config.php';
session_start();

// Verificar sesión y permisos de usuario
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../../private/config/database.php';

$user_id = $_SESSION['user_id'];
$favoritos = [];
$companias = [];

try {
    // 1. Obtener los viajes favoritos del usuario
    $query = "SELECT 
                v.id AS id,
                po.nombre AS origen,
                pd.nombre AS destino,
                v.asientos_disponibles AS asientosLibres,
                c.nombre AS agencia,
                v.precio,
                o.ruta_key,
                v.hora,
                v.hora_llegada,
                v.es_directo
            FROM usuario_viaje_favorito uf
            JOIN viajes v ON uf.viaje_id = v.id
            JOIN paradas po ON v.origen_id = po.id
            JOIN paradas pd ON v.destino_id = pd.id
            JOIN omnibus o ON v.omnibus_id = o.id
            JOIN compania c ON o.compania_id = c.id
            WHERE uf.user_id = :user_id
            ORDER BY v.hora ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Obtener todas las compañías ordenando primero las favoritas y luego alfabéticamente
    $queryComp = "SELECT 
                    c.id,
                    c.nombre,
                    c.email,
                    c.telefono,
                    CASE WHEN ucf.user_id IS NOT NULL THEN 1 ELSE 0 END AS es_favorito
                FROM compania c
                LEFT JOIN usuario_compania_favorita ucf ON c.id = ucf.compania_id AND ucf.user_id = :user_id
                ORDER BY es_favorito DESC, c.nombre ASC";

    $stmtComp = $pdo->prepare($queryComp);
    $stmtComp->execute([':user_id' => $user_id]);
    $companias = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_db = "Error al cargar los datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Favoritos y Compañías</title>
    <!-- CSS de Leaflet (Mapas) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php include '../../private/includes/head.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/panel_admin.css">
</head>
<body>
    <?php include '../../private/includes/header.php'; ?>

    <!-- Contenedor Flexbox Responsivo de Alto Completo -->
    <div class="d-flex flex-column flex-md-row min-vh-100">

        <!-- Sidebar Lateral -->
        <?php include '../../private/includes/sidebar.php'; ?>

        <!-- Área Principal de Contenido -->
        <main class="flex-grow-1 p-4 overflow-auto bg-body">

            <div class="container-fluid" style="max-width: 1100px;">

                <!-- Encabezado de Bienvenida -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 text-body fw-bold m-0">Mis Favoritos</h1>
                    <span class="badge bg-primary fs-6 px-3 py-2">
                        <i class="fa-solid fa-user me-1"></i> Usuario
                    </span>
                </div>

                <?php if (isset($error_db)) { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_db) ?></div>
                <?php } ?>

                <!-- Tarjeta contenedora de la tabla de Viajes Favoritos -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3 text-primary fw-bold">Tus rutas guardadas</h2>

                        <?php if (empty($favoritos)) { ?>
                            <p class="text-muted text-center py-4">Aún no tienes ningún viaje marcado como favorito.</p>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center">
                                    <thead>
                                        <tr>
                                            <th>Origen</th>
                                            <th>Destino</th>
                                            <th>Hora</th>
                                            <th>Asientos Disp.</th>
                                            <th>Agencia</th>
                                            <th>Precio</th>
                                            <th>Favorito</th>
                                            <th>Ruta</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaFavoritos">
                                        <?php foreach ($favoritos as $index => $viaje) { ?>
                                            <tr class="text-center align-middle">
                                                <td><?= htmlspecialchars($viaje['origen']) ?></td>
                                                <td><?= htmlspecialchars($viaje['destino']) ?></td>
                                                <td><?= htmlspecialchars($viaje['hora']) ?></td>
                                                <td><?= (int)$viaje['asientosLibres'] ?></td>
                                                <td><?= htmlspecialchars($viaje['agencia']) ?></td>
                                                <td>$<?= number_format($viaje['precio'], 2) ?></td>
                                                <td>
                                                    <button class="btn btn-sm bg-transparent border-0 shadow-none btn-favorito p-0" data-id="<?= (int)$viaje['id'] ?>" style="font-size: 1.2rem;">
                                                        <i class="fas fa-star text-primary"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn btn-sm btn-outline-primary border-0 btn-mapa fw-bold"
                                                        data-index="<?= $index ?>"
                                                        data-ruta="<?= htmlspecialchars(empty($viaje['ruta_key']) ? 'urbano' : $viaje['ruta_key']) ?>"
                                                        style="transition: transform 0.3s ease;">
                                                        ▼
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Fila para el mapa desplegable -->
                                            <tr class="bg-body">
                                                <td colspan="8" class="p-0 border-bottom border-primary">
                                                    <div
                                                        id="mapa-<?= $index ?>"
                                                        class="rounded shadow-sm"
                                                        style="display: none; height: 350px; width: 100%;">
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Tarjeta contenedora de la tabla de Compañías -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3 text-primary fw-bold">Compañías de Transporte</h2>

                        <?php if (empty($companias)) { ?>
                            <p class="text-muted text-center py-4">No hay compañías registradas en el sistema.</p>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Correo electrónico</th>
                                            <th>Teléfono</th>
                                            <th>Favorito</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaCompanias">
                                        <?php foreach ($companias as $comp) { ?>
                                            <tr class="text-center align-middle">
                                                <td><?= htmlspecialchars($comp['nombre']) ?></td>
                                                <td><?= htmlspecialchars($comp['email']) ?></td>
                                                <td><?= htmlspecialchars($comp['telefono']) ?></td>
                                                <td>
                                                    <button class="btn btn-sm bg-transparent border-0 shadow-none btn-compania-favorito p-0" data-id="<?= (int)$comp['id'] ?>" style="font-size: 1.2rem;">
                                                        <?php if ($comp['es_favorito']) { ?>
                                                            <i class="fas fa-star text-primary"></i>
                                                        <?php } else { ?>
                                                            <i class="far fa-star text-secondary"></i>
                                                        <?php } ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>

        </main>

    </div>

    <!-- Scripts de Leaflet y lógica de mapas/favoritos -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let paradasData = [];
        let mapasInicializados = {};

        // Cargar paradas para los mapas al igual que en el index
        fetch('../api/obtener_paradas.php')
            .then(res => res.json())
            .then(data => { paradasData = data; })
            .catch(err => console.error("Error cargando paradas:", err));

        function crearPinColor(colorHex) {
            return L.divIcon({
                className: 'custom-pin',
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
                popupAnchor: [0, -30]
            });
        }
        const pinOrigenRojo = crearPinColor("#dc3545");

        // Manejo de Favoritos de Viajes
        const tbody = document.getElementById("tablaFavoritos");
        if (tbody) {
            tbody.addEventListener("click", function (e) {
                const btnFav = e.target.closest(".btn-favorito");
                if (btnFav) {
                    const viajeId = btnFav.getAttribute("data-id");
                    const fila = btnFav.closest("tr");
                    const filaSiguiente = fila.nextElementSibling;

                    fetch('../usuario/marcar_favorito.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ viaje_id: viajeId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && !data.favorito) {
                            fila.remove();
                            if (filaSiguiente) filaSiguiente.remove();
                        } else if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(err => console.error("Error al actualizar favoritos:", err));
                    return;
                }

                // Manejo de mapas desplegables
                const btn = e.target.closest(".btn-mapa");
                if (!btn) return;

                const index = btn.getAttribute("data-index");
                const rutaKey = btn.getAttribute("data-ruta");
                const filaPadre = btn.closest("tr");
                const origenViaje = filaPadre.cells[0].textContent;
                const destinoViaje = filaPadre.cells[1].textContent;
                const contenedorMapa = document.getElementById(`mapa-${index}`);

                btn.classList.toggle("abierto");
                btn.style.transform = btn.classList.contains("abierto") ? "rotate(180deg)" : "rotate(0deg)";

                if (contenedorMapa.style.display === "block") {
                    contenedorMapa.style.display = "none";
                } else {
                    contenedorMapa.style.display = "block";
                    if (!mapasInicializados[index]) {
                        inicializarMapaTabla(index, rutaKey, origenViaje, destinoViaje);
                    } else {
                        setTimeout(() => { mapasInicializados[index].invalidateSize(); }, 100);
                    }
                }
            });
        }

        // Manejo de Favoritos de Compañías
        const tbodyCompanias = document.getElementById("tablaCompanias");
        if (tbodyCompanias) {
            tbodyCompanias.addEventListener("click", function (e) {
                const btnCompFav = e.target.closest(".btn-compania-favorito");
                if (btnCompFav) {
                    const companiaId = btnCompFav.getAttribute("data-id");
                    const icono = btnCompFav.querySelector("i");

                    fetch('../usuario/marcar_compania_favorita.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ compania_id: companiaId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.favorito) {
                                icono.className = "fas fa-star text-primary";
                            } else {
                                icono.className = "far fa-star text-secondary";
                            }
                        } else if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(err => console.error("Error al actualizar compañía favorita:", err));
                }
            });
        }

        function inicializarMapaTabla(index, rutaKey, nombreOrigen, nombreDestino) {
            const idContenedor = `mapa-${index}`;
            const map = L.map(idContenedor).setView([-34.42, -57.44], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let paradaOrigenObj = paradasData.find(p => p.nombre.toLowerCase() === nombreOrigen.toLowerCase());
            let paradaDestinoObj = paradasData.find(p => p.nombre.toLowerCase() === nombreDestino.toLowerCase());

            if (paradaOrigenObj && paradaDestinoObj) {
                const lng1 = parseFloat(paradaOrigenObj.lng);
                const lat1 = parseFloat(paradaOrigenObj.lat);
                const lng2 = parseFloat(paradaDestinoObj.lng);
                const lat2 = parseFloat(paradaDestinoObj.lat);

                const urlOSRM = `https://router.project-osrm.org/route/v1/driving/${lng1},${lat1};${lng2},${lat2}?overview=full&geometries=geojson`;

                fetch(urlOSRM)
                    .then(res => res.json())
                    .then(data => {
                        if (data.routes && data.routes.length > 0) {
                            const polyline = L.geoJSON(data.routes[0].geometry, {
                                style: { color: '#0056b3', weight: 5, opacity: 0.8, dashArray: '8, 8' }
                            }).addTo(map);
                            map.fitBounds(polyline.getBounds());
                        } else {
                            dibujarLineaRectaFallback(map, lat1, lng1, lat2, lng2);
                        }
                    })
                    .catch(() => dibujarLineaRectaFallback(map, lat1, lng1, lat2, lng2));
            }

            if (paradaOrigenObj) {
                L.marker([paradaOrigenObj.lat, paradaOrigenObj.lng], { icon: pinOrigenRojo }).addTo(map).bindPopup(`<b>${paradaOrigenObj.nombre}</b><br>Rol: ORIGEN`).openPopup();
            }
            if (paradaDestinoObj) {
                L.marker([paradaDestinoObj.lat, paradaDestinoObj.lng], { icon: pinOrigenRojo }).addTo(map).bindPopup(`<b>${paradaDestinoObj.nombre}</b><br>Rol: DESTINO`);
            }

            setTimeout(() => { map.invalidateSize(); }, 100);
            mapasInicializados[index] = map;
        }

        function dibujarLineaRectaFallback(map, lat1, lng1, lat2, lng2) {
            const polyline = L.polyline([[lat1, lng1], [lat2, lng2]], {
                color: '#0056b3', weight: 5, dashArray: '8, 8'
            }).addTo(map);
            map.fitBounds(polyline.getBounds());
        }
    </script>
</body>
</html>