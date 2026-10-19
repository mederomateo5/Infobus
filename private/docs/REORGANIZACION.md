# Reorganización de Infobus

Se reorganizó la estructura del proyecto sin cambiar su lógica, sus consultas SQL ni su diseño. Solo se movieron/renombraron archivos y se actualizaron las rutas que los referencian.

## Estructura nueva

```text
Infobus/
├── index.php                      (redirige a public/index.php)
├── private/                       (NO accesible por URL: ver private/.htaccess)
│   ├── config/    config.php, database.php
│   ├── database/  schema.sql, data.sql
│   ├── includes/  head.php, header.php, sidebar.php
│   ├── src/       User.php, server.js
│   └── docs/      README.md, REORGANIZACION.md
└── public/
    ├── index.php, urbanos.php, paradas.php, sobre_nosotros.php
    ├── auth/      login.php, logout.php, redirigir.php
    ├── api/       obtener_paradas.php, registrar_visita.php
    ├── paneles/   panel_admin.php, panel_compania.php, panel_usuario.php
    ├── gestion/   ver_usuarios, ver_companias, ver_omnibus, ver_paradas, ver_rutas, ver_viajes, editar_viaje
    ├── alta/  baja/  actualizar/  buscador/  usuario/   (sin cambios de lugar)
    └── assets/    css/  img/  js/  data/
```

## Movimientos y renombres

### Configuración y base de datos

| Antes | Ahora |
|---|---|
| `config/config.php` | `private/config/config.php` |
| `config/data.sql` | `private/database/data.sql` |
| `config/database.php` | `private/config/database.php` |
| `config/schema.sql` | `private/database/schema.sql` |

### Archivos privados

| Antes | Ahora |
|---|---|
| `README.md` | `private/docs/README.md` |
| `includes/head.php` | `private/includes/head.php` |
| `includes/header.php` | `private/includes/header.php` |
| `includes/sidebar.php` | `private/includes/sidebar.php` |
| `public/js/server.js` | `private/src/server.js` |
| `src/User.php` | `private/src/User.php` |

### Páginas y endpoints

| Antes | Ahora |
|---|---|
| `public/editar_viaje.php` | `public/gestion/editar_viaje.php` |
| `public/login.php` | `public/auth/login.php` |
| `public/logout.php` | `public/auth/logout.php` |
| `public/obtener_paradas.php` | `public/api/obtener_paradas.php` |
| `public/panel_admin.php` | `public/paneles/panel_admin.php` |
| `public/panel_compania.php` | `public/paneles/panel_compania.php` |
| `public/panel_usuario.php` | `public/paneles/panel_usuario.php` |
| `public/redirigir.php` | `public/auth/redirigir.php` |
| `public/registrar_visita.php` | `public/api/registrar_visita.php` |
| `public/usuarios.php` | `public/gestion/ver_usuarios.php` |
| `public/vcompania.php` | `public/gestion/ver_companias.php` |
| `public/viajes.php` | `public/gestion/ver_viajes.php` |
| `public/vomnibus.php` | `public/gestion/ver_omnibus.php` |
| `public/vparadas.php` | `public/gestion/ver_paradas.php` |
| `public/vrutas.php` | `public/gestion/ver_rutas.php` |

### Recursos (assets)

