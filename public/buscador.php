<?php

    require_once '../config/database.php';

    // Solo aceptar POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir datos del formulario
    $nombre   = trim($_POST['textoDeBusqueda']   ?? ''); //consulta sql. mostrar tabla y como arreglar lo de que muestre lo de json que esta en crea
    echo $nombre;
    exit();


    }


   
   
    