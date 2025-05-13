<?php 
require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conexion = $db->connect();

$sql = "SELECT * FROM tareas";
$stmt = $conexion->query($sql);
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
            <?php foreach ($tareas as $tarea): ?>
                <tr>
                    <td><?= htmlspecialchars($tarea['id_tarea']) ?></td>
                    <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                    <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                    <td><?= htmlspecialchars($tarea['categoria']) ?></td>

                    <td><?= htmlspecialchars($tarea['fecha_creacion']) ?></td>
                    <td>
                        <a href="actualizar.php" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="crear.php" class="btn btn-primary">Agregar nueva Tarea</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
