<?php
session_start();
require_once '../bd/cad.php';

// Seguridad: Solo Admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['userId'];
    $nombre = $_POST['userName'];
    $rol = $_POST['userRol'];
    // Nota: El correo no se suele editar para no romper el login, 
    // pero si quisieras, agrégalo aquí.

    if (CAD::editarUsuario($id, $nombre, $rol)) {
        header("Location: ../pages/manage_users.php?msg=usuario_editado");
    } else {
        header("Location: ../pages/manage_users.php?error=bd");
    }
}
?>