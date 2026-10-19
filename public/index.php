<?php
require_once '../private/config/config.php';
// Incluir la conexión a la base de datos
require_once '../private/config/database.php';

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

// Obtener el Top 3 de viajes más visitados / concurridos
$top_viajes = [];
try {
    $stmt_top = $pdo->query("
        SELECT 
            v.id, 
            v.hora, 
            v.hora_llegada, 
            v.precio, 
            c.nombre AS agencia, 
            po.nombre AS origen_nombre,
            pd.nombre AS destino_nombre,
            COUNT(vv.id) AS total_visitas
        FROM viaje_visita vv
        JOIN viajes v ON vv.viaje_id = v.id
        JOIN omnibus o ON v.omnibus_id = o.id
        JOIN compania c ON o.compania_id = c.id
        JOIN paradas po ON v.origen_id = po.id
        JOIN paradas pd ON v.destino_id = pd.id
        GROUP BY v.id
        ORDER BY total_visitas DESC
        LIMIT 3
    ");
    
    if ($stmt_top) {
        $top_viajes = $stmt_top->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger m-3'><strong>Error SQL en Top 3:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    $top_viajes = [];
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
    <?php include '../private/includes/head.php'; ?>
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
                            <input type="date" name="fecha" id="fecha" class="form-control" min="<?= date('Y-m-d'); ?>" required>
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
        <div class="card shadow-sm border-0 mb-4 position-relative" id="contenedorHorarios" style="display:none;">
            
            <!-- Icono y panel de filtros en la esquina superior derecha -->
            <div class="position-absolute top-0 end-0 p-3" style="z-index: 10;">
                <button class="btn btn-light border shadow-sm rounded-circle" id="btnToggleFiltros" title="Filtrar resultados" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-filter text-primary"></i>
                </button>

                <!-- Mini panel desplegable -->
                <div id="panelFiltros" class="card shadow border p-3 position-absolute end-0 mt-2" style="display: none; width: 280px; z-index: 20; background: #ffffff;">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Filtrar Viajes</h6>
                    
                    <!-- Filtro por Agencia -->
                    <div class="mb-2">
                        <label for="filtroAgencia" class="form-label small fw-semibold">Agencia</label>
                        <select id="filtroAgencia" class="form-select form-select-sm">
                            <option value="">Todas las agencias</option>
                        </select>
                    </div>

                    <!-- Filtro por Precio Máximo -->
                    <div class="mb-2">
                        <label for="filtroPrecio" class="form-label small fw-semibold">Precio Máximo ($)</label>
                        <input type="number" id="filtroPrecio" class="form-control form-control-sm" placeholder="Ej. 500">
                    </div>

                    <!-- Filtro por Hitos / Tipo -->
                    <div class="mb-2">
                        <label for="filtroHito" class="form-label small fw-semibold">Tipo de Viaje</label>
                        <select id="filtroHito" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="directo">Directos</option>
                        </select>
                    </div>

                    <!-- Checkbox Mostrar Finalizados -->
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="chkMostrarFinalizados">
                        <label class="form-check-label small" for="chkMostrarFinalizados">
                            Mostrar viajes finalizados
                        </label>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" id="btnLimpiarFiltros" class="btn btn-outline-secondary btn-sm">Limpiar</button>
                        <button type="button" id="btnAplicarFiltros" class="btn btn-primary btn-sm">Aplicar</button>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-5">
                <h2 class="h4 mb-3 text-primary fw-bold">Viajes encontrados</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>Salida</th>
                                <th>Llegada</th>
                                <th>LLega en:</th>
                                <th>Asientos Disp.</th>
                                <th>Agencia</th>
                                <th>Precio</th>
                                <th>Marcar como Favorito</th>
                                <th>Ruta</th>
                                <th>Comprar Pasaje</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHorarios"></tbody>
                    </table>
                </div>
            </div>
        </div>

       <?php if (!empty($top_viajes)) { ?>
        <div class="card shadow-sm border-0 text-center mb-4">
            <div class="card-body p-4">
                <h2 class="h4 fw-bold mb-1 text-dark">
                    <i class="fa-solid fa-trophy text-warning me-2"></i>Top 3 Viajes Más Concurridos
                </h2>
                <p class="text-muted small mb-4">Los destinos y horarios preferidos por los pasajeros</p>

                <!-- Carrusel / Mini galería con flechas -->
                <div id="carouselTopViajes" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($top_viajes as $index => $tv) { 
                            $claseActiva = ($index === 0) ? 'active' : '';
                            
                            // Definir medalla / trofeo según el puesto
                            if ($index === 0) {
                                $badgeTrofeo = '<span class="badge bg-warning text-dark px-3 py-2 fw-bold fs-6 shadow-sm">🥇 1.° Puesto</span>';
                            } elseif ($index === 1) {
                                $badgeTrofeo = '<span class="badge bg-secondary px-3 py-2 fw-bold fs-6 shadow-sm">🥈 2.° Puesto</span>';
                            } else {
                                $badgeTrofeo = '<span class="badge bg-danger px-3 py-2 fw-bold fs-6 shadow-sm" style="background-color: #cd7f32 !important;">🥉 3.° Puesto</span>';
                            }
                        ?>
                            <div class="carousel-item <?= $claseActiva ?>">
                                <div class="card bg-white border shadow-sm mx-auto p-4 rounded-4" style="max-width: 480px;">
                                    <div class="mb-3">
                                        <?= $badgeTrofeo ?>
                                    </div>
                                    
                                    <h3 class="h5 fw-bold text-primary mb-3">
                                        <?= htmlspecialchars($tv['origen_nombre']) ?> &rarr; <?= htmlspecialchars($tv['destino_nombre']) ?>
                                    </h3>

                                    <div class="row text-start g-2 my-2 bg-light p-3 rounded-3 border">
                                        <div class="col-6"><strong>Salida:</strong> <?= htmlspecialchars($tv['hora']) ?></div>
                                        <div class="col-6"><strong>Llegada:</strong> <?= htmlspecialchars($tv['hora_llegada']) ?></div>
                                        <div class="col-6"><strong>Agencia:</strong> <?= htmlspecialchars($tv['agencia']) ?></div>
                                        <div class="col-6"><strong>Precio:</strong> $<span class="text-success fw-bold"><?= htmlspecialchars($tv['precio']) ?></span></div>
                                    </div>

                                    <div class="mt-3">
                                        <button 
                                            class="btn btn-sm btn-primary px-4 py-2 fw-bold btn-comprar shadow-sm"
                                            data-id="<?= $tv['id'] ?>"
                                            data-agencia="<?= htmlspecialchars($tv['agencia']) ?>">
                                            Comprar Pasaje
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Botones de navegación (Flechas) -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselTopViajes" data-bs-slide="prev" style="width: 5%;">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2 shadow" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselTopViajes" data-bs-slide="next" style="width: 5%;">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-2 shadow" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>

    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="<?= BASE_URL ?>assets/js/index/utils.js"></script>
    <script src="<?= BASE_URL ?>assets/js/index/mapas.js"></script>
    <script src="<?= BASE_URL ?>assets/js/index/buscador.js"></script>
    <script src="<?= BASE_URL ?>assets/js/index/interacciones.js"></script>
</body>
</html>