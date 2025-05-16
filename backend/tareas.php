<?php
// tareas.php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = isset($_POST['tarea_id']) ? (int)$_POST['tarea_id'] : null;
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getPDO();
        
        switch ($action) {
            case 'Crear':
                header("Location: new_task.php");
                exit;
                
            case 'Pendiente':
                updateTaskStatus($pdo, $task_id, 'Pendiente');
                $_SESSION['message'] = "Tarea marcada como Pendiente";
                break;
                
            case 'Completado':
                updateTaskStatus($pdo, $task_id, 'Completado');
                $_SESSION['message'] = "¡Tarea completada!";
                break;
                
            case 'Listo':
                updateTaskStatus($pdo, $task_id, 'Listo');
                $_SESSION['message'] = "Tarea marcada como Lista para revisión";
                break;
                
            case 'Eliminar':
                if (confirmarEliminacion()) {
                    deleteTask($pdo, $task_id);
                    $_SESSION['message'] = "Tarea eliminada correctamente";
                }
                break;
                
            case 'Editar':
                header("Location: edit_task.php?id=$task_id");
                exit;
                
            default:
                $_SESSION['error'] = "Acción no reconocida";
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }
    
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}

/**
 * Actualiza el estado de una tarea en la base de datos
 */
function updateTaskStatus($pdo, $id, $status) {
    $stmt = $pdo->prepare("UPDATE tareas SET estado = ?, fecha_actualizacion = NOW() WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("No se encontró la tarea con ID $id");
    }
}

/**
 * Elimina una tarea de la base de datos
 */
function deleteTask($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("No se encontró la tarea con ID $id para eliminar");
    }
}

/**
 * Confirmación antes de eliminar (puedes implementar esto con JavaScript también)
 */
function confirmarEliminacion() {
    if (!isset($_POST['confirmar']) && empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        $_SESSION['confirmacion'] = [
            'action' => 'Eliminar',
            'tarea_id' => $_POST['tarea_id']
        ];
        return false;
    }
    return true;
}
?>