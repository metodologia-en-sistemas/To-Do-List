<?php
require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conexion = $db->connect();
$id_tarea = null;
if (isset($_GET['id_tarea'])) {
    $id_tarea = $_GET['id_tarea'];
    $sql = "SELECT * FROM tareas WHERE id_tarea = :id_tarea";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id_tarea' => $id_tarea]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tarea = $_POST['id_tarea'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE tareas SET titulo = :titulo, descripcion = :descripcion WHERE id_tarea = :id_tarea";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['titulo' => $titulo, 'descripcion' => $descripcion, 'id_tarea' => $id_tarea]);

    header('Location: mostrar.php');
    exit; // Asegurarse de detener la ejecución del script después de la redirección
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Editar Tarea</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Editar Tarea</h2>

    <?php if ($id_tarea): ?>
        <form method="POST" action="actualizar.php" class="card p-4 shadow-sm">
            <input type="hidden" name="id_tarea" value="<?= $id_tarea['id_tarea'] ?>">

            <div class="mb-3">
                <label for="tiulo" class="form-label">titulo</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo['titulo']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">descripcion</label>
                <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?= htmlspecialchars($descripcion['descripcion']) ?>" required>
            </div>

            <div class="text-center">
                <button href="mostrar.php" type="submit" class="btn btn-primary">Actualizar tarea</button>
                <a href="mostrar.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger text-center">Tarea no encontrada.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
