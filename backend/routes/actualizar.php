<?php
include ('../config/database.php');
session_start();

$tarea = null;
if (isset($_GET['id_tarea'])) {
    $id_tarea = $_GET['id_tarea'];
    $sql = "SELECT * FROM tareas WHERE id_tarea = :id_tarea";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id_tarea' => $id_tarea]);
    $tarea = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tarea = $_POST['id_tarea'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE tareas SET titulo = :titulo, descripcion = :descripcion WHERE id_tarea = :id_tarea";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['titulo' => $titulo, 'descripcion' => $descripcion, 'id_tarea' => $id_tarea]);

    header('Location: ../../frontend/tareas.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Editar Tarea</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Editar Tarea</h2>

    <?php if ($tarea): ?>
        <form method="POST" action="actualizar.php" class="card p-4 shadow-sm">
            <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($tarea['id_tarea']) ?>">

            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($tarea['titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?= htmlspecialchars($tarea['descripcion']) ?>" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Actualizar tarea</button>
                <a href="../../frontend/tareas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger text-center">Tarea no encontrada.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
