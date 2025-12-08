<?php
session_start();
require_once '../bd/cad.php';

// Seguridad: Solo Admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos (los names deben coincidir con tu HTML)
    $nombre = $_POST['itemName'];
    $desc = $_POST['itemDesc'];
    $total = $_POST['itemTotal'];
    $categoria = $_POST['itemCategory'];

    if (CAD::agregarArticulo($nombre, $desc, $total, $categoria)) {
        header("Location: ../pages/manage_inventory.php?msg=agregado");
    } else {
        header("Location: ../pages/manage_inventory.php?error=bd");
    }
}
?>