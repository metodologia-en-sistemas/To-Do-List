<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_tarea'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

try {
    // Verificar si la tarea ya está completada
    $query = "SELECT completada_global FROM tareas_comunitarias WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_POST['id_tarea']]);
    $estado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($estado && $estado['completada_global']) {
        $_SESSION['error'] = "Esta tarea ya está completada.";
        header("Location: ../../frontend/comunidad.php");
        exit;
    }

    // Verificar si ya está asignado
    $query = "SELECT COUNT(*) FROM tareas_asignadas 
              WHERE id_tarea_comunitaria = ? AND id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_POST['id_tarea'], $_SESSION['id_usuario']]);

    if ($stmt->fetchColumn() == 0) {
        // Asignar la tarea
        $query = "INSERT INTO tareas_asignadas 
                 (id_tarea_comunitaria, id_usuario) 
                 VALUES (?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$_POST['id_tarea'], $_SESSION['id_usuario']]);

        $_SESSION['exito'] = "¡Tarea asignada correctamente!";
    } else {
        $_SESSION['error'] = "Ya estabas asignado a esta tarea";
    }

    // Obtener información de la tarea para notificación
    $query = "SELECT tc.titulo, tc.id_creador 
             FROM tareas_comunitarias tc
             WHERE tc.id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_POST['id_tarea']]);
    $tarea_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // Notificar al creador (si no es el mismo usuario)
    if ($tarea_info && $tarea_info['id_creador'] != $_SESSION['id_usuario']) {
        $query = "SELECT nombre FROM usuarios WHERE id_usuario = ?";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$_SESSION['id_usuario']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        $mensaje = $usuario['nombre']." se ha asignado a tu tarea: ".$tarea_info['titulo'];

        $query = "INSERT INTO notificaciones 
                 (id_usuario, mensaje) 
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
