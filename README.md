# 🚌 InfoBus

**InfoBus** es una aplicación web desarrollada como proyecto final de **UTU**, orientada a facilitar la consulta de información sobre servicios de ómnibus y sus horarios.

El proyecto busca centralizar la información de diferentes empresas de transporte y permitir que los usuarios puedan consultar los viajes disponibles de una forma sencilla y organizada.

## 📌 Características

* 🚌 Consulta de empresas de transporte.
* 🕐 Consulta de horarios de ómnibus.
* 📅 Búsqueda de viajes según una fecha y el día de la semana correspondiente.
* 📍 Visualización de paradas mediante un mapa interactivo.
* 👤 Sistema de usuarios.
* 🏢 Gestión de empresas de transporte.
* 🔐 Diferentes tipos de usuario y permisos.
* 🗄️ Base de datos MySQL para almacenar la información del sistema.
* 📱 Interfaz adaptable a diferentes tamaños de pantalla.

## 🛠️ Tecnologías utilizadas

### Frontend

* HTML5
* CSS3
* JavaScript
* Bootstrap
* Leaflet

### Backend

* PHP
* MySQL
* PDO

### Herramientas

* XAMPP
* Visual Studio Code
* Git
* GitHub

## 🗄️ Base de datos

InfoBus utiliza **MySQL** para almacenar y organizar la información relacionada con:

* Usuarios
* Empresas de transporte
* Ómnibus
* Paradas
* Recorridos
* Viajes
* Días de viaje
* Horarios

La aplicación utiliza **PDO** para realizar la conexión con la base de datos desde PHP.

## 📅 Sistema de viajes

Una de las funcionalidades principales del sistema es la consulta de viajes a partir de una fecha.

El usuario selecciona una fecha y el sistema determina qué día de la semana corresponde. A partir de ese día se muestran los viajes disponibles para esa jornada.

Por ejemplo:

```text
Fecha seleccionada
       ↓
Se obtiene el día de la semana
       ↓
Se buscan los viajes correspondientes
       ↓
Se muestran los horarios disponibles
```

De esta forma, no es necesario almacenar una fecha específica para cada viaje que se repite semanalmente.

## 📍 Mapa de paradas

El proyecto utiliza **Leaflet** para mostrar las paradas de ómnibus en un mapa.

Cada parada cuenta con información de ubicación mediante coordenadas geográficas, permitiendo al usuario visualizar dónde se encuentra cada punto.

## 👥 Tipos de usuario

El sistema contempla diferentes tipos de usuarios con diferentes niveles de acceso.

Entre ellos se encuentran:

* **Usuario:** puede consultar la información disponible.
* **Compañía:** puede gestionar información relacionada con su empresa y servicios.
* **Administrador:** cuenta con permisos para administrar diferentes aspectos del sistema.

## 🚍 Empresas

El proyecto trabaja con información de empresas de transporte y sus respectivos servicios.

Entre las empresas contempladas se encuentran:

* TAB
* COT
* Turil
* Urbano

La información se organiza en la base de datos para relacionar empresas, vehículos, viajes, horarios y recorridos.

## 💻 Instalación

Para ejecutar InfoBus de forma local se necesita tener instalado un entorno compatible con PHP y MySQL. Para el desarrollo se utiliza **XAMPP**.

### 1. Clonar el repositorio

```bash
git clone https://github.com/mederomateo5/ProyectoUTU2.git
```

### 2. Mover el proyecto

Colocar la carpeta del proyecto dentro de:

```text
C:\xampp\htdocs\
```

Por ejemplo:

```text
C:\xampp\htdocs\InfoBus
```

### 3. Iniciar XAMPP

Iniciar los servicios:

* Apache
* MySQL

### 4. Crear la base de datos

Crear la base de datos utilizada por InfoBus en **phpMyAdmin** o mediante MySQL.

Luego importar el archivo SQL del proyecto, si se encuentra disponible en el repositorio.

### 5. Configurar la conexión

Modificar la configuración de conexión a la base de datos en:

```text
private/config/database.php
```

Ejemplo:

```php
$host = "localhost";
$dbname = "infobus";
$username = "root";
$password = "";
```

### 6. Abrir el proyecto

Desde el navegador:

```text
http://localhost/InfoBus/
```

## 🎯 Objetivo del proyecto

El objetivo principal de InfoBus es desarrollar una plataforma que permita consultar información de transporte de forma centralizada, evitando tener que buscar horarios y datos de diferentes empresas por separado.

Además, el proyecto permite aplicar conocimientos adquiridos durante el bachillerato en áreas como:

* Desarrollo web
* Programación
* Bases de datos
* Diseño de interfaces
* PHP
* JavaScript
* HTML y CSS
* Modelado de datos
* Control de versiones con Git

## 🚧 Estado del proyecto

**En desarrollo 🚧**

InfoBus continúa siendo desarrollado como proyecto académico. Algunas funcionalidades pueden encontrarse en proceso de implementación, modificación o mejora.

## 👨‍💻 Autor

Proyecto desarrollado por **Mateo Medero, Alejo Medina, Sebastián De Leon, Marcelo Méndez y Thiago Rodriguez** como parte del bachillerato de **Tecnologías de la Información – UTU**.

---
