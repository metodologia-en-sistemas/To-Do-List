<?php
require_once __DIR__ . '/../config/database.php';
$db = new Database();
$conexion = $db->connect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $categoria=$_POST['categoria'];
    $fecha_creacion=$_POST['fecha_creacion'];

    // Insertar el usuario en la base de datos con la imagen
    $sql = "INSERT INTO tareas (titulo, descripcion,categoria,fecha_creacion) VALUES (:titulo, :descripcion, :categoria, :fecha_creacion)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'categoria' => $categoria,
        'fecha_creacion'=>$fecha_creacion
    ]);

    header('Location: ./mostrar.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <title>Crear Tarea</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Crear Nueva Tarea</h2>

    <form method="POST" action="./crear.php" enctype="multipart/form-data" class="border p-4 bg-white shadow-sm rounded">
        <div class="mb-3">
            <label for="titulo" class="form-label">titulo</label>
            <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ingresa la tarea" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">descripcion</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Ingresa la descripcion" required>
        </div>

        <div class="mb-3">
            <label for="categoria" class="form-label">categoria</label>
            <input type="text" name="categoria" id="categoria" class="form-control" placeholder="Ingresa la categoria" required>
        </div>

        <div class="mb-3">
            <label for="fecha_creacion" class="form-label">Fecha de Creacion</label>
            <input type="date" name="fecha_creacion" id="fecha_creacion" class="form-control" accept="fecha_creacion">
        </div>

        <button type="submit " href="mostrar.php" class="btn btn-success w-100">Crear Tarea</button>
        <a href="./mostrar.php" class="btn btn-secondary w-100 mt-3">Lista de Tareas</a>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
