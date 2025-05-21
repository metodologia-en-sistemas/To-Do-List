<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_tarea'])) {
    header('Location: ../../login.php');
    exit;
}

try {
    // 1. Marcar tarea como completada por el usuario
    $query = "UPDATE tareas_asignadas 
             SET completada = 1, completada_en = NOW() 
             WHERE id_tarea_comunitaria = ? AND id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_POST['id_tarea'], $_SESSION['id_usuario']]);

    // 2. Verificar si todos han completado la tarea
    $query = "SELECT COUNT(*) as pendientes 
             FROM tareas_asignadas 
             WHERE id_tarea_comunitaria = ? AND completada = 0";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_POST['id_tarea']]);
    $pendientes = $stmt->fetchColumn();

    // 3. Obtener información de la tarea (usando join correcto)
    $query = "SELECT tc.titulo, tc.id_creador, u.nombre 
             FROM tareas_comunitarias tc
             JOIN usuarios u ON tc.id_creador = u.id_usuario
             WHERE tc.id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_POST['id_tarea']]);
    $tarea_info = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. Si no hay pendientes, marcar como completada globalmente

 

    // 5. Crear notificación para el creador si no es el mismo que completó
    if ($tarea_info && $tarea_info['id_creador'] != $_SESSION['id_usuario']) {
        $mensaje = $_SESSION['nombre'] . " ha completado la tarea: " . $tarea_info['titulo'];

        $query = "INSERT INTO notificaciones 
                 (id_usuario, mensaje) 
                 VALUES (?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->execute([$tarea_info['id_creador'], $mensaje]);
    }

    $_SESSION['exito'] = "¡Tarea completada con éxito!";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al completar la tarea: " . $e->getMessage();
}

header("Location: ../../frontend/comunidad.php");
exit;
?>
