<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header("Location: login.php"); exit(); }

require_once '../bd/cad.php';

// 1. Obtener datos COMPLETOS (Activos + Bajas)
$itemsConsejeria = CAD::getInventarioAdmin('consejeria');
$itemsFUP = CAD::getInventarioAdmin('fup');
$todosLosArticulos = array_merge($itemsConsejeria, $itemsFUP);

// 2. Calcular Estadísticas (Solo contamos activos para "Disponibles")
$totalArticulos = count($todosLosArticulos);
$totalConsejeria = count($itemsConsejeria);
$totalFUP = count($itemsFUP);
$totalDisponibles = 0;

foreach ($todosLosArticulos as $item) {
    if ($item['estado'] === 'activo') {
        $totalDisponibles += $item['cantidad_disponible'];
    }
}
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestionar Inventario - Administrador</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php include '../includes/header.php'; ?>

        <?php if(isset($_GET['msg'])): ?>
            <div style="background-color:#dff0d8; color:#3c763d; padding:15px; margin:20px 24px 0; border-radius:8px; display:flex; align-items:center; gap:10px;">
                <span class="material-icons">check_circle</span> 
                <?php 
                    if($_GET['msg'] == 'eliminado') echo "Artículo dado de baja correctamente.";
                    if($_GET['msg'] == 'reactivado') echo "Artículo reactivado exitosamente.";
                    if($_GET['msg'] == 'agregado') echo "Artículo agregado correctamente.";
                    if($_GET['msg'] == 'editado') echo "Artículo editado correctamente.";
                ?>
            </div>
        <?php endif; ?>

        <div class="inventory-stats-bar">
            
            <div class="stat-card">
                <div class="stat-info">
                    <h3>Total Registros</h3>
                    <span class="stat-number" style="color: #1c398e;"><?php echo $totalArticulos; ?></span>
                    <span class="stat-desc">Histórico completo</span>
                </div>
                <span class="material-icons stat-icon total-items">inventory_2</span>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>Consejería</h3>
                    <span class="stat-number" style="color: #bfa523;"><?php echo $totalConsejeria; ?></span>
                    <span class="stat-desc">Artículos</span>
                </div>
                <span class="material-icons stat-icon cat-consejeria">meeting_room</span> 
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>FUP</h3>
                    <span class="stat-number" style="color: #17a2b8;"><?php echo $totalFUP; ?></span>
                    <span class="stat-desc">Artículos</span>
                </div>
                <span class="material-icons stat-icon cat-fup">groups</span> 
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3>Unidades Libres</h3>
                    <span class="stat-number available"><?php echo $totalDisponibles; ?></span>
                    <span class="stat-desc">En artículos activos</span>
                </div>
                <span class="material-icons stat-icon available">check_circle</span>
            </div>
        </div>

        <div class="manage-inventory-section">
            <div class="manage-header">
                <h2 class="manage-title">Inventario de Artículos</h2>
                <button class="action-btn green add-item-btn" id="open-add-modal-btn">
                    <span class="material-icons">add</span> Agregar Nuevo Artículo
                </button>
            </div>

            <div class="inventory-table-manage">
                <div class="inv-manage-header">
                    <div class="col-article">Artículo</div>
                    <div class="col-desc">Descripción</div>
                    <div class="col-cat">Categoría</div>
                    <div class="col-total">Total</div>
                    <div class="col-disp">Disponible</div>
                    <div class="col-status">Estado</div>
                    <div class="col-actions">Acciones</div>
                </div>

                <?php foreach($todosLosArticulos as $item): ?>
                    <?php 
                        $esBaja = ($item['estado'] === 'baja');
                        $claseFila = $esBaja ? 'item-baja' : '';
                    ?>
                    <div class="inv-manage-item <?php echo $claseFila; ?>" data-id="<?php echo $item['id_articulo']; ?>">
                        <div class="col-article">
                            <span class="item-name"><?php echo $item['nombre']; ?></span>
                            <span class="item-id">ID: <?php echo $item['id_articulo']; ?></span>
                        </div>
                        <div class="col-desc"><?php echo $item['descripcion']; ?></div>
                        <div class="col-cat">
                            <span class="category-tag <?php echo $item['categoria']; ?>">
                                <?php echo ($item['categoria'] == 'consejeria') ? 'Consejería' : 'FUP'; ?>
                            </span>
                        </div>
                        <div class="col-total"><?php echo $item['cantidad_total']; ?></div>
                        
                        <div class="col-disp <?php echo ($item['cantidad_disponible'] > 0 && !$esBaja) ? 'count-available' : 'count-unavailable'; ?>">
                            <?php echo $item['cantidad_disponible']; ?>
                        </div>
                        
                        <div class="col-status">
                            <?php if($esBaja): ?>
                                <span class="status-badge baja">Baja</span>
                            <?php else: ?>
                                <span class="status-badge available">Activo</span>
                            <?php endif; ?>
                        </div>

                        <div class="col-actions">
                            <button class="edit-btn" data-id="<?php echo $item['id_articulo']; ?>"><span class="material-icons">edit</span></button>
                            
                            <?php if($esBaja): ?>
                                <button class="reactivate-btn" data-id="<?php echo $item['id_articulo']; ?>" title="Reactivar">
                                    <span class="material-icons">restore_from_trash</span> Activar
                                </button>
                            <?php else: ?>
                                <button class="delete-btn" data-id="<?php echo $item['id_articulo']; ?>" title="Dar de baja">
                                    <span class="material-icons">delete</span> Eliminar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if(empty($todosLosArticulos)): ?>
                    <div style="padding: 20px; text-align: center; color: #777;">No hay registros.</div>
                <?php endif; ?>
            </div>
            <?php 
                $dashboardLink = ($_SESSION['rol'] === 'admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
            ?>

            <a href="<?php echo $dashboardLink; ?>" class="back-to-dash-btn">
                &larr; Volver al Dashboard
            </a>
        </div> 
                
        <div class="modal-overlay hidden" id="add-item-modal">
            <div class="modal-card">
                <button class="modal-close-btn" id="close-add-modal-btn"><span class="material-icons">close</span></button>
                <h3>Agregar Nuevo Artículo</h3>
                <form id="add-item-form" action="../control/agregar_articulo.php" method="POST">
                    <div class="form-group"><label>Nombre</label><input type="text" name="itemName" required></div>
                    <div class="form-group"><label>Descripción</label><input type="text" name="itemDesc"></div>
                    <div class="form-group"><label>Total</label><input type="number" name="itemTotal" required min="1"></div>
                    <div class="form-group"><label>Categoría</label>
                        <select name="itemCategory" required>
                            <option value="consejeria">Consejería</option><option value="fup">FUP</option>
                        </select>
                    </div>
                    <button type="submit" class="action-btn green modal-submit-btn">Agregar Artículo</button>
                </form>
            </div>
        </div>

        <div class="modal-overlay hidden" id="edit-item-modal">
            <div class="modal-card">
                <button class="modal-close-btn" id="close-edit-modal-btn"><span class="material-icons">close</span></button>
                <h3>Editar Artículo</h3>
                <form id="edit-item-form" action="../control/editar_articulo.php" method="POST">
                    <input type="hidden" id="edit-item-id" name="itemId"> 
                    <div class="form-group"><label>Nombre</label><input type="text" id="edit-item-name" name="itemName" required></div>
                    <div class="form-group"><label>Descripción</label><input type="text" id="edit-item-desc" name="itemDesc"></div>
                    <div class="form-group"><label>Total</label><input type="number" id="edit-item-total" name="itemTotal" required min="0"> </div>
                    <div class="form-group"><label>Disponible</label><input type="number" id="edit-item-available" name="itemAvailable" required min="0"> </div>
                    <div class="form-group"><label>Categoría</label>
                        <select id="edit-item-category" name="itemCategory" required>
                            <option value="consejeria">Consejería</option><option value="fup">FUP</option>
                        </select>
                    </div>
                    <div class="modal-button-row">
                        <button type="button" class="action-btn outline modal-cancel-btn" id="cancel-edit-btn">Cancelar</button>
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
        
        <script src="../js/funcionalidad.js?v=<?php echo time(); ?>" type="text/javascript"></script>
    </body>
</html>