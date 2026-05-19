<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Header Responsive</title>

  <style>

    header{
      position:fixed;
      top:0;
      left:0;
      width:100%;
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:1rem 2rem;
      background:white;
      border: 2px solid green;
      z-index:1000;
    }
    .logo{
        width:100%;
  max-width:300px;
  height:auto;
    }

  </style>

</head>
<body>

<header>
<nav>
    <img class="logo" src=../public/img/logotipo.jpg>
</nav>


</header>

</body>
</html>
