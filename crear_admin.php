<?php
// Incluimos el cerebro del sistema
require_once 'bd/cad.php';

echo "<h1>Creando Usuario Administrador...</h1>";

// Datos del Admin
$nombre = "Administrador Principal";
$correo = "admin@uaslp.mx";
$password = "admin123"; // Esta será tu contraseña
$rol = "admin";

try {
    // Intentamos agregarlo usando la función que ya programaste
    if (CAD::agregaUsuario($nombre, $correo, $password, $rol)) {
        echo "<h3 style='color: green;'>¡ÉXITO! Usuario creado correctamente.</h3>";
        echo "<p><strong>Correo:</strong> $correo</p>";
        echo "<p><strong>Contraseña:</strong> $password</p>";
        echo "<br><a href='pages/login.php'>Ir al Login</a>";
    } else {
        echo "<h3 style='color: red;'>ERROR: No se pudo crear.</h3>";
    }
} catch (Exception $e) {
    // Si sale error, probablemente es porque ya existe el correo (clave duplicada)
    echo "<h3 style='color: orange;'>AVISO: Probablemente el usuario ya existe.</h3>";
    echo "Intenta iniciar sesión directamente.<br>";
    echo "<a href='pages/login.php'>Ir al Login</a>";
}
?>