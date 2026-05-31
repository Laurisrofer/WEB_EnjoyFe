<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Enjoyfe</title>
    <link rel="stylesheet" href="recursos/login.css">
</head>
<body>

    <div class="login-container">
        <h1>Bienvenido a Enjoyfe</h1>
        
        <form action="acciones/procesar_login.php" method="POST">
            <div class="form-group">
                <label for="usuario">Nombre de usuario</label>
                <input type="text" id="usuario" name="usuario" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Iniciar sesión</button>
        </form>
    </div>

</body>
</html>