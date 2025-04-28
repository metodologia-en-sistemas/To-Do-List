<?php
// Importa el controlador de tareas
require_once '../controller/TaskController.php';
session_start();

// Crea una instancia del controlador de tareas
$taskController = new TaskController();

// Verifica el tipo de solicitud HTTP
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Rutas para el CRUD de tareas
switch ($requestMethod) {
    case 'POST':  // Crear una nueva tarea
        if (isset($_POST['titulo']) && isset($_POST['descripcion'])) {
            // Llama al método de crear tarea
            $response = $taskController->create($_POST);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        }
        break;

    case 'GET':  // Obtener todas las tareas o una tarea específica
        if (isset($_GET['id_tarea'])) {
            // Obtener tarea por ID
            $response = $taskController->getById($_GET['id_tarea']);
            echo json_encode($response);
        } else {
            // Obtener todas las tareas
            $response = $taskController->getAll();
            echo json_encode($response);
        }
        break;

    case 'PUT':  // Actualizar una tarea
        // Obtener datos crudos del PUT
        parse_str(file_get_contents("php://input"), $putData);

        if (isset($putData['id_tarea']) && isset($putData['titulo'])) {
            $response = $taskController->update($putData['id_tarea'], $putData);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan datos para actualizar']);
        }
        break;

    case 'DELETE':  // Eliminar una tarea
        if (isset($_GET['id_tarea'])) {
            $response = $taskController->delete($_GET['id_tarea']);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de tarea no proporcionado']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Método HTTP no permitido']);
        break;
}
?>
