<?php
// Validamos que la sesión exista para evitar errores si se incluye mal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rolTexto = ($_SESSION['rol'] === 'admin') ? 'Administrador' : 'Miembro';
$rolClase = ($_SESSION['rol'] === 'admin') ? 'admin' : 'member';
?>

<div class="header-content">
    <div class="logo-VTDM"></div>
    <div class="empresa-sistema">
        <div class="nombre-sistema">Sistema de Control de Oficinas</div>
        <div class="developer-VTDM">VorTics Development</div>
    </div>
    <div class="logo-Ing"></div>
    <div class="nombres-uni">
        <div class="nombre-facultad">Facultad de Ingeniería</div>
        <div class="nombre-universidad">Universidad Autónoma de San Luis Potosí</div>
    </div>
    
    <div class="rol-login dashboard-rol">
        <div class="user-welcome">
            Bienvenido, <strong><?php echo $_SESSION['nombre']; ?></strong>
        </div>
        <div class="user-actions">
            <div class="rol <?php echo $rolClase; ?>">
                <?php echo $rolTexto; ?>
            </div>
            
            <a href="../control/logout.php" class="logout-button">
                Cerrar Sesión
            </a>
        </div>
    </div>
</div>