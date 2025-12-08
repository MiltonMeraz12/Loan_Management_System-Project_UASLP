<?php
require_once 'conexion.php';

class CAD {
    
    // --- 1. LOGIN: Verificar credenciales ---
    static public function verificaUsuario($correo, $password) {
        $con = new Conexion();
        $conexion = $con->conectar();

        // Buscamos usuario activo por correo
        $sql = "SELECT * FROM usuarios WHERE correo = :correo AND estado = 'activo'";
        $query = $conexion->prepare($sql);
        $query->bindParam(':correo', $correo);
        $query->execute();

        // Si existe el correo, verificamos la contraseña encriptada
        if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                return $row; // Retorna ID, Nombre, Rol, etc.
            }
        }
        return false; // Credenciales incorrectas
    }

    // --- 2. GESTIÓN: Registrar nuevo usuario (Admin/Miembro) ---
    static public function agregaUsuario($nombre, $correo, $password, $rol) {
        $con = new Conexion();
        $conexion = $con->conectar();

        // Encriptamos la contraseña antes de guardarla
        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre_completo, correo, password, rol) 
                VALUES (:nom, :cor, :pass, :rol)";
        
        $query = $conexion->prepare($sql);
        $query->bindParam(':nom', $nombre);
        $query->bindParam(':cor', $correo);
        $query->bindParam(':pass', $passHash);
        $query->bindParam(':rol', $rol);

        return $query->execute();
    }

    // --- 3. CAMBIAR CONTRASEÑA ---
    static public function cambiarContrasena($idUsuario, $nuevaPassword) {
        $con = new Conexion();
        $conexion = $con->conectar();

        $passHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET password = :pass WHERE id_usuario = :id";
        $query = $conexion->prepare($sql);
        $query->bindParam(':pass', $passHash);
        $query->bindParam(':id', $idUsuario);

        return $query->execute();
    }

    // --- 4. REGISTRAR PRÉSTAMO (Con Facultad) ---
    // Nota: Agregamos $facultad a los parámetros recibidos
    static public function registrarPrestamo($idArticulo, $claveEstudiante, $nombreEstudiante, $telefonoEstudiante, $facultad, $tipoId, $idUsuarioPrestamo, $esProxy, $nombreProxy, $telefonoProxy, $comentarios) {
        $con = new Conexion();
        $conexion = $con->conectar();

        try {
            $conexion->beginTransaction();

            // 1. Guardar o Actualizar Estudiante
            // Agregamos la columna 'facultad' al INSERT y al UPDATE
            $sqlEstudiante = "INSERT INTO estudiantes (clave_uaslp, nombre_completo, telefono, facultad) 
                              VALUES (:clave, :nombre, :tel, :fac) 
                              ON DUPLICATE KEY UPDATE nombre_completo = :nom_u, telefono = :tel_u, facultad = :fac_u";
            
            $qEst = $conexion->prepare($sqlEstudiante);
            $qEst->execute([
                ':clave' => $claveEstudiante,
                ':nombre' => $nombreEstudiante,
                ':tel' => $telefonoEstudiante,
                ':fac' => $facultad,          // Nuevo valor para insertar
                ':nom_u' => $nombreEstudiante,
                ':tel_u' => $telefonoEstudiante,
                ':fac_u' => $facultad         // Nuevo valor para actualizar si ya existe
            ]);

            // 2. Insertar el préstamo (Esto no cambia)
            $sqlPrestamo = "INSERT INTO prestamos (id_articulo, clave_estudiante, id_usuario_prestamo, fecha_limite, tipo_identificacion, es_proxy, nombre_proxy, telefono_proxy, comentarios_prestamo) 
                            VALUES (:art, :clave, :usu, DATE_ADD(NOW(), INTERVAL 1 DAY), :tipoId, :proxy, :nomProxy, :telProxy, :com)";
            
            $query = $conexion->prepare($sqlPrestamo);
            $query->execute([
                ':art' => $idArticulo,
                ':clave' => $claveEstudiante,
                ':usu' => $idUsuarioPrestamo,
                ':tipoId' => $tipoId,
                ':proxy' => $esProxy,
                ':nomProxy' => $nombreProxy,
                ':telProxy' => $telefonoProxy,
                ':com' => $comentarios
            ]);

            // 3. Restar Stock
            $sqlUpdate = "UPDATE articulos SET cantidad_disponible = cantidad_disponible - 1 WHERE id_articulo = :art";
            $queryUpdate = $conexion->prepare($sqlUpdate);
            $queryUpdate->bindParam(':art', $idArticulo);
            $queryUpdate->execute();

            $conexion->commit();
            return true;

        } catch (Exception $e) {
            $conexion->rollBack();
            return false;
        }
    }

    // --- 5. OBTENER INVENTARIO (Faltaba esta función) ---
    static public function getInventario($categoria) {
        $con = new Conexion();
        $conexion = $con->conectar();

        // Trae solo los artículos de la categoría solicitada (fup o consejeria) que estén activos
        $sql = "SELECT * FROM articulos WHERE categoria = :cat AND estado = 'activo'";
        $query = $conexion->prepare($sql);
        $query->bindParam(':cat', $categoria);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 6. OBTENER ESTADÍSTICAS (Para el Dashboard) ---
    static public function getEstadisticas() {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        $stats = [];

        // 1. Contar Préstamos Activos
        $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estado = 'activo'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $stats['prestamos_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Contar Préstamos Vencidos (Activos y fecha límite ya pasó)
        $sql = "SELECT COUNT(*) as total FROM prestamos WHERE estado = 'activo' AND fecha_limite < NOW()";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $stats['prestamos_vencidos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Contar Total Artículos
        $sql = "SELECT SUM(cantidad_total) as total FROM articulos WHERE estado = 'activo'";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $stats['total_articulos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $stats;
    }

    // --- 7. DATOS PARA REPORTES (Versión Final) ---
    static public function getReporte($tipo, $valor, $categoria) {
        $con = new Conexion();
        $conexion = $con->conectar();

        $sql = "SELECT p.id_prestamo, p.fecha_prestamo, p.fecha_limite, p.estado,
                       a.nombre as articulo, a.categoria,
                       e.nombre_completo as estudiante
                FROM prestamos p
                JOIN articulos a ON p.id_articulo = a.id_articulo
                JOIN estudiantes e ON p.clave_estudiante = e.clave_uaslp
                WHERE 1=1"; 

        // 1. Filtro de Tiempo
        if ($tipo === 'semanal') {
            // Comparación por Semana ISO (Año-Semana, ej: 2025-W42)
            // %x = Año ISO, %v = Semana ISO
            $sql .= " AND DATE_FORMAT(p.fecha_prestamo, '%x-W%v') = :valor";
        } else {
            // Comparación por Mes (Año-Mes, ej: 2025-10)
            $sql .= " AND DATE_FORMAT(p.fecha_prestamo, '%Y-%m') = :valor";
        }

        // 2. Filtro de Categoría
        if ($categoria !== 'todas') {
            $sql .= " AND a.categoria = :cat";
        }

        $sql .= " ORDER BY p.fecha_prestamo DESC";

        $query = $conexion->prepare($sql);
        $query->bindParam(':valor', $valor);
        if ($categoria !== 'todas') {
            $query->bindParam(':cat', $categoria);
        }
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 8. TRAER PRÉSTAMOS VENCIDOS ---
    static public function getVencidos() {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        $sql = "SELECT p.*, a.nombre as articulo, a.categoria, e.nombre_completo as estudiante, e.telefono, u.nombre_completo as registrado_por
                FROM prestamos p
                JOIN articulos a ON p.id_articulo = a.id_articulo
                JOIN estudiantes e ON p.clave_estudiante = e.clave_uaslp
                JOIN usuarios u ON p.id_usuario_prestamo = u.id_usuario
                WHERE p.estado = 'activo' AND p.fecha_limite < NOW()";
        
        $query = $conexion->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 9. TRAER HISTORIAL COMPLETO ---
    static public function getHistorial() {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        $sql = "SELECT p.*, a.nombre as articulo, a.categoria, e.nombre_completo as estudiante, u.nombre_completo as registrado_por
                FROM prestamos p
                JOIN articulos a ON p.id_articulo = a.id_articulo
                JOIN estudiantes e ON p.clave_estudiante = e.clave_uaslp
                JOIN usuarios u ON p.id_usuario_prestamo = u.id_usuario
                ORDER BY p.fecha_prestamo DESC LIMIT 100"; // Límite para no saturar
        
        $query = $conexion->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 10. TRAER TODOS LOS USUARIOS (Para Gestión) ---
    static public function getUsuarios() {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        $sql = "SELECT * FROM usuarios ORDER BY nombre_completo ASC";
        $query = $conexion->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 11. CAMBIAR ESTADO DE USUARIO ---
    static public function cambiarEstadoUsuario($idUsuario, $nuevoEstado) {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id";
        $query = $conexion->prepare($sql);
        $query->bindParam(':estado', $nuevoEstado);
        $query->bindParam(':id', $idUsuario);
        
        return $query->execute();
    }

    // --- 12. OBTENER PRÉSTAMOS ACTIVOS (La que te falta para el error) ---
    static public function getPrestamosActivos() {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        // Hacemos JOIN para traer el nombre del artículo y del estudiante en lugar de solo IDs
        $sql = "SELECT p.*, 
                       a.nombre as articulo, 
                       e.nombre_completo as estudiante, e.telefono,
                       u.nombre_completo as registrado_por
                FROM prestamos p
                JOIN articulos a ON p.id_articulo = a.id_articulo
                JOIN estudiantes e ON p.clave_estudiante = e.clave_uaslp
                JOIN usuarios u ON p.id_usuario_prestamo = u.id_usuario
                WHERE p.estado = 'activo'
                ORDER BY p.fecha_prestamo ASC";
                
        $query = $conexion->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 13. PROCESAR DEVOLUCIÓN (La necesitarás al dar click en 'Confirmar') ---
    static public function devolverPrestamo($idPrestamo, $idUsuarioDevolucion, $comentarios) {
        $con = new Conexion();
        $conexion = $con->conectar();

        try {
            $conexion->beginTransaction();

            // 1. Obtener ID del artículo para devolverlo al stock
            $sqlGet = "SELECT id_articulo FROM prestamos WHERE id_prestamo = :id";
            $stmt = $conexion->prepare($sqlGet);
            $stmt->execute([':id' => $idPrestamo]);
            $idArticulo = $stmt->fetchColumn();

            // 2. Actualizar el préstamo (Fecha, estado, quién recibió)
            $sqlUpdate = "UPDATE prestamos 
                          SET fecha_devolucion = NOW(), 
                              estado = 'devuelto', 
                              id_usuario_devolucion = :usu, 
                              comentarios_devolucion = :com 
                          WHERE id_prestamo = :id";
            $qUpdate = $conexion->prepare($sqlUpdate);
            $qUpdate->execute([
                ':usu' => $idUsuarioDevolucion,
                ':com' => $comentarios,
                ':id' => $idPrestamo
            ]);

            // 3. Sumar 1 al inventario (Devolver el artículo)
            $sqlStock = "UPDATE articulos SET cantidad_disponible = cantidad_disponible + 1 WHERE id_articulo = :art";
            $qStock = $conexion->prepare($sqlStock);
            $qStock->execute([':art' => $idArticulo]);

            $conexion->commit();
            return true;

        } catch (Exception $e) {
            $conexion->rollBack();
            return false;
        }
    }

    // --- 14. AGREGAR NUEVO ARTÍCULO (CORREGIDO) ---
    static public function agregarArticulo($nombre, $descripcion, $cantidad, $categoria) {
        $con = new Conexion();
        $conexion = $con->conectar();

        // Al crear, la cantidad disponible es igual al total.
        // OJO: Usamos dos marcadores distintos :total y :disp para evitar confusión en PDO
        $sql = "INSERT INTO articulos (nombre, descripcion, cantidad_total, cantidad_disponible, categoria, estado) 
                VALUES (:nom, :desc, :total, :disp, :cat, 'activo')";
        
        $query = $conexion->prepare($sql);
        $query->execute([
            ':nom' => $nombre,
            ':desc' => $descripcion,
            ':total' => $cantidad, // Asignamos la cantidad al total
            ':disp' => $cantidad,  // Y también al disponible
            ':cat' => $categoria
        ]);
        return true;
    }

    // --- 15. EDITAR ARTÍCULO EXISTENTE ---
    static public function editarArticulo($id, $nombre, $descripcion, $total, $disponible, $categoria) {
        $con = new Conexion();
        $conexion = $con->conectar();

        $sql = "UPDATE articulos 
                SET nombre = :nom, 
                    descripcion = :desc, 
                    cantidad_total = :total, 
                    cantidad_disponible = :disp, 
                    categoria = :cat 
                WHERE id_articulo = :id";
        
        $query = $conexion->prepare($sql);
        $query->execute([
            ':nom' => $nombre,
            ':desc' => $descripcion,
            ':total' => $total,
            ':disp' => $disponible,
            ':cat' => $categoria,
            ':id' => $id
        ]);
        return true;
    }

    // --- 16. ELIMINAR ARTÍCULO (Borrado lógico) ---
    static public function eliminarArticulo($id) {
        $con = new Conexion();
        $conexion = $con->conectar();

        // No borramos el registro (DELETE) para no romper el historial de préstamos pasados.
        // Mejor lo marcamos como 'baja' o 'inactivo'.
        $sql = "UPDATE articulos SET estado = 'baja' WHERE id_articulo = :id";
        
        $query = $conexion->prepare($sql);
        $query->execute([':id' => $id]);
        return true;
    }

    // --- 17. EDITAR USUARIO (Faltaba esta) ---
    static public function editarUsuario($id, $nombre, $rol) {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        $sql = "UPDATE usuarios SET nombre_completo = :nom, rol = :rol WHERE id_usuario = :id";
        $query = $conexion->prepare($sql);
        $query->execute([':nom' => $nombre, ':rol' => $rol, ':id' => $id]);
        return true;
    }

    // --- 18. OBTENER INVENTARIO COMPLETO (ACTIVOS Y BAJAS) ---
    // Usado solo en manage_inventory.php
    static public function getInventarioAdmin($categoria) {
        $con = new Conexion();
        $conexion = $con->conectar();

        // Trae TODO sin filtrar por estado 'activo'
        $sql = "SELECT * FROM articulos WHERE categoria = :cat ORDER BY estado ASC, nombre ASC";
        $query = $conexion->prepare($sql);
        $query->bindParam(':cat', $categoria);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 19. REACTIVAR ARTÍCULO ---
    static public function reactivarArticulo($id) {
        $con = new Conexion();
        $conexion = $con->conectar();

        $sql = "UPDATE articulos SET estado = 'activo' WHERE id_articulo = :id";
        
        $query = $conexion->prepare($sql);
        $query->execute([':id' => $id]);
        return true;
    }

    // --- 20. BUSCAR ESTUDIANTE (Para autocompletar) ---
    static public function buscarEstudiante($clave) {
        $con = new Conexion();
        $conexion = $con->conectar();

        $sql = "SELECT * FROM estudiantes WHERE clave_uaslp = :clave";
        $query = $conexion->prepare($sql);
        $query->bindParam(':clave', $clave);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC); // Retorna false si no existe
    }

    // --- 21. OBTENER INFO OFICINA (Index y Admin) ---
    static public function getOficina($clave) {
        $con = new Conexion();
        $conexion = $con->conectar();
        $sql = "SELECT * FROM oficinas WHERE clave = :cve";
        $query = $conexion->prepare($sql);
        $query->execute([':cve' => $clave]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // --- 22. OBTENER SERVICIOS POR OFICINA ---
    static public function getServicios($claveOficina) {
        $con = new Conexion();
        $conexion = $con->conectar();
        $sql = "SELECT * FROM servicios WHERE oficina = :cve";
        $query = $conexion->prepare($sql);
        $query->execute([':cve' => $claveOficina]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 23. ACTUALIZAR TODO (Oficina + Servicios) ---
    // Esta función recibe el estado general y un array con los estados de los servicios
    static public function actualizarConfiguracion($clave, $estadoOficina, $horario, $estadosServicios) {
        $con = new Conexion();
        $conexion = $con->conectar();
        
        try {
            $conexion->beginTransaction();

            // 1. Actualizar Oficina
            $sqlOficina = "UPDATE oficinas SET estado = :est, horario = :hor WHERE clave = :cve";
            $qOf = $conexion->prepare($sqlOficina);
            $qOf->execute([':est' => $estadoOficina, ':hor' => $horario, ':cve' => $clave]);

            // 2. Actualizar cada Servicio
            $sqlServ = "UPDATE servicios SET estado = :st WHERE id_servicio = :id";
            $qServ = $conexion->prepare($sqlServ);

            foreach ($estadosServicios as $id => $estado) {
                $qServ->execute([':st' => $estado, ':id' => $id]);
            }

            $conexion->commit();
            return true;
        } catch (Exception $e) {
            $conexion->rollBack();
            return false;
        }
    }
}
?>