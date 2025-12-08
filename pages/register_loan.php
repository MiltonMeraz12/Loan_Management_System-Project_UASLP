<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../bd/cad.php';
$consejeria = CAD::getInventario('consejeria');
$fup = CAD::getInventario('fup');
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrar Préstamo - Administrador</title> 
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../css/global_styles.css" rel="stylesheet" type="text/css"/>
    </head>
    
    <body>
        <?php include '../includes/header.php'; ?>

        <div class="register-loan-container">
            <div class="loan-form-card">
                <h2 class="form-title">Información del Préstamo</h2>
                
                <?php if(isset($_GET['error'])): ?>
                    <p style="color: red; font-weight: bold;">Error al registrar. Verifica los datos.</p>
                <?php endif; ?>

                <form id="register-loan-form" action="../control/registrar_prestamo.php" method="POST">
                    
                    <div class="form-group">
                        <label>Categoría</label>
                        <select id="loan-category" name="category" required>
                            <option value="consejeria">Consejería</option>
                            <option value="fup">FUP</option>
                        </select>
                    </div>

                    <hr class="form-divider"> 
                    <h4>Información de Identificación</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tipo de Identificación</label>
                            <select name="idType" required>
                                <option value="universidad">Credencial Universitaria</option>
                                <option value="ine">INE</option>
                                <option value="otra">Otra</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Clave</label>
                            <input type="text" id="student-key" name="studentKey" placeholder="123456" maxlength="6" required> 
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Facultad de Procedencia</label>
                        <select name="studentFaculty" required>
                            <option value="Ingeniería" selected>Ingeniería</option>
                            <option value="Ciencias Químicas">Ciencias Químicas</option>
                            <option value="Hábitat">Hábitat</option>
                            <option value="Medicina">Medicina</option>
                            <option value="Derecho">Derecho</option>
                            <option value="Contaduría y Administración">Contaduría y Administración (FCA)</option>
                            <option value="Economía">Economía</option>
                            <option value="Enfermería y Nutrición">Enfermería y Nutrición</option>
                            <option value="Estomatología">Estomatología</option>
                            <option value="Ciencias">Ciencias</option>
                            <option value="Psicología">Psicología</option>
                            <option value="Ciencias de la Información">Ciencias de la Información</option>
                            <option value="Ciencias Sociales y Humanidades">FCSyH</option>
                            <option value="Agronomía y Veterinaria">Agronomía y Veterinaria</option>
                            <option value="Otra">Otra / Externa</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" id="student-name" name="studentName" required>
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" id="student-phone" name="studentPhone" required> 
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="proxy-requester-checkbox" name="isProxy">
                        <label for="proxy-requester-checkbox">Préstamo solicitado por otra persona</label>
                    </div>
                    <div class="proxy-requester-details hidden" id="proxy-details-section">
                        <hr class="form-divider">
                        <h4>Información del Solicitante</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" id="proxy-name" name="proxyName">
                            </div>
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="tel" id="proxy-phone" name="proxyPhone">
                            </div>
                        </div>
                    </div>

                    <hr class="form-divider"> 
                    <h4>Detalles del Préstamo</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Artículo a Prestar</label>
                            <select id="loan-item" name="itemId" required>
                                <option value="" disabled selected>Selecciona un artículo</option>
                                
                                <?php foreach($consejeria as $item): ?>
                                    <?php if($item['cantidad_disponible'] > 0): ?>
                                        <option value="<?php echo $item['id_articulo']; ?>" data-category="consejeria">
                                            <?php echo $item['nombre']; ?> (Disp: <?php echo $item['cantidad_disponible']; ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php foreach($fup as $item): ?>
                                    <?php if($item['cantidad_disponible'] > 0): ?>
                                        <option value="<?php echo $item['id_articulo']; ?>" data-category="fup">
                                            <?php echo $item['nombre']; ?> (Disp: <?php echo $item['cantidad_disponible']; ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Registrado por:</label> 
                            <input type="text" value="<?php echo $_SESSION['nombre']; ?>" disabled> 
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Comentarios</label>
                        <textarea name="comments" rows="3"></textarea>
                    </div>

                    <button type="submit" class="action-btn green submit-loan-btn">Registrar Préstamo</button>
                </form>
            </div>

            <div class="available-items-card">
                <div class="available-header">
                    <h2 class="items-title" id="available-items-title">Artículos Disponibles</h2>
                </div>
                
                <div class="available-items-list" id="items-list-consejeria">
                    <?php foreach($consejeria as $item): ?>
                    <div class="available-item">
                        <span class="item-name"><?php echo $item['nombre']; ?></span>
                        <span class="item-quantity">Cant: <?php echo $item['cantidad_disponible']; ?></span>
                        <span class="item-availability <?php echo ($item['cantidad_disponible']>0)?'available':'unavailable'; ?>">
                            <?php echo ($item['cantidad_disponible']>0)?'Disp.':'Agotado'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="available-items-list hidden" id="items-list-fup">
                    <?php foreach($fup as $item): ?>
                    <div class="available-item">
                        <span class="item-name"><?php echo $item['nombre']; ?></span>
                        <span class="item-quantity">Cant: <?php echo $item['cantidad_disponible']; ?></span>
                        <span class="item-availability <?php echo ($item['cantidad_disponible']>0)?'available':'unavailable'; ?>">
                            <?php echo ($item['cantidad_disponible']>0)?'Disp.':'Agotado'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php 
                    $dashboardLink = ($_SESSION['rol'] === 'admin') ? 'admin_dashboard.php' : 'member_dashboard.php';
                ?>

                <a href="<?php echo $dashboardLink; ?>" class="back-to-dash-btn">
                    &larr; Volver al Dashboard
                </a>
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