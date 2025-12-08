<?php
session_start();
require_once '../bd/cad.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claveOficina = $_POST['clave_oficina']; // 'consejeria' o 'fup'
    $estadoOficina = $_POST['estado_oficina']; // 'abierto' o 'cerrado'
    $horario = $_POST['horario'];
    
    // Los servicios vienen en un array: servicios[id] = 'disponible'
    $servicios = $_POST['servicios'] ?? [];

    if (CAD::actualizarConfiguracion($claveOficina, $estadoOficina, $horario, $servicios)) {
        header("Location: ../pages/manage_users.php?msg=config_actualizada");
    } else {
        header("Location: ../pages/manage_users.php?error=bd");
    }
}
?>