<?php
session_start();
require_once('../config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_limite = $_POST['fecha_limite'];
    $usuarios_asignados = $_POST['usuarios'] ?? [];

    try {
        $pdo->beginTransaction();

        // 1. Crear la tarea grupal
        $stmt = $pdo->prepare("INSERT INTO tareas_comunitarias 
                              (titulo, descripcion, fecha_limite, id_creador) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $fecha_limite, $_SESSION['id_usuario']]);
        $tarea_id = $pdo->lastInsertId();

        // 2. Asignar al creador (automáticamente)
        $stmt = $pdo->prepare("INSERT INTO tareas_asignadas 
                              (id_tarea_comunitaria, id_usuario) 
                              VALUES (?, ?)");
        $stmt->execute([$tarea_id, $_SESSION['id_usuario']]);

        // 3. Asignar a los usuarios seleccionados
        if (!empty($usuarios_asignados)) {
            foreach ($usuarios_asignados as $usuario_id) {
                $stmt->execute([$tarea_id, $usuario_id]);
            }
        }

        $pdo->commit();
        $_SESSION['mensaje_exito'] = "Tarea grupal creada exitosamente";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['mensaje_error'] = "Error al crear tarea grupal: " . $e->getMessage();
    }

    header("Location: ../../frontend/comunidad.php");
    exit;
}
?>