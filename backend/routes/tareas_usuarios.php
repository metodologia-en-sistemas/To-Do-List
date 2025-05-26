<?php

session_start();
include('../config/database.php');

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Traer solo los campos necesarios para el calendario
$sql = "SELECT titulo, fecha_limite FROM tareas WHERE id_usuario = :id_usuario";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id_usuario' => $id_usuario]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($tareas);