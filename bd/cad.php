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

    // --- 4. REGISTRAR PRÉSTAMO (Con lógica de Proxy) ---
    static public function registrarPrestamo($idArticulo, $claveEstudiante, $idUsuarioPrestamo, $esProxy, $nombreProxy, $telefonoProxy, $comentarios) {
        $con = new Conexion();
        $conexion = $con->conectar();

        try {
            // A. Iniciamos una TRANSACCIÓN (Para asegurar que se hagan los dos cambios o ninguno)
            $conexion->beginTransaction();

            // 1. Insertar el préstamo en la tabla 'prestamos'
            $sqlPrestamo = "INSERT INTO prestamos (id_articulo, clave_estudiante, id_usuario_prestamo, fecha_limite, es_proxy, nombre_proxy, telefono_proxy, comentarios_prestamo) 
                            VALUES (:art, :clave, :usu, DATE_ADD(NOW(), INTERVAL 1 DAY), :proxy, :nomProxy, :telProxy, :com)";
            
            $query = $conexion->prepare($sqlPrestamo);
            $query->bindParam(':art', $idArticulo);
            $query->bindParam(':clave', $claveEstudiante);
            $query->bindParam(':usu', $idUsuarioPrestamo);
            $query->bindParam(':proxy', $esProxy);
            $query->bindParam(':nomProxy', $nombreProxy); // Si es null, se guarda NULL
            $query->bindParam(':telProxy', $telefonoProxy);
            $query->bindParam(':com', $comentarios);
            $query->execute();

            // 2. Restar 1 a la cantidad disponible del artículo
            $sqlUpdate = "UPDATE articulos SET cantidad_disponible = cantidad_disponible - 1 WHERE id_articulo = :art";
            $queryUpdate = $conexion->prepare($sqlUpdate);
            $queryUpdate->bindParam(':art', $idArticulo);
            $queryUpdate->execute();

            // B. Confirmar cambios
            $conexion->commit();
            return true;

        } catch (Exception $e) {
            // Si algo falla, deshacemos todo
            $conexion->rollBack();
            return false;
        }
    }
}
?>