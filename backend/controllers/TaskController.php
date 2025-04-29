<?php
// Importa el modelo Task
require_once '../models/Task.php';
require_once '../config/database.php';
// Inicia la sesión para acceder al usuario autenticado
session_start();

// Controlador de tareas: orquesta la lógica entre el modelo y la vista (frontend o API)
class TaskController {

    // Crea una nueva tarea para el usuario autenticado
    public function create($data) {
        if (!isset($_SESSION['id_usuario'])) {
            return ['success' => false, 'message' => 'No autenticado'];
        }

        $task = new Task();
        $data['id_usuario'] = $_SESSION['id_usuario']; // Asocia la tarea al usuario actual
        $data['estado'] = $data['estado'] ?? 'pendiente'; // Valor por defecto

        $result = $task->create($data);
        if ($result) {
            return ['success' => true, 'message' => 'Tarea creada', 'id' => $result];
        } else {
            return ['success' => false, 'message' => 'Error al crear tarea'];
        }
    }

    // Obtiene todas las tareas del usuario autenticado
    public function getAll() {
        if (!isset($_SESSION['id_usuario'])) {
            return [];
        }

        $task = new Task();
        return $task->getAllByUser($_SESSION['id_usuario']);
    }

    // Obtiene los detalles de una tarea por su ID
    public function getById($id) {
        $task = new Task();
        return $task->getById($id);
    }

    // Actualiza una tarea existente
    public function update($id, $data) {
        $task = new Task();
        $updated = $task->update($id, $data);
        if ($updated) {
            return ['success' => true, 'message' => 'Tarea actualizada'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar tarea'];
        }
    }

    // Elimina una tarea
    public function delete($id) {
        $task = new Task();
        $deleted = $task->delete($id);
        if ($deleted) {
            return ['success' => true, 'message' => 'Tarea eliminada'];
        } else {
            return ['success' => false, 'message' => 'Error al eliminar tarea'];
        }
    }
}
?>