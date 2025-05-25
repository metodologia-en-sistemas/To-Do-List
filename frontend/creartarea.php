<?php 
session_start();
include('../backend/config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../frontend/login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $fecha_limite = $_POST['fecha_limite'] ?? null;
    $id_usuario = $_SESSION['id_usuario'];
    $estado = 'Pendiente'; // Estado por defecto

    if ($titulo && $descripcion) {
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

        header('Location: ./tareas.php');
        exit();
    }
}

$query = "SELECT nombre, imagen FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$imagen_usuario = isset($usuario['imagen']) ? '../frontend/' . $usuario['imagen'] : '../frontend/uploads/default-avatar.png';
$_SESSION['nombre'] = $usuario['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Tarea</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../frontend/css/das.css" />
  <link rel="stylesheet" href="../../frontend/css/comun.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>

  <style>
    body {
      background: linear-gradient(135deg, #9ad1d4, #f3e7e9);
      min-height: 100vh;
      margin: 0;
      font-family: 'Inter', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .sidebar {
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
    }

    .content {
      margin-left: 250px; /* ancho típico de sidebar */
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    form {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 500px;
    }

    .form-label,
    .form-control,
    .btn {
      z-index: 2;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <div class="avatar" style="margin: 20px 0; text-align: center;">
      <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%;" />
      <p style="color: black; margin-top: 8px;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>  

    <div class="nav-links">
      <a href="./dashboard.php"><i class="icon-home"></i> Inicio</a>
      <a href="./tareas.php" class="active"><i></i> Tareas</a>
      <a href="./comunidad.php"><i class="icon-project"></i> Comunidad</a>
      <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </div>
  </div>

  <div class="content">
    <form method="POST" action="creartarea.php" enctype="multipart/form-data">
      <h2>Crear Nueva Tarea</h2>
      <div class="mb-3">
        <label for="titulo" class="form-label">Título</label>
        <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ingresa la tarea" required>
      </div>
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Ingresa la descripción" required>
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

