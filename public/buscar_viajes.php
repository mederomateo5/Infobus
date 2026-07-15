<?php
    // Configurar la respuesta como JSON
    header('Content-Type: application/json');
    require_once '../config/database.php';

    // Obtener parámetros de la URL
    $origen    = $_GET['origen'] ?? '';
    $destino   = $_GET['destino'] ?? '';
    $fecha     = $_GET['fecha'] ?? '';
    $pasajeros = filter_input(INPUT_GET, 'pasajeros', FILTER_VALIDATE_INT);

    // Validar parámetros requeridos
    if (empty($origen) || empty($destino) || empty($fecha) || !$pasajeros) {
        echo json_encode([]);
        exit();
    }

    try {
        // Consulta SQL seleccionando la columna ruta_key
        $query = "SELECT 
                    origen, 
                    destino, 
                    fecha, 
                    asientos_disponibles AS asientosLibres, 
                    agencia, 
                    precio,
                    ruta_key
                FROM omnibus 
                WHERE origen = :origen 
                    AND destino = :destino 
                    AND fecha = :fecha 
                    AND asientos_disponibles >= :pasajeros
                    AND ya_salio = 0
                ORDER BY hora ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':origen'    => $origen,
            ':destino'   => $destino,
            ':fecha'     => $fecha,
            ':pasajeros' => $pasajeros
        ]);

        $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sanitizar y tipar datos antes de enviarlos a Javascript
        foreach ($viajes as &$v) {
            $v['asientosLibres'] = (int)$v['asientosLibres'];
            $v['precio'] = (float)$v['precio'];
            // Respaldo por si el registro no tiene una ruta asignada en la BD
            $v['ruta_key'] = empty($v['ruta_key']) ? 'urbano' : $v['ruta_key'];
        }

        echo json_encode($viajes);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al consultar la base de datos.']);
    }
?>