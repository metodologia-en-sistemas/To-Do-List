<?php 
session_start();
include ('../config/database.php');

// Validar sesión al inicio
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../frontend/login.html');
    exit;
}

// Cargar tareas del usuario logueado
$id_usuario = $_SESSION['id_usuario'];

// Cambia esta consulta para traer SOLO las tareas de este usuario:
$sql = "SELECT * FROM tareas WHERE id_usuario = :id_usuario ";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id_usuario' => $id_usuario]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Lista de Tareas</h2>

    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Fecha de creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tareas)): ?>
                <?php foreach ($tareas as $tarea): ?>
                    <tr>
                        <td><?= htmlspecialchars($tarea['id_tarea']) ?></td>
                        <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                        <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                        <td><?= htmlspecialchars($tarea['estado']) ?></td>
                        <td><?= htmlspecialchars($tarea['fecha_limite']) ?></td>
                        <td>
                            <a href="actualizar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="eliminar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No hay tareas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="crear.php" class="btn btn-primary">Agregar nueva Tarea</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
