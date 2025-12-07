<?php
session_start();
require_once '../bd/cad.php';

// Verificar que el usuario (becario/admin) esté logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recibir datos básicos
    $idArticulo = $_POST['itemId'];       // Del <select name="itemId">
    $claveEstudiante = $_POST['studentKey']; // Del <input name="studentKey">
    $comentarios = $_POST['comments'];
    $idUsuarioLogueado = $_SESSION['id_usuario']; // El becario que está registrando

    // 2. Lógica del PROXY (Aquí ocurre la magia)
    $esProxy = 0;           // Por defecto es NO
    $nombreProxy = null;    // Por defecto vacío
    $telefonoProxy = null;  // Por defecto vacío

    // Verificamos si el checkbox 'isProxy' llegó en el POST
    if (isset($_POST['isProxy'])) {
        $esProxy = 1; // ¡Sí hay intermediario!
        $nombreProxy = $_POST['proxyName'];   // Recibimos nombre del amigo
        $telefonoProxy = $_POST['proxyPhone']; // Recibimos teléfono del amigo
    }

    // 3. Guardar en BD usando la función que creamos
    if (CAD::registrarPrestamo($idArticulo, $claveEstudiante, $idUsuarioLogueado, $esProxy, $nombreProxy, $telefonoProxy, $comentarios)) {
        // Éxito
        header("Location: ../pages/admin_dashboard.php?msg=prestamo_exitoso");
    } else {
        // Error
        header("Location: ../pages/register_loan.php?error=error_bd");
    }

} else {
    // Acceso directo no permitido
    header("Location: ../pages/register_loan.php");
}
?>