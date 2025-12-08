<?php
session_start();
// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
require_once '../bd/cad.php';

// 1. Obtenemos la lista de la BD
$prestamos = CAD::getPrestamosActivos();

// 2. Calculamos las estadísticas
$totalActivos = count($prestamos);
$totalVencidos = 0;
$totalAlDia = 0;

$hoy = new DateTime(); 

foreach ($prestamos as $p) {
    $fechaLimite = new DateTime($p['fecha_limite']);
    if ($hoy > $fechaLimite) {
        $totalVencidos++;
    } else {
        $totalAlDia++;
    }
}
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Procesar Devolución - Sistema de Control</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'devuelto'): ?>
            <div class="success-banner" id="return-success-banner">
                <span class="material-icons">check_circle</span>
                <span id="success-message-text">Préstamo devuelto exitosamente.</span>
            </div>
        <?php endif; ?>

        <div class="return-container">
            
            <div class="active-loans-column">
                <div class="loan-list-card">
                    <div class="loan-list-header">
                        <h2>Préstamos Activos</h2>
                        <span class="item-count-badge" id="active-loan-count"><?php echo $totalActivos; ?> préstamos</span>
                    </div>
                    
                    <div class="loan-search">
                        <input type="text" id="loan-search-input" placeholder="Buscar por nombre, clave o artículo...">
                        <span class="material-icons search-icon">search</span>
                    </div>
                    
                    <div class="loan-list" id="active-loans-list">
                        <?php foreach($prestamos as $p): ?>
                            <?php 
                                $fechaLimite = new DateTime($p['fecha_limite']);
                                $esVencido = ($hoy > $fechaLimite);
                                $claseVencido = $esVencido ? 'overdue' : '';
                            ?>
                            
                            <div class="loan-item <?php echo $claseVencido; ?>" 
                                data-loan-id="<?php echo $p['id_prestamo']; ?>" 
                                data-article="<?php echo $p['articulo']; ?>" 
                                data-student="<?php echo $p['estudiante']; ?>" 
                                data-key="<?php echo $p['clave_estudiante']; ?>" 
                                data-phone="<?php echo $p['telefono']; ?>"
                                data-id-type="<?php echo $p['tipo_identificacion']; ?>" 
                                data-loan-date="<?php echo $p['fecha_prestamo']; ?>" 
                                data-due-date="<?php echo $p['fecha_limite']; ?>" 
                                data-registered-by="<?php echo $p['registrado_por']; ?>"
                                data-proxy-name="<?php echo $p['nombre_proxy']; ?>" 
                                data-proxy-phone="<?php echo $p['telefono_proxy']; ?>">
                                
                                <span class="loan-item-title">
                                    <?php echo $p['articulo']; ?>
                                    <?php if($esVencido): ?>
                                        <span class="status-tag overdue">Vencido</span>
                                    <?php endif; ?>
                                </span>
                                <span class="loan-item-details">Estudiante: <?php echo $p['estudiante']; ?> (<?php echo $p['clave_estudiante']; ?>)</span>
                                <span class="loan-item-details">Fecha límite: <?php echo $p['fecha_limite']; ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if(empty($prestamos)): ?>
                            <p style="padding:20px; text-align:center; color:#777;">No hay préstamos activos.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="return-details-column">
                <div class="return-details-card">
                    <h2>Procesar Devolución</h2>
                    
                    <div class="placeholder-message" id="return-placeholder">
                        <span class="material-icons placeholder-icon" style="font-size: 48px; color: #ccc;">receipt_long</span>
                        <p>Selecciona un préstamo de la lista...</p>
                    </div>
                    
                    <div class="loan-details hidden" id="return-details-section">
                        <h4>Detalles del Préstamo</h4>
                        
                        <div class="detail-item"><strong>Artículo:</strong> <span id="detail-article"></span></div>
                        <div class="detail-item"><strong>Estudiante:</strong> <span id="detail-student"></span></div>
                        <div class="detail-item"><strong>Clave:</strong> <span id="detail-key"></span></div>
                        <div class="detail-item"><strong>Teléfono:</strong> <span id="detail-phone"></span></div>
                        <div class="detail-item"><strong>ID Usada:</strong> <span id="detail-id-type"></span></div>
                        
                        <div class="detail-item hidden" id="detail-proxy-info" style="background:#f9f9f9; padding:8px; border-radius:4px; margin-top:8px;">
                            <strong>Recogido por:</strong> <span id="detail-proxy-name"></span> 
                            <br><small>Tel: <span id="detail-proxy-phone"></span></small>
                        </div>

                        <hr class="form-divider" style="margin: 15px 0;">
                        <div class="detail-item"><strong>Fecha préstamo:</strong> <span id="detail-loan-date"></span></div>
                        <div class="detail-item"><strong>Fecha límite:</strong> <span id="detail-due-date"></span></div>
                        <div class="detail-item"><strong>Registrado por:</strong> <span id="detail-registered-by"></span></div>
                        
                        <div id="overdue-warning" class="hidden" style="margin-top: 10px;">
                            <span class="status-tag overdue">⚠️ Préstamo Vencido</span>
                        </div>

                        <form action="../control/devolver_prestamo.php" method="POST">
                            <input type="hidden" name="loanId" id="detail-loan-id-input"> 
                            
                            <div class="comments-section form-group">
                                <label>Comentarios de Devolución</label>
                                <textarea id="return-comments" name="comments" rows="3" placeholder="Ej: Entregado en buen estado..."></textarea>
                            </div>

                            <button type="submit" class="action-btn green" id="confirm-return-btn">Confirmar Devolución</button>
                            <button type="button" class="action-btn outline" id="cancel-return-btn">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="quick-stats-card full-width-stats">
            <h3>Estadísticas Rápidas</h3>
            <div class="stats-content"> 
                <div class="stat-item">
                    <span>Total activos:</span> 
                    <span class="stat-value" id="stat-total-active"><?php echo $totalActivos; ?></span>
                </div>
                <div class="stat-item">
                    <span>Vencidos:</span> 
                    <span class="stat-value stat-urgent" id="stat-overdue"><?php echo $totalVencidos; ?></span>
                </div>
                <div class="stat-item">
                    <span>Al día:</span> 
                    <span class="stat-value stat-ok" id="stat-on-time"><?php echo $totalAlDia; ?></span>
                </div>
            </div>

            <?php 
                $dashboardLink = ($_SESSION['rol'] === 'admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
            ?>

            <a href="<?php echo $dashboardLink; ?>" class="back-to-dash-btn">
                &larr; Volver al Dashboard
            </a>
        </div>

        <div class="footer">
            <div class="footer-copyright">
                <p>© 2025 VorTics Development (Milton Torres). Todos los derechos reservados.</p>
            </div>
        </div>

        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>"></script>
        <script>
            // Pequeño script para pasar el ID al input oculto cuando se selecciona (Respaldo)
            // La lógica principal ya está en funcionalidad.js, esto asegura el valor del ID.
            document.getElementById('active-loans-list').addEventListener('click', (e) => {
                const item = e.target.closest('.loan-item');
                if(item) {
                    document.getElementById('detail-loan-id-input').value = item.dataset.loanId;
                }
            });
        </script>
    </body>
</html>