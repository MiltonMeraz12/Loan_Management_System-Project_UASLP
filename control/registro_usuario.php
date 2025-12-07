<?php
session_start();
require_once '../bd/cad.php';

// Verificar permisos: Solo admin puede crear usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['userName'];
    $correo = $_POST['userEmail'];
    $password = $_POST['userPass']; // Contraseña temporal
    $rol = $_POST['userRol'];

    try {
        if (CAD::agregaUsuario($nombre, $correo, $password, $rol)) {
            header("Location: ../pages/manage_users.php?status=success");
        } else {
            header("Location: ../pages/manage_users.php?status=error");
        }
    } catch (Exception $e) {
        // Error común: Correo duplicado
        header("Location: ../pages/manage_users.php?status=error_duplicate");
    }
}
?>