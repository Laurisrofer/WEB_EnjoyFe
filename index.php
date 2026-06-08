<?php
$mostrarError = isset($_GET['error']) && $_GET['error'] === 'login';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Enjoyfe</title>
    <link rel="icon" type="image/png" href="recursos/favicon.png">
    <link rel="stylesheet" href="recursos/login.css">
    <style>
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
            z-index: 1000;
        }
        .modal-box {
            background: white; padding: 25px 30px; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); text-align: center; max-width: 400px; width: 90%;
        }
        .modal-box h2 { margin-top: 0; color: #d32f2f; }
        .modal-box p { color: #555; margin-bottom: 20px; line-height: 1.5; }
        .modal-btn {
            background: #d32f2f; color: white; border: none; padding: 10px 20px;
            border-radius: 6px; cursor: pointer; font-size: 16px; transition: background 0.2s;
        }
        .modal-btn:hover { background: #b71c1c; }
    </style>
</head>
<body>

    <?php if ($mostrarError): ?>
    <div class="modal-overlay" id="errorModal">
        <div class="modal-box">
            <h2>Error de acceso</h2>
            <p>El usuario y/o la contraseña introducidos no son correctos.<br>Por favor, inténtalo de nuevo.</p>
            <button class="modal-btn" onclick="document.getElementById('errorModal').style.display='none'">Aceptar</button>
        </div>
    </div>
    <?php endif; ?>

    <div class="login-container">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="recursos/logo_enjoyfe.png" alt="Logo Enjoyfe" style="max-width: 150px; height: auto;">
        </div>
        <h1 style="text-align: center;">Bienvenido a Enjoyfe</h1>
        
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

    <?php include 'componentes/footer.php'; ?>
</body>
</html>