<?php
session_start();
include ('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id_usuario'])) {
        die("No se ha iniciado sesión. Redirige al login si es necesario.");
    }

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $fecha_limite = $_POST['fecha_limite'];
    $id_usuario = $_SESSION['id_usuario'];

    $sql = "INSERT INTO tareas (titulo, descripcion, estado, fecha_limite, id_usuario) 
            VALUES (:titulo, :descripcion, :estado, :fecha_limite, :id_usuario)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'estado' => $estado,
        'fecha_limite' => $fecha_limite,
        'id_usuario' => $id_usuario
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
            <label for="estado" class="form-label">estado</label>
            <input type="text" name="estado" id="estado" class="form-control" placeholder="Ingresa el estado de la tarea" required>
        </div>

        <div class="mb-3">
            <label for="fecha_limite" class="form-label">Fecha limite</label>
            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control" accept="fecha_limite">
        </div>

        <button type="submit " href="mostrar.php" class="btn btn-success w-100">Crear Tarea</button>
        <a href="./mostrar.php" class="btn btn-secondary w-100 mt-3">Lista de Tareas</a>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
