<?php
session_start();
include('../config/database.php'); // ← Ruta corregida

if (isset($_GET['id_tarea'])) {
    $id_tarea = $_GET['id_tarea'];
    // Marcar la tarea como completada
    $sql = "UPDATE tareas SET completado = 1 WHERE id_tarea = :id_tarea AND id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'id_tarea' => $id_tarea,
        'id_usuario' => $_SESSION['id_usuario']
    ]);
}

header('Location: ../../frontend/tareas.php');
exit;