<?php
// Importa el modelo Task
require_once '../models/Task.php';
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

        // Asignamos los datos de la tarea
        $data['titulo'] = $data['titulo'] ?? '';
        $data['descripcion'] = $data['descripcion'] ?? null;
        $data['id_categoria'] = $data['id_categoria'] ?? null;
        $data['id_estado'] = $data['id_estado'] ?? null;
        
        // Aquí asumimos que el id_usuario es el usuario autenticado
        $data['id_usuario'] = $_SESSION['id_usuario'];

        $result = $task->create($data);

        if ($result) {
            return ['success' => true, 'message' => 'Tarea creada', 'id' => $result];
        } else {
            return ['success' => false, 'message' => 'Error al crear tarea'];
        }
    }

    // Obtiene todas las tareas asignadas al usuario autenticado
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

        // Verifica y prepara campos opcionales
        $data['titulo'] = $data['titulo'] ?? '';
        $data['descripcion'] = $data['descripcion'] ?? null;
        $data['id_categoria'] = $data['id_categoria'] ?? null;
        $data['id_estado'] = $data['id_estado'] ?? null;

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
