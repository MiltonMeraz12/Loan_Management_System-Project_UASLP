<?php
require_once 'bd/cad.php';

// 1. Cargar datos de Consejería
$cefiInfo = CAD::getOficina('consejeria');
$cefiServicios = CAD::getServicios('consejeria');
$cefiInventario = array_slice(CAD::getInventario('consejeria'), 0, 3); // Top 3 items

// 2. Cargar datos de FUP
$fupInfo = CAD::getOficina('fup');
$fupServicios = CAD::getServicios('fup');
$fupInventario = array_slice(CAD::getInventario('fup'), 0, 3); // Top 3 items
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistema de Control de Oficinas - UASLP</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
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
            <div class="rol-login">
                <div class="rol">Visitante</div>
                <a href="pages/login.php" class="login-button">Iniciar Sesión</a>
            </div>
        </div>

        <div class="nav-content">
            <div class="filtros">
                <button class="tab-button activo" data-tab="consejeria">Consejería Estudiantil</button>
                <button class="tab-button" data-tab="fup">Federación Universitaria</button>
            </div>
            <div class="link-grupo">
                <a href="#">Grupo Estudiantil</a>
            </div>
        </div>
        
        <div class="main-contenido">
    
            <div class="status-seccion">
                
                <div class="status-card <?php echo ($cefiInfo['estado']=='abierto')?'open':'closed'; ?>" id="consejeria-status">
                    <div class="status-header">
                        Estado del Servicio
                        <span class="status-badge"><?php echo ucfirst($cefiInfo['estado']); ?></span>
                    </div>
                    <div class="status-body">
                        <div class="logo-conse"></div> 
                        <p class="status-text">
                            <?php echo ($cefiInfo['estado']=='abierto') ? '¡Estamos atendiendo!' : 'Oficina cerrada por el momento'; ?>
                        </p>
                        <div class="status-schedule">
                            <p class="schedule-days">Horario de Atención</p>
                            <p class="schedule-hours"><?php echo $cefiInfo['horario']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="status-card <?php echo ($fupInfo['estado']=='abierto')?'open':'closed'; ?> hidden" id="fup-status">
                    <div class="status-header">
                        Estado del Servicio
                        <span class="status-badge"><?php echo ucfirst($fupInfo['estado']); ?></span>
                    </div>
                    <div class="status-body">
                        <div class="logo-fup"></div> 
                        <p class="status-text">
                            <?php echo ($fupInfo['estado']=='abierto') ? '¡Estamos atendiendo!' : 'Oficina cerrada por el momento'; ?>
                        </p>
                        <div class="status-schedule">
                            <p class="schedule-days">Horario de Atención</p>
                            <p class="schedule-hours"><?php echo $fupInfo['horario']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="services-section" id="consejeria-services">
                <h2 class="services-title">Servicios Consejería (CEFI)</h2>
                <div class="services-grid">
                    <?php foreach($cefiServicios as $s): ?>
                        <div class="service-card <?php echo ($s['estado']=='disponible')?'available':'unavailable'; ?>">
                            <span class="material-icons service-icon-font"><?php echo $s['icono']; ?></span>
                            <p class="service-name"><?php echo $s['nombre']; ?></p>
                            <span class="service-status">
                                <?php echo ($s['estado']=='disponible')?'Disponible':'No disponible'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="services-section hidden" id="fup-services">
                <h2 class="services-title">Servicios FUP</h2>
                <div class="services-grid">
                    <?php foreach($fupServicios as $s): ?>
                        <div class="service-card <?php echo ($s['estado']=='disponible')?'available':'unavailable'; ?>">
                            <span class="material-icons service-icon-font"><?php echo $s['icono']; ?></span>
                            <p class="service-name"><?php echo $s['nombre']; ?></p>
                            <span class="service-status">
                                <?php echo ($s['estado']=='disponible')?'Disponible':'No disponible'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div> 

        <div class="inventory-section" id="consejeria-inventory"> 
            <div class="inventory-header">
                <h2 class="inventory-title">Inventario de Consejería</h2>
                <span class="update-status">Tiempo Real</span>
            </div>
            <div class="inventory-list">
                <div class="inventory-row-header">
                    <div class="col-articulo">Artículo</div>
                    <div class="col-total">Disp.</div>
                    <div class="col-estado" style="text-align: right;">Estado</div>
                </div>
                <?php foreach($cefiInventario as $item): ?>
                <div class="inventory-item">
                    <div class="col-articulo"><?php echo $item['nombre']; ?></div>
                    <div class="col-total count-available"><?php echo $item['cantidad_disponible']; ?></div>
                    <div class="col-estado" style="text-align: right;">
                        <span class="status-badge <?php echo ($item['cantidad_disponible']>0)?'available':'unavailable'; ?>">
                            <?php echo ($item['cantidad_disponible']>0)?'Disponible':'Agotado'; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="inventory-section hidden" id="fup-inventory"> 
            <div class="inventory-header">
                <h2 class="inventory-title">Inventario de FUP</h2>
                <span class="update-status">Tiempo Real</span>
            </div>
            <div class="inventory-list">
                <div class="inventory-row-header">
                    <div class="col-articulo">Artículo</div>
                    <div class="col-total">Disp.</div>
                    <div class="col-estado" style="text-align: right;">Estado</div>
                </div>
                <?php foreach($fupInventario as $item): ?>
                <div class="inventory-item">
                    <div class="col-articulo"><?php echo $item['nombre']; ?></div>
                    <div class="col-total count-available"><?php echo $item['cantidad_disponible']; ?></div>
                    <div class="col-estado" style="text-align: right;">
                        <span class="status-badge <?php echo ($item['cantidad_disponible']>0)?'available':'unavailable'; ?>">
                            <?php echo ($item['cantidad_disponible']>0)?'Disponible':'Agotado'; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="footer">
            
            <div class="footer-container">

                <div class="footer-columns">
                    
                    <div class="footer-column">
                        <h3>Contacto</h3>
                        <div class="contact-item">
                            <span class="material-icons">email</span> 
                            <span>consejeriafupinge@uaslp.mx</span>
                        </div>
                        <div class="contact-item">
                            <span class="material-icons">phone</span>
                            <span>(444) 826-2300</span>
                        </div>
                        <div class="contact-item">
                            <span class="material-icons">location_on</span>
                            <span>Av. Dr. Manuel Nava #304, Zona Universitaria, S.L.P.</span>
                        </div>
                    </div>

                    <div class="footer-column">
                        <h3>Horario Regular</h3>
                        <p>Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                        <p>Sábados: Cerrado</p>
                        <p>Domingos: Cerrado</p>
                    </div>

                    <div class="footer-column">
                        <h3>Enlaces Útiles</h3>
                        <a href="#">Portal Universitario (Ingenieria)</a>
                        <a href="#">Reglamento de Préstamos</a>
                        <a href="#">Soporte Técnico</a>
                    </div>

                </div> <hr class="footer-divider">

                <div class="footer-copyright">
                    <p>© 2025 VorTics Development (Milton Torres). Todos los derechos reservados.</p>
                </div>

            </div>
        </div>

        <script src="js/funcionalidad.js"></script>
    </body>
</html>
