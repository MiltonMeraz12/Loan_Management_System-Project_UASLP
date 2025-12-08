<?php
session_start(); // Iniciar sesión PHP para guardar datos del usuario
require_once '../bd/cad.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['email'];     // Debe coincidir con name="email" en tu HTML
    $password = $_POST['password']; // Debe coincidir con name="password" en tu HTML

    $usuario = CAD::verificaUsuario($correo, $password);

    if ($usuario) {
        // ¡Login Exitoso! Guardamos datos en sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre_completo'];
        $_SESSION['rol'] = $usuario['rol'];

        // Redirección según el rol
        if ($usuario['rol'] === 'admin') {
            header("Location: ../pages/admin_dashboard.php");
        } else {
            header("Location: ../pages/member_dashboard.php");
        }
        exit();
    } else {
        // Login Fallido: Regresar con error
        header("Location: ../pages/login.php?error=credenciales");
        exit();
    }
} else {
    // Si intentan entrar directo sin formulario
    header("Location: ../pages/login.php");
    exit();
}
?>