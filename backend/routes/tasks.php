<?php
// Importa el controlador de tareas
require_once '../controller/Task.php';

// Crea una instancia del controlador de tareas
$taskController = new TaskController();

// Verifica el tipo de solicitud HTTP
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Rutas para el CRUD de tareas
switch ($requestMethod) {
    case 'POST':  // Crear una nueva tarea
        if (isset($_POST['title']) && isset($_POST['description'])) {
            // Llama al método de crear tarea
            $response = $taskController->create($_POST);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        }
        break;

    case 'GET':  // Obtener todas las tareas del usuario o una tarea específica
        if (isset($_GET['id'])) {
            // Obtener tarea por ID
            $response = $taskController->getById($_GET['id']);
            echo json_encode($response);
        } else {
            // Obtener todas las tareas del usuario autenticado
            $response = $taskController->getAll();
            echo json_encode($response);
        }
        break;

    case 'PUT':  // Actualizar una tarea
        // Obtiene los datos de la solicitud PUT (requiere raw POST data)
        parse_str(file_get_contents("php://input"), $putData);
        
        if (isset($putData['id']) && isset($putData['title'])) {
            // Actualiza la tarea con los datos recibidos
            $response = $taskController->update($putData['id'], $putData);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Faltan datos']);
        }
        break;

    case 'DELETE':  // Eliminar una tarea
        if (isset($_GET['id'])) {
            // Elimina la tarea por su ID
            $response = $taskController->delete($_GET['id']);
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de tarea no proporcionado']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Método HTTP no permitido']);
        break;
}