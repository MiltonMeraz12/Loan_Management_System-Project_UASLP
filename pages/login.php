<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar Sesión - Sistema de Préstamos</title>
        <link href="../css/global_styles.css?v=2" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>

        <div class="login-container">

            <div class="login-logo"></div>

            <h1 class="login-title">Sistema de Control de Oficinas</h1>
            <h2 class="login-subtitle">VorTics Development</h2>

            <div class="login-card">
                <h3>Iniciar Sesión</h3>

                <?php
                if (isset($_GET['error'])) {
                    echo '<div style="background-color: #fde6e6; color: #d9534f; padding: 10px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #f5c6cb; text-align: center;">';
                    
                    if ($_GET['error'] == 'credenciales') {
                        echo 'Usuario o contraseña incorrectos.';
                    } else if ($_GET['error'] == 'inactivo') {
                        echo 'Tu cuenta ha sido desactivada.';
                    } else {
                        echo 'Ocurrió un error al iniciar sesión.';
                    }
                    
                    echo '</div>';
                }
                ?>

                <form id="login-form" action="../control/validar_login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="usuario@uaslp.mx" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="********" required>
                    </div>

                    <button type="submit" class="login-submit-button">
                        Iniciar Sesión
                    </button>
                </form>

                <div class="info-box">
                    <strong>Credenciales de prueba:</strong>
                    <p><b>Administrador:</b> admin@uaslp.mx / admin123</p>
                    <p><b>Miembro:</b> miembro@uaslp.mx / miembro123</p>
                </div>
            </div>

            <a href="../index.html" class="back-link">
                &larr; Volver al inicio
            </a>

        </div>

        <script src="../js/funcionalidad.js"></script>
    </body>
</html>