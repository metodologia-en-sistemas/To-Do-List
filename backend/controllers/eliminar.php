<?php
require_once __DIR__ . '/../config/database.php';

$id_tarea = null; // Inicializamos la variable

// Si se recibe la confirmación de eliminación, procedemos con la eliminación
if (isset($_POST['confirmar_eliminacion']) && isset($_POST['id_tarea'])) {
    $id_tarea = $_POST['id_tarea'];  // Aseguramos que la variable id_tarea esté definida
    $sql = "DELETE FROM tareas WHERE id_tarea = :id_tarea";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id_tarea' => $id_tarea]);

    header('Location: ../../frontend/tareas.php');
    exit;
} elseif (isset($_GET['id_tarea'])) {
    $id_tarea = $_GET['id_tarea'];  // Asignamos el id_tarea recibido por GET

    // Consultamos la tarea para mostrarla en el mensaje de confirmación
    $sql = "SELECT titulo FROM tareas WHERE id_tarea = :id_tarea";
    $stmt = $conexion->prepare($sql);
    $stmt->execute(['id_tarea' => $id_tarea]);
    $tarea = $stmt->fetch(PDO::FETCH_ASSOC);  // Guardamos el resultado de la consulta

    // Si no se encuentra la tarea, mostramos un mensaje de error
    if (!$tarea) {
        $id_tarea = null;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Eliminar tarea</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php if ($id_tarea && $tarea): ?>
        <div class="alert alert-warning text-center" role="alert">
            ¿Estás seguro de que deseas eliminar esta tarea <strong><?= htmlspecialchars($tarea['titulo']) ?></strong>?
        </div>

        <div class="text-center">
            <form method="POST" action="/backend/routes/eliminar.php">
                <input type="hidden" name="id_tarea" value="<?= htmlspecialchars($id_tarea) ?>">
                <button type="submit" name="confirmar_eliminacion" class="btn btn-danger">Eliminar</button>
                <a href="../../frontend/tareas.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center" role="alert">
            La tarea no existe o ya ha sido eliminada.
        </div>
        <div class="text-center">
            <a href="../../frontend/tareas.php" class="btn btn-secondary">Volver a la Lista</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
