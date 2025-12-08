<?php
session_start();
// Seguridad: Solo admin entra
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../bd/cad.php';

// 1. Obtener Estadísticas
$stats = CAD::getEstadisticas();

// 2. Obtener Resumen de Inventario (Primeros 5)
$consejeria = CAD::getInventario('consejeria');
$fup = CAD::getInventario('fup');
$consejeria = array_slice($consejeria, 0, 5);
$fup = array_slice($fup, 0, 5);

// 3. Obtener Préstamos Recientes (Reutilizamos historial y tomamos los 3 primeros)
$historialCompleto = CAD::getHistorial();
$recientes = array_slice($historialCompleto, 0, 3);
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Administrador</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>Préstamos Activos</h3>
                    <span class="stat-number"><?php echo $stats['prestamos_activos']; ?></span>
                    <span class="stat-desc">Artículos prestados actualmente</span>
                </div>
                <span class="material-icons stat-icon blue">description</span>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>Préstamos Vencidos</h3>
                    <span class="stat-number urgent"><?php echo $stats['prestamos_vencidos']; ?></span>
                    <span class="stat-desc">Requieren atención inmediata</span>
                </div>
                <span class="material-icons stat-icon urgent">warning</span>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>Total de Artículos</h3>
                    <span class="stat-number available"><?php echo $stats['total_articulos']; ?></span>
                    <span class="stat-desc">En el inventario</span>
                </div>
                <span class="material-icons stat-icon available">inventory_2</span>
            </div>
        </div>

        <div class="dashboard-nav">
            <button class="dash-nav-button activo" data-page="prestamos">Gestión de Préstamos</button>
            <button class="dash-nav-button" data-page="inventario">Inventario</button>
            <button class="dash-nav-button" data-page="reportes">Reportes</button>
            <button class="dash-nav-button" data-page="admin">Administración</button>
        </div>

        <div class="dashboard-content-area">

            <div class="dash-page" id="page-prestamos">
                
                <div class="dash-card">
                    <h2 class="dash-card-title">Acciones Rápidas</h2>
                    <a href="register_loan.php" class="action-btn green">Registrar Nuevo Préstamo</a>
                    <a href="process_return.php" class="action-btn dark">Procesar Devolución</a>
                    <a href="overdue_loans.php" class="action-btn red">Ver Préstamos Vencidos</a>
                    <a href="loan_history.php" class="action-btn outline">Consultar Historial</a>
                </div>

                <div class="dash-card">
                    <h2 class="dash-card-title">Movimientos Recientes</h2>
                    
                    <?php foreach($recientes as $r): ?>
                        <?php 
                            // Lógica visual de estado (Idéntica a historial)
                            $hoy = new DateTime();
                            $limite = new DateTime($r['fecha_limite']);
                            $textoEstado = "Activo";
                            $claseEstado = "active";

                            if ($r['estado'] == 'devuelto') {
                                $textoEstado = "Devuelto";
                                // Usamos una clase gris personalizada o reutilizamos una existente
                                $claseEstado = "returned"; 
                            } elseif ($hoy > $limite) {
                                $textoEstado = "Vencido";
                                $claseEstado = "overdue";
                            }
                        ?>
                        <div class="recent-item">
                            <div class="item-details">
                                <span class="item-name"><?php echo $r['articulo']; ?></span>
                                <span class="item-user"><?php echo $r['estudiante']; ?> (<?php echo $r['clave_estudiante']; ?>)</span>
                                <span style="font-size: 0.85em; color: #999;">
                                    <?php echo date('d/m/Y', strtotime($r['fecha_prestamo'])); ?>
                                </span>
                            </div>
                            <span class="recent-status <?php echo $claseEstado; ?>">
                                <?php echo $textoEstado; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <?php if(empty($recientes)): ?>
                        <p style="color: #777; font-style: italic;">No hay movimientos recientes.</p>
                    <?php endif; ?>
                </div>

            </div>

            <div class="dash-page hidden" id="page-inventario">
                
                <div class="dash-card full-width">
                    <h2 class="dash-card-title">Acciones de Inventario</h2>
                    <a href="manage_inventory.php" class="action-btn blue">Gestionar Inventario Completo</a>
                    <p class="card-desc">Ir a la página de administración para agregar, editar o eliminar artículos.</p>
                </div>

                <div class="dash-card">
                    <h2 class="dash-card-title">Resumen Consejería (CEFI)</h2>
                    <div class="inventory-list dash-inventory-list">
                        <div class="inventory-row-header">
                            <div class="col-articulo">Artículo</div>
                            <div class="col-total">Total</div>
                            <div class="col-disponible">Disp.</div>
                            <div class="col-estado">Estado</div>
                        </div>
                        
                        <?php foreach($consejeria as $item): ?>
                        <div class="inventory-item">
                            <div class="col-articulo"><?php echo $item['nombre']; ?></div>
                            <div class="col-total"><?php echo $item['cantidad_total']; ?></div>
                            <div class="col-disponible <?php echo ($item['cantidad_disponible'] > 0) ? 'count-available' : ''; ?>">
                                <?php echo $item['cantidad_disponible']; ?>
                            </div>
                            <div class="col-estado">
                                <?php if($item['cantidad_disponible'] > 0): ?>
                                    <span class="status-badge available">Disp.</span>
                                <?php else: ?>
                                    <span class="status-badge unavailable">Agotado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($consejeria)): ?>
                            <p style="text-align: center; padding: 10px;">No hay artículos registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dash-card">
                    <h2 class="dash-card-title">Resumen FUP</h2>
                    <div class="inventory-list dash-inventory-list">
                        <div class="inventory-row-header">
                            <div class="col-articulo">Artículo</div>
                            <div class="col-total">Total</div>
                            <div class="col-disponible">Disp.</div>
                            <div class="col-estado">Estado</div>
                        </div>

                        <?php foreach($fup as $item): ?>
                        <div class="inventory-item">
                            <div class="col-articulo"><?php echo $item['nombre']; ?></div>
                            <div class="col-total"><?php echo $item['cantidad_total']; ?></div>
                            <div class="col-disponible <?php echo ($item['cantidad_disponible'] > 0) ? 'count-available' : ''; ?>">
                                <?php echo $item['cantidad_disponible']; ?>
                            </div>
                            <div class="col-estado">
                                <?php if($item['cantidad_disponible'] > 0): ?>
                                    <span class="status-badge available">Disp.</span>
                                <?php else: ?>
                                    <span class="status-badge unavailable">Agotado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if(empty($fup)): ?>
                            <p style="text-align: center; padding: 10px;">No hay artículos registrados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="dash-page hidden" id="page-reportes">
                <div class="dash-card full-width">
                    <h2 class="dash-card-title">Reportes y Estadísticas</h2>
                    <a href="reports.php" class="action-btn blue">Generar Reportes Detallados</a>
                    <div class="button-row">
                        <button class="action-btn outline" id="quick-report-weekly">Reporte Rápido Semanal</button>
                        <button class="action-btn outline" id="quick-report-monthly">Reporte Rápido Mensual</button>
                    </div>
                    <p class="card-desc">Como administrador, puedes generar reportes completos en PDF.</p>
                </div>
            </div>

            <div class="dash-page hidden" id="page-admin">
                <div class="dash-card">
                    <h2 class="dash-card-title">Gestión de Inventario</h2>
                    <a href="manage_inventory.php" class="action-btn blue">Administrar Artículos</a>
                    <p class="card-desc">Agregar, editar y eliminar artículos del inventario.</p>
                </div>
                <div class="dash-card">
                    <h2 class="dash-card-title">Gestión de Oficinas</h2>
                    <a href="manage_users.php" class="action-btn green">Administrar Sistema</a>
                    <p class="card-desc">Agregar y editar cuentas de miembros del equipo y modificación de oficinas.</p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-copyright">
                <p>© 2025 VorTics Development (Milton Torres).</p>
            </div>
        </div>

        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>" type="text/javascript"></script>
    </body>
</html>