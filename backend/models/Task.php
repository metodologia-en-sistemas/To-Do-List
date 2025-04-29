<?php
// Importa la clase Database para manejar la conexión PDO
require_once '../config/database.php';

// Modelo de Tarea que contiene toda la lógica de acceso a datos
class Task {
    private $conexion; // Variable donde se guarda la conexión a la base de datos

    public function __construct()
    {
        // Instancia la conexión a la base de datos
        $this->conexion = (new Database())->connect();
    }

    // Crea una nueva tarea en la base de datos
    public function create($data) {
        $sql = "INSERT INTO tareas (id_usuario, titulo, descripcion, estado, fecha_creacion)
                VALUES (:id_usuario, :titulo, :descripcion, :estado, :fecha_creacion)";
        $stmt = $this->conexion->prepare($sql);

        // Asignación de valores a los parámetros SQL
        $stmt->bindParam(':id_usuario', $data['id_usuario']);
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':estado', $data['estado']);
        $stmt->bindParam(':fecha_creacion', $data['fecha_creacion']);

        // Ejecuta y devuelve el ID de la tarea creada o false si falla
        if ($stmt->execute()) {
            return $this->conexion->lastInsertId();
        }
        return false;
    }

    // Devuelve todas las tareas de un usuario en orden por fecha
    public function getAllByUser($userId) {
        $sql = "SELECT * FROM tareas WHERE id_usuario = :id_usuario ORDER BY estado ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve una sola tarea por su ID
    public function getById($id) {
        $sql = "SELECT * FROM tareas WHERE id_tarea = :id_tarea";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza una tarea existente por su ID
    public function update($id, $data) {
        $sql = "UPDATE tareas SET titulo = :titulo, descripcion = :descripcion, estado = :estado, fecha_creacion = :fecha_creacion
                WHERE id_tarea = :id_tarea";
        $stmt = $this->conexion->prepare($sql);

        // Asignación de nuevos valores
        $stmt->bindParam(':titulo', $data['titulo']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':estado', $data['estado']);
        $stmt->bindParam(':fecha_creacion', $data['fecha_creacion']);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Elimina una tarea por su ID
    public function delete($id) {
        $sql = "DELETE FROM tareas WHERE id_tarea = :id_tarea";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tarea', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>