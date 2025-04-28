<?php 
require_once '../config/database.php';

class User {
    private $conexion;//Variable donde se guarda la conexion a la base de datos.

    public function __construct()
    {
        $this->conexion = (new Database())->connect(); // Obtenemos la conexión PDO     
    }
    //CREAR UN NUEVO USUARIO   
    public function create($data){
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $stmt = $this->conexion->prepare($sql);
        //Hashea el password con la ultima actualizacion disponible en algoritmos de hasheo
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            return $this->conexion->lastInsertId(); //Devuelve el ID del nuevo usuario
        }
        return false;
    }
    //BUSCAR POR EMAIL
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve array asociativo del usuario
    }
    //Buscar por ID 
    public function findByID($id) {
        $sql = "SELECT id, nombre, email FROM usuarios WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>