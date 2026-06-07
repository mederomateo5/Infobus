<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$nombre = htmlspecialchars($_SESSION['user_name'] ?? 'Usuario');
$tipo   = htmlspecialchars($_SESSION['user_tipo']  ?? 'usuario');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ómnibus — Bienvenido</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg: #0d0f14; --surface: #161920; --border: #2a2f42;
            --accent: #f5a623; --text: #f0f2f8; --muted: #7a8099;
            --radius: 14px;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .emoji { font-size: 3.5rem; margin-bottom: 20px; display: block; }
        h1 {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            letter-spacing: -.03em;
            margin-bottom: 10px;
        }
        h1 span { color: var(--accent); }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 99px;
            background: rgba(245,166,35,.12);
            border: 1px solid rgba(245,166,35,.3);
            color: var(--accent);
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 28px;
        }
        p { color: var(--muted); margin-bottom: 32px; }
        a.btn {
            display: inline-block;
            padding: 11px 28px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: .9rem;
            transition: border-color .2s, color .2s;
        }
        a.btn:hover { border-color: var(--accent); color: var(--accent); }
        @media (max-width: 480px) { .card { padding: 32px 20px; } }
    </style>
</head>
<body>
    <div class="card">
        <span class="emoji">🚌</span>
        <h1>Hola, <span><?= $nombre ?></span></h1>
        <div class="badge"><?= $tipo ?></div>
        <p>Estás dentro del sistema de gestión de Ómnibus UY. Desde aquí podrás acceder a todas las funcionalidades según tu rol.</p>
        <a href="logout.php" class="btn">Cerrar sesión</a>
    </div>
</body>
</html>
