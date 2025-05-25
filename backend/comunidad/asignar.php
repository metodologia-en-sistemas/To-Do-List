<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_tarea'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

try {
    $idTarea = $_POST['id_tarea'];
    $idUsuario = $_SESSION['id_usuario'];

    // Verificar si la tarea comunitaria ya fue completada por alguien
    $query = "SELECT COUNT(*) FROM tareas_asignadas 
              WHERE id_tarea_comunitaria = ? AND completada = 1";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$idTarea]);
    $yaCompletada = $stmt->fetchColumn();

    if ($yaCompletada > 0) {
        $_SESSION['error'] = "Esta tarea ya está completada.";
        header("Location: ../../frontend/comunidad.php");
        exit;
    }

    // Verificar si el usuario ya está asignado
    $query = "SELECT COUNT(*) FROM tareas_asignadas 
              WHERE id_tarea_comunitaria = ? AND id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$idTarea, $idUsuario]);
    $asignado = $stmt->fetchColumn();

    if ($asignado == 0) {
        // Asignar la tarea
        $query = "INSERT INTO tareas_asignadas 
                  (id_tarea_comunitaria, id_usuario) 
                  VALUES (?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$idTarea, $idUsuario]);
        $_SESSION['exito'] = "¡Tarea asignada correctamente!";
    }

    // Obtener información de la tarea para notificación
    $query = "SELECT tc.titulo, tc.id_creador 
              FROM tareas_comunitarias tc
              WHERE tc.id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$idTarea]);
    $tarea_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Notificar al creador si el usuario no es el mismo
    if ($tarea_info && $tarea_info['id_creador'] != $idUsuario) {
        $query = "SELECT nombre FROM usuarios WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $mensaje = $usuario['nombre']." se ha asignado a tu tarea: ".$tarea_info['titulo'];

        $query = "INSERT INTO notificaciones (id_usuario, mensaje) 
                  VALUES (?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$tarea_info['id_creador'], $mensaje]);
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Error al asignar tarea: ".$e->getMessage();
}

header("Location: ../../frontend/comunidad.php");
exit;
?>
