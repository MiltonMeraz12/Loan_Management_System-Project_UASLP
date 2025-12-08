<?php
session_start();
// Si no hay usuario logueado, redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../bd/cad.php';

// 1. Obtener historial completo
$historial = CAD::getHistorial();

// 2. Calcular Estadísticas para la pestaña "Estadísticas"
$conteoConsejeria = 0;
$conteoFup = 0;
$conteoArticulos = []; // Array para contar frecuencia de cada artículo

foreach ($historial as $h) {
    // Contar categorías
    if ($h['categoria'] == 'consejeria') {
        $conteoConsejeria++;
    } else {
        $conteoFup++;
    }

    // Contar frecuencia de artículos
    $nombreArt = $h['articulo'];
    if (!isset($conteoArticulos[$nombreArt])) {
        $conteoArticulos[$nombreArt] = 0;
    }
    $conteoArticulos[$nombreArt]++;
}

// Ordenar artículos por popularidad (Descendente) y tomar los top 5
arsort($conteoArticulos);
$topArticulos = array_slice($conteoArticulos, 0, 5);
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Historial de Préstamos - Sistema de Control</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <div class="history-container">

            <div class="filter-card">
                <h2>Buscar en Historial</h2>
                <form id="history-filter-form" class="filter-form">
                    
                    <div class="form-group filter-group">
                        <label for="search-type">Tipo de búsqueda</label>
                        <select id="search-type" name="searchType">
                            <option value="estudiante" selected>Por Estudiante</option>
                            <option value="articulo">Por Artículo</option>
                        </select>
                    </div>

                    <div class="form-group filter-group student-filter">
                        <label for="student-key-filter">Clave</label>
                        <input type="text" id="student-key-filter" name="studentKey" placeholder="123456" maxlength="6">
                    </div>
                    <div class="form-group filter-group student-filter">
                        <label for="student-name-filter">Nombre</label>
                        <input type="text" id="student-name-filter" name="studentName" placeholder="Juan Pérez">
                    </div>

                    <div class="form-group filter-group article-filter hidden">
                        <label for="article-name-filter">Artículo</label>
                        <input type="text" id="article-name-filter" name="articleName" placeholder="Calculadora, Laptop...">
                    </div>

                    <div class="form-group filter-group">
                        <label for="category-filter">Categoría</label>
                        <select id="category-filter" name="category">
                            <option value="todas" selected>Todas</option>
                            <option value="consejeria">Consejería</option>
                            <option value="fup">FUP</option>
                        </select>
                    </div>

                    <div class="form-group filter-group">
                        <label for="status-filter">Estado</label>
                        <select id="status-filter" name="status">
                            <option value="todos" selected>Todos</option>
                            <option value="activo">Activos</option>
                            <option value="devuelto">Devueltos</option>
                            <option value="vencido">Vencidos</option>
                        </select>
                    </div>
                    
                    <div class="filter-buttons"> 
                        <button type="button" class="action-btn outline clear-filters-btn" id="clear-filters-btn">Limpiar Filtros</button>
                        <button type="submit" class="action-btn blue filter-submit-btn">Buscar</button>
                    </div>
                </form>
            </div>

            <?php 
                $dashboardLink = ($_SESSION['rol'] === 'admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
            ?>

            <a href="<?php echo $dashboardLink; ?>" class="back-to-dash-btn">
                &larr; Volver al Dashboard
            </a>

            <div class="history-tabs">
                <button class="history-tab-button activo" data-tab-target="history-results">Resultados (<span id="results-count"><?php echo count($historial); ?></span>)</button>
                <button class="history-tab-button" data-tab-target="history-stats">Estadísticas</button>
            </div>

            <div class="history-content-area">

                <div class="history-tab-content" id="history-results">
                    <div class="history-list-card">
                        <div class="history-list-header">
                            <h2>Historial de Préstamos</h2>
                            <span class="item-count-badge" id="history-record-count"><?php echo count($historial); ?> registros</span>
                        </div>
                        <div class="history-loan-list" id="history-loans-list">
                            
                            <?php foreach($historial as $h): ?>
                                <?php
                                    // Lógica de estado visual
                                    $estadoVisual = '';
                                    $claseEstado = '';
                                    $hoy = new DateTime();
                                    $limite = new DateTime($h['fecha_limite']);

                                    if ($h['estado'] == 'devuelto') {
                                        $estadoVisual = 'Devuelto';
                                        $claseEstado = 'returned';
                                    } elseif ($hoy > $limite) {
                                        $estadoVisual = 'Vencido';
                                        $claseEstado = 'overdue';
                                    } else {
                                        $estadoVisual = 'Activo';
                                        $claseEstado = 'active';
                                    }
                                ?>

                                <div class="history-loan-item <?php echo $claseEstado; ?>"
                                    data-loan-id="<?php echo $h['id_prestamo']; ?>"
                                    data-article="<?php echo $h['articulo']; ?>"
                                    data-category="<?php echo ucfirst($h['categoria']); ?>"
                                    data-student="<?php echo $h['estudiante']; ?>"
                                    data-key="<?php echo $h['clave_estudiante']; ?>"
                                    data-phone="<?php echo $h['telefono'] ?? 'N/A'; ?>"
                                    data-id-type="<?php echo $h['tipo_identificacion'] ?? 'N/A'; ?>"
                                    data-loan-date="<?php echo date('d/m/Y H:i', strtotime($h['fecha_prestamo'])); ?>"
                                    data-due-date="<?php echo date('d/m/Y', strtotime($h['fecha_limite'])); ?>"
                                    data-return-date="<?php echo $h['fecha_devolucion'] ? date('d/m/Y H:i', strtotime($h['fecha_devolucion'])) : 'Pendiente'; ?>"
                                    data-registered-by="<?php echo $h['registrado_por']; ?>"
                                    data-proxy-name="<?php echo $h['nombre_proxy']; ?>"
                                    data-proxy-phone="<?php echo $h['telefono_proxy']; ?>"
                                    data-comments-loan="<?php echo $h['comentarios_prestamo']; ?>"
                                    data-comments-return="<?php echo $h['comentarios_devolucion']; ?>"
                                    data-status="<?php echo $estadoVisual; ?>">

                                    <div class="history-item-main">
                                        <span class="history-item-title">
                                            <?php echo $h['articulo']; ?>
                                            <span class="status-tag <?php echo $claseEstado; ?>"><?php echo $estadoVisual; ?></span>
                                            <span class="category-tag <?php echo $h['categoria']; ?>"><?php echo ucfirst($h['categoria']); ?></span>
                                        </span>
                                        <span class="history-item-details">Estudiante: <?php echo $h['estudiante']; ?> (<?php echo $h['clave_estudiante']; ?>)</span>
                                        <span class="history-item-details">Registrado por: <?php echo $h['registrado_por']; ?></span>
                                        
                                        <?php if($h['nombre_proxy']): ?>
                                            <span class="history-item-details"><i>Recogido por: <?php echo $h['nombre_proxy']; ?></i></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="history-item-dates">
                                        <span class="history-item-details">Prestado: <?php echo date('d/m/Y', strtotime($h['fecha_prestamo'])); ?></span>
                                        <?php if($h['fecha_devolucion']): ?>
                                            <span class="history-item-details">Devuelto: <b><?php echo date('d/m/Y', strtotime($h['fecha_devolucion'])); ?></b></span>
                                        <?php else: ?>
                                            <span class="history-item-details">Límite: <?php echo date('d/m/Y', strtotime($h['fecha_limite'])); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="history-item-actions">
                                        <button class="action-btn outline small-btn view-history-details-btn">
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if(empty($historial)): ?>
                                <p style="padding: 20px; text-align: center; color: #777;">No hay historial registrado.</p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <div class="history-tab-content hidden" id="history-stats">
                    <div class="history-stats-card">
                        <h2>Estadísticas Generales</h2>
                        <div class="stats-summary">
                            <div class="stat-category-count">
                                <span class="stat-number available"><?php echo $conteoConsejeria; ?></span>
                                <span>Consejería</span>
                            </div>
                            <div class="stat-category-count">
                                <span class="stat-number urgent"><?php echo $conteoFup; ?></span>
                                <span>FUP</span>
                            </div>
                        </div>
                        <hr class="form-divider">
                        <h3>Artículos más populares:</h3>
                        <ul class="popular-items-list">
                            <?php foreach($topArticulos as $nombre => $cantidad): ?>
                                <li>
                                    <span><?php echo $nombre; ?></span>
                                    <span><?php echo $cantidad; ?></span>
                                </li>
                            <?php endforeach; ?>
                            
                            <?php if(empty($topArticulos)): ?>
                                <li style="justify-content: center; color: #999;">No hay datos suficientes.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

            </div> 
        </div>

        <div class="modal-overlay hidden" id="history-details-modal">
            <div class="modal-card">
                <button class="modal-close-btn" id="close-history-modal-btn"><span class="material-icons">close</span></button>
                <h3>Detalles del Registro</h3>
                
                <div class="loan-details-modal">
                    <h4>Información General</h4>
                    <div class="detail-item"><strong>ID Préstamo:</strong> <span id="hist-id"></span></div>
                    <div class="detail-item"><strong>Artículo:</strong> <span id="hist-article"></span> (<span id="hist-category"></span>)</div>
                    <div class="detail-item"><strong>Estado Final:</strong> <span id="hist-status" style="font-weight:bold;"></span></div>
                    <div class="detail-item"><strong>Registrado por:</strong> <span id="hist-registered-by"></span></div>

                    <h4>Fechas</h4>
                    <div class="detail-item"><strong>Préstamo:</strong> <span id="hist-loan-date"></span></div>
                    <div class="detail-item"><strong>Límite:</strong> <span id="hist-due-date"></span></div>
                    <div class="detail-item"><strong>Devolución:</strong> <span id="hist-return-date"></span></div>

                    <h4>Estudiante</h4>
                    <div class="detail-item"><strong>Nombre:</strong> <span id="hist-student"></span></div>
                    <div class="detail-item"><strong>Clave:</strong> <span id="hist-key"></span></div>
                    <div class="detail-item"><strong>Teléfono:</strong> <span id="hist-phone"></span></div>
                    <div class="detail-item"><strong>ID Usada:</strong> <span id="hist-id-type"></span></div>

                    <div id="hist-proxy-section" class="hidden">
                        <h4>Intermediario</h4>
                        <div class="detail-item"><strong>Nombre:</strong> <span id="hist-proxy-name"></span></div>
                        <div class="detail-item"><strong>Teléfono:</strong> <span id="hist-proxy-phone"></span></div>
                    </div>

                    <h4>Comentarios</h4>
                    <div class="detail-item"><strong>Al Prestar:</strong> <span id="hist-com-loan" style="font-style:italic; color:#666;"></span></div>
                    <div class="detail-item"><strong>Al Devolver:</strong> <span id="hist-com-return" style="font-style:italic; color:#666;"></span></div>
                </div>

                <div class="modal-button-row single-button">
                    <button type="button" class="action-btn outline modal-cancel-btn" id="ok-history-modal-btn">Cerrar</button>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-copyright">
                <p>© 2025 VorTics Development (Milton Torres). Todos los derechos reservados.</p>
            </div>
        </div>

        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>" type="text/javascript"></script>
    </body>
</html>