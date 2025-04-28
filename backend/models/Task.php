<?php
// Importa la clase Database para manejar la conexión PDO
require_once './config/database.php';

// Modelo de Tarea que contiene toda la lógica de acceso a datos
class Task {
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    // Crea una nueva tarea en la base de datos
    public function create($data) {
        $sql = "INSERT INTO tarea (id_usuario, titulo, descripcion, categoria, estado)
                VALUES (:id_usuario, :titulo, :descripcion, :categoria, :estado)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id_usuario', $data['id_usuario']);
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':categoria', $data['id_categoria']);
        $stmt->bindParam(':estado', $data['id_estado']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Devuelve todas las tareas asignadas a un usuario
    public function getAllByUser($id_usuario) {
        $sql = "SELECT * FROM tarea WHERE id_usuario = :id_usuario ORDER BY id_tarea ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve una sola tarea por su ID
    public function getById($id) {
        $sql = "SELECT * FROM tarea WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza una tarea existente por su ID
    public function update($id, $data) {
        $sql = "UPDATE tarea 
                SET titulo = :titulo, 
                    descripcion = :descripcion, 
                    categoria = :categoria, 
                    estado = :estado
                WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':categoria', $data['id_categoria']);
        $stmt->bindParam(':estado', $data['id_estado']);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Elimina una tarea por su ID
    public function delete($id) {
        $sql = "DELETE FROM tarea WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
