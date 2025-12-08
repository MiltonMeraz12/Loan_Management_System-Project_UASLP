<?php
session_start();
// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../bd/cad.php';

// 1. Obtener la lista de vencidos de la BD
$vencidos = CAD::getVencidos();

// 2. Calcular Estadísticas Avanzadas
$totalVencidos = count($vencidos);
$maxDias = 0;   // Para el "Más Crítico"
$sumDias = 0;   // Para el "Promedio"
$hoy = new DateTime();

foreach ($vencidos as $v) {
    $fechaLimite = new DateTime($v['fecha_limite']);
    // Calculamos la diferencia en días
    $diff = $hoy->diff($fechaLimite);
    $dias = $diff->days;

    // Buscar el máximo
    if ($dias > $maxDias) {
        $maxDias = $dias;
    }
    
    // Sumar para promedio
    $sumDias += $dias;
}

// Calcular promedio (evitando división por cero)
$promedioDias = ($totalVencidos > 0) ? round($sumDias / $totalVencidos) : 0;
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Préstamos Vencidos - Sistema de Control</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <div class="warning-banner <?php echo empty($vencidos) ? 'hidden' : ''; ?>" id="overdue-warning-banner">
            <span class="material-icons">warning</span>
            <span id="warning-message-text">Hay <?php echo $totalVencidos; ?> préstamos vencidos que requieren atención inmediata.</span>
        </div>

        <div class="overdue-container">
            
            <div class="overdue-stats-bar">
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Total Vencidos</h3>
                        <span class="stat-number urgent" id="overdue-total-count"><?php echo $totalVencidos; ?></span>
                        <span class="stat-desc">Préstamos sin devolver</span>
                    </div>
                    <span class="material-icons stat-icon urgent">warning</span>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Más Crítico</h3>
                        <span class="stat-number urgent"><?php echo $maxDias; ?> días</span>
                        <span class="stat-desc">Máximo retraso</span>
                    </div>
                    <span class="material-icons stat-icon urgent">crisis_alert</span> 
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Promedio</h3>
                        <span class="stat-number urgent"><?php echo $promedioDias; ?> días</span>
                        <span class="stat-desc">Retraso promedio</span>
                    </div>
                    <span class="material-icons stat-icon urgent">show_chart</span> 
                </div>

            </div>

            <div class="overdue-list-card">
                <h2>Lista de Préstamos Vencidos</h2>
                <div class="overdue-loan-list" id="overdue-loans-list">
                    
                    <?php foreach($vencidos as $v): ?>
                        <?php 
                            // Recalcular días para mostrar en la tarjeta individual
                            $fechaLimite = new DateTime($v['fecha_limite']);
                            $dias = $hoy->diff($fechaLimite)->days;
                        ?>
                        <div class="overdue-loan-item" 
                            data-loan-id="<?php echo $v['id_prestamo']; ?>" 
                            data-article="<?php echo $v['articulo']; ?>" 
                            data-student="<?php echo $v['estudiante']; ?>" 
                            data-key="<?php echo $v['clave_estudiante']; ?>" 
                            data-phone="<?php echo $v['telefono']; ?>" 
                            data-id-type="<?php echo $v['tipo_identificacion']; ?>" 
                            data-loan-date="<?php echo $v['fecha_prestamo']; ?>" 
                            data-due-date="<?php echo $v['fecha_limite']; ?>" 
                            data-registered-by="<?php echo $v['registrado_por']; ?>"
                            data-proxy-name="<?php echo $v['nombre_proxy']; ?>"
                            data-proxy-phone="<?php echo $v['telefono_proxy']; ?>"
                            data-days-overdue="<?php echo $dias; ?>">
                            
                            <div class="overdue-item-info">
                                <span class="overdue-item-title">
                                    <?php echo $v['articulo']; ?>
                                    <span class="status-tag overdue"><?php echo $dias; ?> días vencido</span>
                                    <span class="category-tag <?php echo $v['categoria']; ?>"><?php echo ucfirst($v['categoria']); ?></span>
                                </span>
                                <span class="overdue-item-details">Estudiante: <?php echo $v['estudiante']; ?> (<?php echo $v['clave_estudiante']; ?>)</span>
                                <span class="overdue-item-details">Tel: <?php echo $v['telefono']; ?></span>
                                <span class="overdue-item-details">Registrado por: <?php echo $v['registrado_por']; ?></span>
                            </div>
                            <div class="overdue-item-dates">
                                <span class="overdue-item-details">Fecha límite: <?php echo $v['fecha_limite']; ?></span>
                            </div>
                            <div class="overdue-item-actions">
                                <button class="action-btn outline small-btn view-details-btn">Ver Detalles</button>
                                <button class="action-btn green small-btn mark-returned-btn">Marcar como Devuelto</button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if(empty($vencidos)): ?>
                        <p style="text-align:center; padding:20px; color:#777;">¡Excelente! No hay préstamos vencidos.</p>
                    <?php endif; ?>

                </div>
            </div>
            
            <?php 
                $dashboardLink = ($_SESSION['rol'] === 'admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
            ?>

            <a href="<?php echo $dashboardLink; ?>" class="back-to-dash-btn">
                &larr; Volver al Dashboard
            </a>

            <div class="modal-overlay hidden" id="view-details-modal">
                <div class="modal-card">
                    <button class="modal-close-btn" id="close-view-details-modal-btn">
                        <span class="material-icons">close</span>
                    </button>
                    <h3>Detalles del Préstamo</h3>
                    
                    <div class="loan-details-modal">
                        <h4>Información General</h4>
                        <div class="detail-item"><strong>ID Préstamo:</strong> <span id="view-loan-id"></span></div>
                        <div class="detail-item"><strong>Artículo:</strong> <span id="view-article"></span></div>
                        <div class="detail-item"><strong>Categoría:</strong> <span id="view-category"></span></div>
                        <div class="detail-item"><strong>Registrado por:</strong> <span id="view-registered-by"></span></div>
                        
                        <h4>Fechas</h4>
                        <div class="detail-item"><strong>Fecha Préstamo:</strong> <span id="view-loan-date"></span></div>
                        <div class="detail-item"><strong>Fecha Límite:</strong> <span id="view-due-date"></span></div>
                        <div class="detail-item"><strong>Estado:</strong> <span id="view-days-overdue" class="urgent-text"></span></div>

                        <h4>Información del Estudiante</h4>
                        <div class="detail-item"><strong>Nombre:</strong> <span id="view-student-name"></span></div>
                        <div class="detail-item"><strong>Clave:</strong> <span id="view-student-key"></span></div>
                        <div class="detail-item"><strong>Teléfono:</strong> <span id="view-student-phone"></span></div>
                        <div class="detail-item"><strong>ID Presentada:</strong> <span id="view-id-type"></span></div>

                        <div id="view-proxy-section" class="hidden">
                            <h4>Recogido por (Proxy)</h4>
                            <div class="detail-item"><strong>Nombre:</strong> <span id="view-proxy-name"></span></div>
                            <div class="detail-item"><strong>Teléfono:</strong> <span id="view-proxy-phone"></span></div>
                        </div>
                    </div>

                    <div class="modal-button-row single-button">
                        <button type="button" class="action-btn outline modal-cancel-btn" id="ok-view-details-btn">Cerrar</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay hidden" id="confirm-return-modal">
                <div class="modal-card">
                    <button class="modal-close-btn" id="close-confirm-return-modal-btn">
                        <span class="material-icons">close</span>
                    </button>
                    <h3>Confirmar Devolución de Préstamo Vencido</h3>
                    
                    <div class="loan-details-modal">
                        <div class="detail-item"><strong>Artículo:</strong> <span id="modal-detail-article"></span></div>
                        <div class="detail-item"><strong>Estudiante:</strong> <span id="modal-detail-student"></span></div>
                        <div class="detail-item"><strong>Clave:</strong> <span id="modal-detail-key"></span></div>
                        <div class="detail-item"><strong>Fecha préstamo:</strong> <span id="modal-detail-loan-date"></span></div>
                        <div class="detail-item"><strong>Fecha límite:</strong> <span id="modal-detail-due-date"></span></div>
                        <div class="detail-item"><strong>Días Vencido:</strong> <span id="modal-detail-days-overdue" class="urgent-text"></span></div>
                    </div>
                    
                    <form id="confirm-return-form" action="../control/devolver_prestamo.php" method="POST">
                        <input type="hidden" id="confirm-loan-id" name="loanId"> 
                        <div class="comments-section form-group">
                            <label for="modal-return-comments">Comentarios de Devolución (Opcional)</label>
                            <textarea id="modal-return-comments" name="comments" rows="3" placeholder="Ej: Entregado con daño, se aplicará cargo..."></textarea>
                        </div>
                        <div class="modal-button-row">
                            <button type="button" class="action-btn outline modal-cancel-btn" id="cancel-confirm-return-btn">Cancelar</button>
                            <button type="submit" class="action-btn green modal-submit-btn">Confirmar Devolución</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <div class="footer">
            <div class="footer-copyright">
                <p>© 2025 VorTics Development (Milton Torres).</p>
            </div>
        </div>
        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>"></script>
    </body>
</html>