<?php
session_start();
require_once '../bd/cad.php';

// Configuramos la cabecera para que JS sepa que respondemos JSON
header('Content-Type: application/json');

// Verificar sesión (seguridad)
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if (isset($_GET['clave'])) {
    $clave = $_GET['clave'];
    
    // Buscamos al estudiante
    $estudiante = CAD::buscarEstudiante($clave);

    if ($estudiante) {
        // Encontramos datos, los devolvemos
        echo json_encode([
            'success' => true,
            'data' => $estudiante
        ]);
    } else {
        // No existe
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>