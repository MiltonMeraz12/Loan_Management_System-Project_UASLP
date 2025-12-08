<?php
session_start();
// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../bd/cad.php';
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generar Reportes - Sistema de Control</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <div class="reports-container">

            <div class="report-card">
                <div class="report-header">
                    <span class="material-icons">assessment</span>
                    <h2>Configuración del Reporte</h2>
                </div>
                
                <form id="report-config-form" action="../control/generar_reporte.php" method="GET" target="_blank" class="filter-form">
                    
                    <div class="form-group filter-group">
                        <label for="report-type">Tipo de Reporte</label>
                        <select id="report-type" name="reportType">
                            <option value="semanal" selected>Reporte Semanal</option>
                            <option value="mensual">Reporte Mensual</option>
                        </select>
                    </div>
                    
                    <div class="form-group filter-group" id="report-week-group">
                        <label for="report-week">Semana</label>
                        <input type="week" id="report-week" name="reportWeek" class="form-control" required> 
                    </div>

                    <div class="form-group filter-group hidden" id="report-month-group">
                        <label for="report-month">Mes</label>
                        <input type="month" id="report-month" name="reportMonth" class="form-control">
                    </div>

                    <div class="form-group filter-group">
                        <label for="report-category">Categoría</label>
                        <select id="report-category" name="category">
                            <option value="todas" selected>Todas</option>
                            <option value="consejeria">Consejería</option>
                            <option value="fup">FUP</option>
                        </select>
                    </div>
                    
                    <div class="filter-buttons"> 
                        <button type="submit" class="action-btn blue filter-submit-btn">
                            <span class="material-icons">picture_as_pdf</span> Generar PDF
                        </button>
                    </div>
                </form>
                <p class="card-desc report-note">
                    <span class="material-icons">info_outline</span>
                    Se generará un documento PDF con el listado detallado de préstamos del periodo seleccionado.
                </p>
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

        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>" type="text/javascript"></script>
    </body>
</html>