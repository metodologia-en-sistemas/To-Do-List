<?php
session_start();
include('../config/database.php');

if (isset($_POST['id_tarea'])) {
    $id_tarea = $_POST['id_tarea'];
    $id_usuario = $_SESSION['id_usuario'];

    // Solo permite eliminar si es el creador
    $stmt = $conexion->prepare("DELETE FROM tareas_comunitarias WHERE id = :id_tarea AND id_creador = :id_usuario");
    $stmt->execute([
        'id_tarea' => $id_tarea,
        'id_usuario' => $id_usuario
    ]);
}

header('Location: ../../frontend/comunidad.php');
exit;