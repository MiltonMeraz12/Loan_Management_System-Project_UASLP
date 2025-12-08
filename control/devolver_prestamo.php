<?php
session_start();
require_once '../bd/cad.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPrestamo = $_POST['loanId'];
    $comentarios = $_POST['comments'];
    $idUsuarioDevolucion = $_SESSION['id_usuario'];

    if (CAD::devolverPrestamo($idPrestamo, $idUsuarioDevolucion, $comentarios)) {
        
        // --- ÉXITO: MOSTRAR PANTALLA DE CONFIRMACIÓN ---
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devolución Exitosa</title>
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
                    font-size: 80px; color: #155dfc; /* Azul para diferenciar de registro */
                    margin-bottom: 20px; 
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
                <span class="material-icons success-icon">task_alt</span>
                <div class="success-title">Devolución Completada</div>
                <div class="success-text">El artículo ha sido reingresado al inventario y el préstamo cerrado.</div>
                
                <a href="../pages/process_return.php?msg=devuelto" class="action-btn blue" style="text-decoration:none; display:inline-block; margin:0;">
                    Continuar
                </a>
            </div>
        </body>
        </html>';
        exit(); // Detenemos el script para mostrar el HTML

    } else {
        header("Location: ../pages/process_return.php?error=bd");
    }
}
?>