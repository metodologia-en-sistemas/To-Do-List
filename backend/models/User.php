<?php 
require_once '../config/database.php';

class User {
    private $conexion; // Variable donde se guarda la conexión a la base de datos.

    public function __construct()
    {
        $this->conn = (new Database())->connect(); // Obtenemos la conexión PDO     
    }

    // CREAR UN NUEVO USUARIO   
    public function create($data) {
        $sql = "INSERT INTO usuario (nombre, correo, contrasena) VALUES (:nombre, :correo, :contrasena)";
        $stmt = $this->conexion->prepare($sql);

        // Hashea el password con la última actualización disponible en algoritmos de hasheo
        $hashedPassword = password_hash($data['contrasena'], PASSWORD_DEFAULT);

        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':correo', $data['correo']);
        $stmt->bindParam(':contrasena', $hashedPassword);

        if ($stmt->execute()) {
            return $this->conexion->lastInsertId(); // Devuelve el ID del nuevo usuario
        }
        return false;
    }

    // BUSCAR POR CORREO
    public function findByEmail($correo) {
        $sql = "SELECT * FROM usuario WHERE correo = :correo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':correo', $correo);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve array asociativo del usuario
    }

    // Buscar por ID 
    public function findByID($id_usuario) {
        $sql = "SELECT id_usuario, nombre, correo FROM usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