| Antes | Ahora |
|---|---|
| `public/css/header.css` | `public/assets/css/header.css` |
| `public/css/login.css` | `public/assets/css/login.css` |
| `public/css/panel_admin.css` | `public/assets/css/panel_admin.css` |
| `public/css/responsive.css` | `public/assets/css/responsive.css` |
| `public/img/Placeholder_Person.jpg` | `public/assets/img/Placeholder_Person.jpg` |
| `public/img/cot.png` | `public/assets/img/cot.png` |
| `public/img/favicon/android-chrome-192x192.png` | `public/assets/img/favicon/android-chrome-192x192.png` |
| `public/img/favicon/android-chrome-512x512.png` | `public/assets/img/favicon/android-chrome-512x512.png` |
| `public/img/favicon/apple-touch-icon.png` | `public/assets/img/favicon/apple-touch-icon.png` |
| `public/img/favicon/favicon-16x16.png` | `public/assets/img/favicon/favicon-16x16.png` |
| `public/img/favicon/favicon-32x32.png` | `public/assets/img/favicon/favicon-32x32.png` |
| `public/img/favicon/favicon.ico` | `public/assets/img/favicon/favicon.ico` |
| `public/img/favicon/site.webmanifest` | `public/assets/img/favicon/site.webmanifest` |
| `public/img/fondo.jpg` | `public/assets/img/fondo.jpg` |
| `public/img/fondo_aereo.jpg` | `public/assets/img/fondo_aereo.jpg` |
| `public/img/logotipo.png` | `public/assets/img/logotipo.png` |
| `public/img/tab.png` | `public/assets/img/tab.png` |
| `public/img/turil.svg` | `public/assets/img/turil.svg` |
| `public/js/buscarcompania.js` | `public/assets/js/buscar_compania.js` |
| `public/js/buscaromnibus.js` | `public/assets/js/buscar_omnibus.js` |
| `public/js/buscarparadas.js` | `public/assets/js/buscar_paradas.js` |
| `public/js/buscarrutas.js` | `public/assets/js/buscar_rutas.js` |
| `public/js/buscarusuarios.js` | `public/assets/js/buscar_usuarios.js` |
| `public/js/buscarviajes.js` | `public/assets/js/buscar_viajes.js` |
| `public/js/index/buscador.js` | `public/assets/js/index/buscador.js` |
| `public/js/index/interacciones.js` | `public/assets/js/index/interacciones.js` |
| `public/js/index/mapas.js` | `public/assets/js/index/mapas.js` |
| `public/js/index/utils.js` | `public/assets/js/index/utils.js` |
| `public/js/paradas.js` | `public/assets/js/mapa_paradas.js` |
| `public/js/recorrido_urbano.geojson` | `public/assets/data/recorrido_urbano.geojson` |
| `public/js/urbanos.js` | `public/assets/js/urbanos.js` |
| `public/js/validaciones.js` | `public/assets/js/validaciones.js` |

Los 53 archivos de `alta/`, `baja/`, `actualizar/`, `buscador/` y `usuario/` (y `public/index.php`, `urbanos.php`, `paradas.php`, `sobre_nosotros.php`) no cambiaron de lugar ni de nombre; solo se actualizaron las rutas que tienen adentro.

## Qué se modificó dentro del código

Únicamente rutas (no se tocó ninguna otra línea):

- 219 × `require`/`include` (config, database, head, header, sidebar, User)
- 116 × redirecciones `header("Location: ...")`
- 65 × URLs armadas con `BASE_URL` (css, js, img y enlaces)
- 32 × `href` relativos
- 27 × rutas dentro de JS (`fetch`, `window.location`)
- 6 × menú activo del sidebar (`$pagina_actual == ...`)
- 6 × `src` relativos
- 3 × `url()` de fondos en estilos inline
- 1 × README (ruta de database.php)
- 1 × redirect de `marcar_favorito.php`

Total: 476 reescrituras. `config.php` se movió sin modificar una sola línea.

## Archivo nuevo

- `private/.htaccess`: bloquea el acceso directo por URL a todo lo que hay en `private/` (credenciales, SQL, includes). Si tu Apache devolviera error 500 por este archivo, borralo y avisame.

## Cosas que ya estaban así y NO se tocaron

1. `gestion/ver_omnibus.php` (antes `vomnibus.php`) carga `buscar_omnibus.js`, que llama a `alta/baja_omnibus.php`; ese archivo no existe, el real es `baja/baja_omnibus.php`.
2. `usuario/favoritos.php` llama a `buscador/toggle_favorito.php`; ese archivo no existe, el real es `usuario/marcar_favorito.php`.
3. `private/src/server.js` (Node/Express) no lo usa ninguna página y su `require("./urbanos")` apunta a un archivo sin exports: ya no funcionaba. Solo se movió fuera de `public/`.
4. `gestion/editar_viaje.php` no lo enlaza ninguna página.
5. `private/database/data.sql` tiene una línea de comentario escrita como `--condicional ...` (sin espacio después de `--`), que MySQL no reconoce como comentario.

## Cómo se verificó

- `php -l` sobre los 73 archivos PHP: sin errores de sintaxis.
- Ninguna ruta apunta a un archivo inexistente (salvo las 2 de arriba, que ya eran así).
- 306 referencias comparadas contra el original: todas llegan al mismo archivo (ya movido).
- Sitio original y reorganizado levantados en paralelo con PHP 8.3 y una base de prueba; recorridos como anónimo, administrador, compañía y usuario: mismo estado HTTP, mismas redirecciones, mismos enlaces y mismo texto visible en todas las páginas.
- 96 pruebas extra de endpoints de los JS, formularios (alta/actualizar) y pantallas de edición: respuestas idénticas.
- No se pudo probar en un navegador real (JavaScript en ejecución, mapas Leaflet): probalo vos con F12 → Console.
