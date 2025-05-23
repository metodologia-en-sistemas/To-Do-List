<?php
session_start();
include ('../config/database.php');

// Si no hay sesión iniciada, redirigimos al login
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../frontend/login.html');
    exit();
}

// Obtener datos del usuario actual
$query = "SELECT nombre, imagen FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Ruta de la imagen del usuario
$imagen_usuario = isset($usuario['imagen']) && !empty($usuario['imagen'])
    ? '../frontend/' . $usuario['imagen']
    : '../frontend/uploads/default-avatar.png';

// Guardar el nombre en la sesión si no está
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = $usuario['nombre'];
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crear Tarea</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./css/das.css" />
  <link rel="stylesheet" href="./css/comun.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
</head>
<body class="bg-light">
  <div class="sidebar">
    <div class="avatar" style="margin: 20px 0; text-align: center;">
      <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%;" />
      <p style="color: black; margin-top: 8px;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>  
    <div class="nav-links">
      <a href="./dashboard.php"><i class="icon-home"></i> Inicio</a>
      <a href="./tareas.php" class="active"><i ></i> Tareas</a>
      <a href="#"><i class="icon-calendar"></i> Agenda</a>
      <a href="./comunidad.php" ><i class="icon-project"></i> Comunidad</a>
      <a href="#"><i class="icon-settings"></i> Configuración</a>
      <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </div>
  </div>

  <div class="container mt-5" style="margin-left: 250px;">
    <h2 class="mb-4 text-center">Crear Nueva Tarea</h2>
    <form method="POST" action="./crear.php" enctype="multipart/form-data" class="border p-4 bg-white shadow-sm rounded">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ingresa la tarea" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Ingresa la descripción" required>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <input type="text" name="estado" id="estado" class="form-control" placeholder="Ingresa el estado de la tarea" required>
        </div>
        <div class="mb-3">
            <label for="fecha_limite" class="form-label">Fecha límite</label>
            <input type="date" name="fecha_limite" id="fecha_limite" class="form-control">
        </div>
        <button type="submit" class="btn btn-success w-100">Crear Tarea</button>
        <a href="../../frontend/tareas.php" class="btn btn-secondary w-100 mt-3">Lista de Tareas</a>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>