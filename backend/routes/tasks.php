<?php
session_start();
require_once '../controllers/TaskController.php'; // Asegúrate de que la ruta sea correcta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esto es solo para pruebas si no hiciste login
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['id_usuario'] = 1; // Usuario de prueba (ID 1)
}


header('Content-Type: application/json');

$taskController = new TaskController();
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'POST':
        // POST normal o JSON
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST; // Soporta también formulario clásico
        }

        if (isset($data['titulo'])) {
            $response = $taskController->create($data);
        } else {
            $response = ['success' => false, 'message' => 'Faltan datos para crear la tarea'];
        }
        echo json_encode($response);
        break;

    case 'GET':
        if (isset($_GET['id_tarea'])) {
            $response = $taskController->getById($_GET['id_tarea']);
        } else {
            $response = $taskController->getAll();
        }
        echo json_encode($response);
        break;

    case 'PUT':
        $putData = [];
        parse_str(file_get_contents("php://input"), $putData);

        if (isset($putData['id_tarea']) && isset($putData['titulo'])) {
            $response = $taskController->update($putData['id_tarea'], $putData);
        } else {
            $response = ['success' => false, 'message' => 'Faltan datos para actualizar la tarea'];
        }
        echo json_encode($response);
        break;

    case 'DELETE':
        if (isset($_GET['id_tarea'])) {
            $response = $taskController->delete($_GET['id_tarea']);
        } else {
            $response = ['success' => false, 'message' => 'ID de tarea no proporcionado'];
        }
        echo json_encode($response);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Método HTTP no permitido']);
        break;
}
