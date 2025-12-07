<?php
class Conexion {
    private $host = 'localhost';
    private $db = 'control_oficinas';
    private $usuario = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';

    public function conectar() {
        try {
            $connectionString = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($connectionString, $this->usuario, $this->pass, $options);
            return $pdo;
        } catch (PDOException $e) {
            print_r('Error de conexión: ' . $e->getMessage());
            die();
        }
    }
}
?>