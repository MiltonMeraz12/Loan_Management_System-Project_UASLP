<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') { header("Location: login.php"); exit(); }
require_once '../bd/cad.php';

$usuarios = CAD::getUsuarios();

// Cargar config de oficinas y sus servicios
$cefiInfo = CAD::getOficina('consejeria');
$cefiServicios = CAD::getServicios('consejeria');

$fupInfo = CAD::getOficina('fup');
$fupServicios = CAD::getServicios('fup');

// Stats de usuarios
$totalUsuarios = count($usuarios);
$totalAdmins = 0; $totalMiembros = 0; $totalActivos = 0;
foreach ($usuarios as $u) {
    if ($u['rol'] === 'admin') $totalAdmins++; else $totalMiembros++;
    if ($u['estado'] === 'activo') $totalActivos++;
}
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar Usuarios - Administrador</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <div class="stats-bar" id="users-stats-bar">
            <div class="stat-card"><div class="stat-info"><h3>Total Usuarios</h3><span class="stat-number" style="color: #6f42c1;"><?php echo $totalUsuarios; ?></span></div><span class="material-icons stat-icon users">groups</span></div>
            <div class="stat-card"><div class="stat-info"><h3>Administradores</h3><span class="stat-number" style="color: #fd7e14;"><?php echo $totalAdmins; ?></span></div><span class="material-icons stat-icon admins">shield</span></div>
            <div class="stat-card"><div class="stat-info"><h3>Miembros</h3><span class="stat-number" style="color: #0dcaf0;"><?php echo $totalMiembros; ?></span></div><span class="material-icons stat-icon members">person</span></div>
            <div class="stat-card"><div class="stat-info"><h3>Activos</h3><span class="stat-number available"><?php echo $totalActivos; ?></span></div><span class="material-icons stat-icon available">check_circle</span></div>
        </div>

        <div class="manage-inventory-section">
            <div class="manage-header">
                <h2 class="manage-title">Panel de Administración</h2>
            </div>

            <div class="dashboard-nav" style="margin: 0 0 24px 0; justify-content: flex-start; padding:0; box-shadow:none;">
                <button class="dash-nav-button activo" onclick="switchAdminTab('users')">Usuarios</button>
                <button class="dash-nav-button" onclick="switchAdminTab('offices')">Control de Oficinas</button>
            </div>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'config_actualizada'): ?>
                <div style="background-color:#dff0d8; color:#3c763d; padding:15px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                    <span class="material-icons">check_circle</span> Configuración de oficina actualizada correctamente.
                </div>
            <?php endif; ?>

            <div id="admin-tab-users">
                <div class="inventory-table-manage">
                    <div class="manage-header" style="border:none; padding-bottom:0;">
                        <h3>Directorio de Usuarios</h3>
                        <button class="action-btn green add-item-btn" id="open-add-user-modal-btn">
                            <span class="material-icons">person_add</span> Nuevo Usuario
                        </button>
                    </div>
                    
                    <div class="user-manage-header"><div>Nombre</div><div>Correo</div><div>Rol</div><div>Estado</div><div>Acciones</div></div>
                    <div id="user-list-container">
                        <?php foreach($usuarios as $u): ?>
                            <div class="user-manage-item" data-id="<?php echo $u['id_usuario']; ?>" data-name="<?php echo $u['nombre_completo']; ?>" data-email="<?php echo $u['correo']; ?>" data-rol="<?php echo $u['rol']; ?>">
                                <div class="col-article"><span class="item-name"><?php echo $u['nombre_completo']; ?></span></div>
                                <div class="col-desc"><?php echo $u['correo']; ?></div>
                                <div class="col-cat"><span class="rol-badge <?php echo ($u['rol']=='admin')?'admin':'member'; ?>"><?php echo ucfirst($u['rol']); ?></span></div>
                                <div class="col-status"><span class="status-badge <?php echo ($u['estado']=='activo')?'available':'unavailable'; ?>"><?php echo ucfirst($u['estado']); ?></span></div>
                                <div class="col-actions">
                                    <button class="action-btn small-btn edit-user-btn"><span class="material-icons" style="font-size:18px; margin:0;">edit</span></button>
                                    <?php if($u['estado']=='activo'): ?><button class="action-btn small-btn red deactivate toggle-active-btn"><span class="material-icons" style="font-size:18px; margin:0;">block</span></button>
                                    <?php else: ?><button class="action-btn small-btn green activate toggle-active-btn"><span class="material-icons" style="font-size:18px; margin:0;">check_circle</span></button><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div id="admin-tab-offices" class="hidden">
                <div class="services-grid" style="grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
                    
                    <div class="office-config-card">
                        <div class="office-header">
                            <h3 style="color:#bfa523;">
                                <span class="material-icons" style="font-size: 32px;">meeting_room</span> Consejería (CEFI)
                            </h3>
                        </div>
                        <form action="../control/actualizar_config.php" method="POST">
                            <input type="hidden" name="clave_oficina" value="consejeria">
                            
                            <div class="form-group">
                                <label class="section-label">Estado General</label>
                                <select name="estado_oficina" class="config-select" style="width:100%;">
                                    <option value="abierto" <?php echo ($cefiInfo['estado']=='abierto')?'selected':''; ?>>🟢 Abierto (Público)</option>
                                    <option value="cerrado" <?php echo ($cefiInfo['estado']=='cerrado')?'selected':''; ?>>🔴 Cerrado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="section-label">Horario Visible</label>
                                <input type="text" name="horario" value="<?php echo $cefiInfo['horario']; ?>" class="form-control" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                            </div>

                            <label class="section-label" style="margin-top: 24px; color:#1c398e; border-bottom: 1px solid #eee; padding-bottom: 5px;">Disponibilidad de Servicios</label>
                            <div style="margin-top: 10px;">
                                <?php foreach($cefiServicios as $s): ?>
                                    <div class="service-row">
                                        <div class="service-info">
                                            <div class="service-icon-wrapper"><span class="material-icons"><?php echo $s['icono']; ?></span></div>
                                            <?php echo $s['nombre']; ?>
                                        </div>
                                        <select name="servicios[<?php echo $s['id_servicio']; ?>]" class="config-select <?php echo ($s['estado']=='disponible')?'status-available':'status-unavailable'; ?>" onchange="updateSelectColor(this)">
                                            <option value="disponible" <?php echo ($s['estado']=='disponible')?'selected':''; ?>>Disponible</option>
                                            <option value="no_disponible" <?php echo ($s['estado']=='no_disponible')?'selected':''; ?>>No disponible</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="action-btn blue" style="margin-top:24px;">Guardar Configuración CEFI</button>
                        </form>
                    </div>

                    <div class="office-config-card">
                        <div class="office-header">
                            <h3 style="color:#17a2b8;">
                                <span class="material-icons" style="font-size: 32px;">groups</span> Federación (FUP)
                            </h3>
                        </div>
                        <form action="../control/actualizar_config.php" method="POST">
                            <input type="hidden" name="clave_oficina" value="fup">
                            
                            <div class="form-group">
                                <label class="section-label">Estado General</label>
                                <select name="estado_oficina" class="config-select" style="width:100%;">
                                    <option value="abierto" <?php echo ($fupInfo['estado']=='abierto')?'selected':''; ?>>🟢 Abierto (Público)</option>
                                    <option value="cerrado" <?php echo ($fupInfo['estado']=='cerrado')?'selected':''; ?>>🔴 Cerrado</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="section-label">Horario Visible</label>
                                <input type="text" name="horario" value="<?php echo $fupInfo['horario']; ?>" class="form-control" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                            </div>

                            <label class="section-label" style="margin-top: 24px; color:#1c398e; border-bottom: 1px solid #eee; padding-bottom: 5px;">Disponibilidad de Servicios</label>
                            <div style="margin-top: 10px;">
                                <?php foreach($fupServicios as $s): ?>
                                    <div class="service-row">
                                        <div class="service-info">
                                            <div class="service-icon-wrapper"><span class="material-icons"><?php echo $s['icono']; ?></span></div>
                                            <?php echo $s['nombre']; ?>
                                        </div>
                                        <select name="servicios[<?php echo $s['id_servicio']; ?>]" class="config-select <?php echo ($s['estado']=='disponible')?'status-available':'status-unavailable'; ?>" onchange="updateSelectColor(this)">
                                            <option value="disponible" <?php echo ($s['estado']=='disponible')?'selected':''; ?>>Disponible</option>
                                            <option value="no_disponible" <?php echo ($s['estado']=='no_disponible')?'selected':''; ?>>No disponible</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="action-btn blue" style="margin-top:24px;">Guardar Configuración FUP</button>
                        </form>
                    </div>

                </div>
            </div>

            <a href="admin_dashboard.php" class="back-to-dash-btn"> &larr; Volver al Dashboard</a>
        </div>
        
        <div class="modal-overlay hidden" id="add-user-modal">
            <div class="modal-card">
                <button class="modal-close-btn" id="close-add-user-modal-btn"><span class="material-icons">close</span></button>
                <h3>Agregar Nuevo Usuario</h3>
                <form id="add-user-form" action="../control/registro_usuario.php" method="POST">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="userName" required placeholder="Ej: Juan Pérez">
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="userEmail" required placeholder="usuario@uaslp.mx">
                    </div>
                    <div class="form-group">
                        <label>Contraseña Inicial</label>
                        <input type="password" name="userPass" required placeholder="********">
                    </div>
                    <div class="form-group">
                        <label>Rol de Sistema</label>
                        <select name="userRol">
                            <option value="miembro">Miembro (Acceso básico)</option>
                            <option value="admin">Administrador (Acceso total)</option>
                        </select>
                    </div>
                    <button type="submit" class="action-btn green modal-submit-btn">Registrar Usuario</button>
                </form>
            </div>
        </div>

        <div class="modal-overlay hidden" id="edit-user-modal">
            <div class="modal-card">
                <button class="modal-close-btn" id="close-edit-user-modal-btn"><span class="material-icons">close</span></button>
                <h3>Editar Información</h3>
                <form id="edit-user-form" action="../control/editar_usuario.php" method="POST">
                    <input type="hidden" id="edit-user-id" name="userId">
                    
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" id="edit-user-name" name="userName" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico (No editable)</label>
                        <input type="email" id="edit-user-email" name="userEmail" disabled style="background-color: #f0f0f0; color: #777;">
                    </div>
                    <div class="form-group">
                        <label>Rol de Sistema</label>
                        <select id="edit-user-rol" name="userRol" required>
                            <option value="miembro">Miembro</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="modal-button-row">
                        <button type="button" class="action-btn outline modal-cancel-btn" id="cancel-edit-user-btn">Cancelar</button>
                        <button type="submit" class="action-btn blue modal-submit-btn">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer">
            <div class="footer-copyright">
                <p>© 2025 VorTics Development (Milton Torres).</p>
            </div>
        </div>
        
        <script>
            function switchAdminTab(tabName) {
                document.getElementById('admin-tab-users').classList.add('hidden');
                document.getElementById('admin-tab-offices').classList.add('hidden');
                document.getElementById('admin-tab-' + tabName).classList.remove('hidden');
                
                const userStats = document.getElementById('users-stats-bar');
                if(tabName === 'users') userStats.style.display = 'flex';
                else userStats.style.display = 'none';

                document.querySelectorAll('.dash-nav-button').forEach(btn => btn.classList.remove('activo'));
                event.target.classList.add('activo');
            }

            // Cambiar color del select al instante
            function updateSelectColor(select) {
                if(select.value === 'no_disponible') {
                    select.classList.remove('status-available');
                    select.classList.add('status-unavailable');
                } else {
                    select.classList.remove('status-unavailable');
                    select.classList.add('status-available');
                }
            }
        </script>
        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>"></script>
    </body>
</html>