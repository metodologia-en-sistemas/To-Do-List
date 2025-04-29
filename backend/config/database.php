<?php

class Database {
    private $host = '127.0.0.1';
    private $dbname = 'gestion_tareas';
    private $user = 'root';  
    private $password = 'Cristian47'; 
    private $conexion = null; // Guarda la conexión

    public function connect() {
        if ($this->conexion === null) {
            try {
                $this->conexion = new PDO("mysql:host={$this->host};dbname={$this->dbname}",
                    $this->user, 
                    $this->password
                );
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "<script>alert('Conexion Fallida'); window.location='../index.php';</script>";
                exit; // Salir después del error
            }
        }
        return $this->conexion;
    }
}
?>