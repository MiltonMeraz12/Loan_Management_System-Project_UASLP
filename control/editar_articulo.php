<?php
session_start();
require_once '../bd/cad.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['itemId']; // El input hidden
    $nombre = $_POST['itemName'];
    $desc = $_POST['itemDesc'];
    $total = $_POST['itemTotal'];
    $disponible = $_POST['itemAvailable'];
    $categoria = $_POST['itemCategory'];

    // Validación básica: No puede haber más disponibles que totales
    if ($disponible > $total) {
        header("Location: ../pages/manage_inventory.php?error=logica_cantidad");
        exit();
    }

    if (CAD::editarArticulo($id, $nombre, $desc, $total, $disponible, $categoria)) {
        header("Location: ../pages/manage_inventory.php?msg=editado");
    } else {
        header("Location: ../pages/manage_inventory.php?error=bd");
    }
}
?>