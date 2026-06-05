<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - Enjoyfe</title>
    <link rel="icon" type="image/png" href="recursos/favicon.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #333;
        }
        .error-container {
            text-align: center;
            padding: 50px 30px;
            max-width: 500px;
        }
        .error-code {
            font-size: 8em;
            font-weight: 900;
            color: #1a5632;
            line-height: 1;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 1.5em;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }
        .error-message {
            font-size: 1em;
            color: #777;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background: #1a5632;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1em;
            transition: background 0.2s, transform 0.2s;
        }
        .btn-home:hover {
            background: #0e3d22;
            transform: translateY(-2px);
        }
        .brand {
            margin-top: 40px;
            font-size: 0.85em;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-title">Página no encontrada</div>
        <p class="error-message">La página que buscas no existe o ha sido movida. Comprueba la dirección o vuelve al inicio.</p>
        <a href="dashboard.php" class="btn-home">Volver al inicio</a>
        <div class="brand">Enjoyfe</div>
    </div>
    <?php include 'componentes/footer.php'; ?>
</body>
</html>
