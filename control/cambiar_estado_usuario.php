<?php
session_start();
require_once '../bd/cad.php';

// Solo admin puede hacer esto
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

$idUsuario = $_GET['id'];
$nuevoEstado = $_GET['estado']; // 'activo' o 'inactivo'

// Nota: Agrega esta función a tu CAD
if (CAD::cambiarEstadoUsuario($idUsuario, $nuevoEstado)) {
    header("Location: ../pages/manage_users.php?msg=estado_actualizado");
} else {
    header("Location: ../pages/manage_users.php?error=bd");
}
?>