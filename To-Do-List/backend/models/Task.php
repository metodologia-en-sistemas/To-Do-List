<?php
// Importa la clase Database para manejar la conexión PDO
require_once '../config/database.php';

// Modelo de Tarea que contiene toda la lógica de acceso a datos
class Task {
    private $conn; // Variable donde se guarda la conexión a la base de datos

    public function __construct()
    {
        // Instancia la conexión a la base de datos
        $this->conn = (new Database())->connect();
    }

    // Crea una nueva tarea en la base de datos
    public function create($data) {
        $sql = "INSERT INTO tasks (user_id, title, description, due_date, status)
                VALUES (:user_id, :title, :description, :due_date, :status)";
        $stmt = $this->conn->prepare($sql);

        // Asignación de valores a los parámetros SQL
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':status', $data['status']);

        // Ejecuta y devuelve el ID de la tarea creada o false si falla
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Devuelve todas las tareas de un usuario en orden por fecha
    public function getAllByUser($userId) {
        $sql = "SELECT * FROM tasks WHERE user_id = :user_id ORDER BY due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve una sola tarea por su ID
    public function getById($id) {
        $sql = "SELECT * FROM tasks WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza una tarea existente por su ID
    public function update($id, $data) {
        $sql = "UPDATE tasks SET title = :title, description = :description, due_date = :due_date, status = :status
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        // Asignación de nuevos valores
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Elimina una tarea por su ID
    public function delete($id) {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>