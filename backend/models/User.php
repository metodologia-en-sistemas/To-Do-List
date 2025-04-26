<?php 
require_once '../config/database.php';

class User {
    private $conn;//Variable donde se guarda la conexion a la base de datos.

    public function __construct()
    {
        $this->conn = (new Database())->connect(); // Obtenemos la conexión PDO     
    }
    //CREAR UN NUEVO USUARIO   
    public function create($data){
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        //Hashea el password con la ultima actualizacion disponible en algoritmos de hasheo
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); //Devuelve el ID del nuevo usuario
        }
        return false;
    }
    //BUSCAR POR EMAIL
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve array asociativo del usuario
    }
    //Buscar por ID 
    public function findByID($id) {
        $sql = "SELECT id, name, email FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>