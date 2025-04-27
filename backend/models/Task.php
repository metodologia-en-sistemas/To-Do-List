<?php
// Importa la clase Database para manejar la conexión PDO
require_once '../config/database.php';

// Modelo de Tarea que contiene toda la lógica de acceso a datos
class Task {
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
    }

    // Crea una nueva tarea en la base de datos
    public function create($data) {
        $sql = "INSERT INTO tareas (título, descripción, fecha_límite, id_categoría, id_estado, prioridad, progreso)
                VALUES (:titulo, :descripcion, :fecha_limite, :id_categoria, :id_estado, :prioridad, :progreso)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':titulo', $data['título']);
        $stmt->bindParam(':descripcion', $data['descripción']);
        $stmt->bindParam(':fecha_limite', $data['fecha_límite']);
        $stmt->bindParam(':id_categoria', $data['id_categoría']);
        $stmt->bindParam(':id_estado', $data['id_estado']);
        $stmt->bindParam(':prioridad', $data['prioridad']);
        $stmt->bindParam(':progreso', $data['progreso']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Devuelve todas las tareas asignadas a un usuario
    public function getAllByUser($id_usuario) {
        $sql = "SELECT t.*
                FROM tareas t
                INNER JOIN asignaciones a ON t.id_tarea = a.id_tarea
                WHERE a.id_usuario = :id_usuario
                ORDER BY t.fecha_límite ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve una sola tarea por su ID
    public function getById($id) {
        $sql = "SELECT * FROM tareas WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza una tarea existente por su ID
    public function update($id, $data) {
        $sql = "UPDATE tareas 
                SET título = :titulo, 
                    descripción = :descripcion, 
                    fecha_límite = :fecha_limite, 
                    id_categoría = :id_categoria,
                    id_estado = :id_estado,
                    prioridad = :prioridad,
                    progreso = :progreso
                WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':titulo', $data['título']);
        $stmt->bindParam(':descripcion', $data['descripción']);
        $stmt->bindParam(':fecha_limite', $data['fecha_límite']);
        $stmt->bindParam(':id_categoria', $data['id_categoría']);
        $stmt->bindParam(':id_estado', $data['id_estado']);
        $stmt->bindParam(':prioridad', $data['prioridad']);
        $stmt->bindParam(':progreso', $data['progreso']);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Elimina una tarea por su ID
    public function delete($id) {
        $sql = "DELETE FROM tareas WHERE id_tarea = :id_tarea";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
