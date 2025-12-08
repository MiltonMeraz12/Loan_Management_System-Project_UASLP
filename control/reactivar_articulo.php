<?php
session_start();
require_once '../bd/cad.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (CAD::reactivarArticulo($id)) {
        header("Location: ../pages/manage_inventory.php?msg=reactivado");
    } else {
        header("Location: ../pages/manage_inventory.php?error=bd");
    }
} else {
    header("Location: ../pages/manage_inventory.php");
}
?>