<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/marcelo_about_style.css">
</head>
<body>
    <style> 

        body{ 
    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    background-image:
        linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
        url("img/fondo_aereo.jpg");
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}
main {
    margin-top: 100px;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    background-color: rgb(240, 237, 237);
    width: 45%;
    justify-self: center;
    border-radius: 50px;
    padding: 10px;

}
main div{  
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    margin: 12px;
}
p{
    height: 80px;
    width: 400px;
    text-align: center;
}
h2 {
    display: flex;
    text-align: center;
}
.info img{
    width:85px; 
    height:85px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

    </style>
<?php include '../includes/header.php'; ?>
<main>
    <div class="info">
        <img src="img/Placeholder_Person.jpg" alt="">
        <h2>Sebastian De León<br> Tester</h2>
        <p>Como tester (QA), se encarga de detectar y corregir errores, realizar pruebas de usabilidad, reportar fallos y validar que el producto final cumpla con los criterios definidos. También verifica que el sistema funcione correctamente en distintos dispositivos.</p>
    </div class="info">
    <div class="info">
        <img src="img/Placeholder_Person.jpg" alt="">
        <h2>Alejo Medina<br> Documentación</h2>
        <p>Responsable de la documentación y las entrevistas, realiza informes técnicos, actas, normas y diagramas. Además, se encarga de mantener un registro claro del proyecto y recabar información mediante técnicas de investigación y entrevistas.</p>
    </div>
    <div class="info">
        <img src="img/Placeholder_Person.jpg" alt="">
        <h2>Thiago Rodriguez<br> Desarrolador Front-end</h2>
        <p>Como desarrollador Front-end, se encarga de desarrollar el diseño visual y la experiencia del usuario. Define la apariencia de la interfaz y colabora con el desarrollador Back-end para garantizar que el diseño sea funcional y efectivo en distintas plataformas.</p>
    </div>
    <div class="info">
        <img src="img/Placeholder_Person.jpg" alt="">
        <h2>Mateo Medero<br> Project Manager</h2>
        <p>Coordinador general o Project Manager, se encarga de planificar, organizar y supervisar el cumplimiento de los plazos y tareas. Además, toma las decisiones clave y mantiene la comunicación entre todos los integrantes del grupo.</p>
    </div>
    <div class="info">
        <img src="img/Placeholder_Person.jpg" alt="">
        <h2>Marcelo Méndez<br> Desarrolador Back-end</h2>
        <p>Como desarrollador Back-end, se ocupa de desarrollar las funcionalidades del sistema, como el login, además de gestionar la lógica del sistema y la base de datos. También debe garantizar el correcto funcionamiento del sistema.</p>
            
    </div>
</main>
</body>
</html>