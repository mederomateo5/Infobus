<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros</title>
    <?php include '../includes/head.php'; ?>
</head>

<style>
    body {
        background-image:
            linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            url("../public/img/fondo_aereo.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>

<body class="bg-secondary-subtle">

    <?php include '../includes/header.php'; ?>

    <!-- Contenedor principal con diseño fluido, bordes redondeados y espaciado adaptativo -->
    <main class="container bg-white rounded-4 shadow-sm p-4 p-md-5 my-5">

        <div class="row justify-content-center g-5">

            <!-- Sebastián -->
            <div class="col-12 col-md-10 col-lg-8 text-center">
                <img
                    src="../public/img/Placeholder_Person.jpg"
                    alt="Sebastian De León"
                    class="rounded-circle shadow-sm mb-3 object-fit-cover"
                    width="85"
                    height="85"
                >
                <h2 class="fs-4 mb-2 fw-bold text-dark">
                    Sebastián De León<br>
                    <span class="fs-6 fw-normal text-muted">Recopilador de información / Administrador de base de datos</span>
                </h2>
                <p class="text-secondary mb-0 px-md-4">
                    Sebastián como recopilador de información y administrador de base de datos, se encarga de recabar información mediante técnicas de investigación y entrevistas, y de gestionar y mantener la base de datos del sistema, garantizando su correcto funcionamiento.
                </p>
            </div>

            <!-- Alejo -->
            <div class="col-12 col-md-10 col-lg-8 text-center">
                <img
                    src="../public/img/Placeholder_Person.jpg"
                    alt="Alejo Medina"
                    class="rounded-circle shadow-sm mb-3 object-fit-cover"
                    width="85"
                    height="85"
                >
                <h2 class="fs-4 mb-2 fw-bold text-dark">
                    Alejo Medina<br>
                    <span class="fs-6 fw-normal text-muted">Líder de documentación y Programador Secundario</span>
                </h2>
                <p class="text-secondary mb-0 px-md-4">
                    Alejo realiza informes técnicos, actas, normas y diagramas, manteniendo un registro claro del proyecto. Además, colabora en el desarrollo del sistema como apoyo al programador principal.
                </p>
            </div>

            <!-- Thiago -->
            <div class="col-12 col-md-10 col-lg-8 text-center">
                <img
                    src="../public/img/Placeholder_Person.jpg"
                    alt="Thiago Rodriguez"
                    class="rounded-circle shadow-sm mb-3 object-fit-cover"
                    width="85"
                    height="85"
                >
                <h2 class="fs-4 mb-2 fw-bold text-dark">
                    Thiago Rodriguez<br>
                    <span class="fs-6 fw-normal text-muted">Administrador de sistemas y Ayudante de documentación</span>
                </h2>
                <p class="text-secondary mb-0 px-md-4">
                    Thiago colabora en el mantenimiento y soporte técnico de los sistemas, y además asiste en la elaboración de informes, actas y registros del proyecto.
                </p>
            </div>

            <!-- Mateo -->
            <div class="col-12 col-md-10 col-lg-8 text-center">
                <img
                    src="../public/img/Placeholder_Person.jpg"
                    alt="Mateo Medero"
                    class="rounded-circle shadow-sm mb-3 object-fit-cover"
                    width="85"
                    height="85"
                >
                <h2 class="fs-4 mb-2 fw-bold text-dark">
                    Mateo Medero<br>
                    <span class="fs-6 fw-normal text-muted">Líder del proyecto y Programador principal</span>
                </h2>
                <p class="text-secondary mb-0 px-md-4">
                    Mateo se encarga de planificar, organizar y supervisar el cumplimiento de los plazos y tareas. Además, toma las decisiones clave, mantiene la comunicación entre todos los integrantes del grupo y lidera el desarrollo del sistema.
                </p>
            </div>

            <!-- Marcelo -->
            <div class="col-12 col-md-10 col-lg-8 text-center">
                <img
                    src="../public/img/Placeholder_Person.jpg"
                    alt="Marcelo Méndez"
                    class="rounded-circle shadow-sm mb-3 object-fit-cover"
                    width="85"
                    height="85"
                >
                <h2 class="fs-4 mb-2 fw-bold text-dark">
                    Marcelo Méndez<br>
                    <span class="fs-6 fw-normal text-muted">Administrador de sistemas y Soporte IT</span>
                </h2>
                <p class="text-secondary mb-0 px-md-4">
                    Marcelo se ocupa de la configuración, el mantenimiento y el correcto funcionamiento de los sistemas y equipos utilizados en el proyecto, brindando soporte técnico ante cualquier inconveniente.
                </p>
            </div>

        </div>

    </main>

</body>

</html>