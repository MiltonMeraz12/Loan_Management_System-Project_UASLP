<?php
session_start();
require_once '../bd/cad.php';

// Si no hay sesión, fuera
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recibir datos del formulario
    $idArticulo = $_POST['itemId'];
    $categoria = $_POST['category'];
    
    // Datos del Estudiante
    $tipoId = $_POST['idType'];
    $claveEstudiante = $_POST['studentKey'];
    $nombreEstudiante = $_POST['studentName'];
    $telefonoEstudiante = $_POST['studentPhone'];
    $facultad = $_POST['studentFaculty']; 
    
    $comentarios = $_POST['comments'];
    $idUsuarioLogueado = $_SESSION['id_usuario'];

    // 2. Datos del Proxy
    $esProxy = isset($_POST['isProxy']) ? 1 : 0;
    $nombreProxy = null;
    $telefonoProxy = null;

    if ($esProxy) {
        $nombreProxy = $_POST['proxyName'];
        $telefonoProxy = $_POST['proxyPhone'];
    }

    // 3. Validaciones básicas
    if (empty($idArticulo) || empty($claveEstudiante) || empty($nombreEstudiante)) {
        header("Location: ../pages/register_loan.php?error=campos_vacios");
        exit();
    }

    // 4. Llamar a la función del CAD
    if (CAD::registrarPrestamo($idArticulo, $claveEstudiante, $nombreEstudiante, $telefonoEstudiante, $facultad, $tipoId, $idUsuarioLogueado, $esProxy, $nombreProxy, $telefonoProxy, $comentarios)) {
        
        // --- ÉXITO: MOSTRAR PANTALLA DE CONFIRMACIÓN ---
        $targetPage = ($_SESSION['rol'] === 'admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Préstamo Exitoso</title>
            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
            <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
            <style>
                body { 
                    display: flex; justify-content: center; align-items: center; 
                    height: 100vh; background-color: #f1f7fc; margin: 0;
                }
                .success-card { 
                    background: white; padding: 40px; border-radius: 12px; 
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; 
                    max-width: 400px; width: 90%; border: 1px solid #e0e0e0;
                }
                .success-icon { 
                    font-size: 80px; color: #28a745; margin-bottom: 20px; 
                    animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                .success-title { font-size: 24px; font-weight: bold; color: #1c398e; margin-bottom: 10px; }
                .success-text { font-size: 16px; color: #555; margin-bottom: 30px; line-height: 1.5; }
                
                @keyframes popIn {
                    0% { transform: scale(0); opacity: 0; }
                    100% { transform: scale(1); opacity: 1; }
                }
            </style>
        </head>
        <body>
            <div class="success-card">
                <span class="material-icons success-icon">check_circle</span>
                <div class="success-title">¡Préstamo Registrado!</div>
                <div class="success-text">La información ha sido guardada correctamente en el sistema.</div>
                
                <a href="../pages/' . $targetPage . '?msg=prestamo_exitoso" class="action-btn green" style="text-decoration:none; display:inline-block; margin:0;">
                    Continuar al Dashboard
                </a>
            </div>
        </body>
        </html>';
        exit(); // Detenemos el script aquí para mostrar el HTML
        
    } else {
        header("Location: ../pages/register_loan.php?error=error_bd");
    }

} else {
    header("Location: ../pages/register_loan.php");
}
?>