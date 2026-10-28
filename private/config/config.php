<?php
// Detectar protocolo (http o https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// Detectar host (localhost o dominio)
$host = $_SERVER['HTTP_HOST'];

// Detectar la ruta base hasta la carpeta 'public/'
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$public_pos = strpos($script_name, '/public/');

if ($public_pos !== false) {
    $base_path = substr($script_name, 0, $public_pos + 8);
} else {
    $base_path = rtrim(dirname($script_name), '/') . '/';
}

// Definir constante global
define('BASE_URL', $protocol . '://' . $host . $base_path);
