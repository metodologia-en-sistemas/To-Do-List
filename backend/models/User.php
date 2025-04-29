<?php
require_once '../config/database.php';

class User {
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Database())->connect();
    }

    // Crear un nuevo usuario
    public function create($data)
    {
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $this->conexion->prepare($sql);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            return $this->conexion->lastInsertId(); // Devuelve el ID del nuevo usuario
        }
        return false;
    }

    // Buscar un usuario por email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve el usuario si existe, o false si no
    }

    // Buscar usuario por ID (si se necesita en el futuro)
    public function findByID($id)
    {
        $sql = "SELECT id, nombre, email FROM usuarios WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
