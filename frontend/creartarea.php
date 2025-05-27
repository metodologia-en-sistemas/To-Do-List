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
    $estado = 'Pendiente';

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
  <link rel="stylesheet" href="../frontend/css/estructura_das.css">
  <link rel="stylesheet" href="../frontend/css/comun.css" />
  <style>
    .main-content-crear {
      margin-left: 260px;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f4f6fa;
    }
    .crear-tarea-card {
      background: linear-gradient(135deg, #f4f9ff 60%, #e6e6fa 100%);
      border-radius: 28px;
      box-shadow: 0 8px 32px rgba(44,62,80,0.12);
      padding: 3.5rem 3rem 2.5rem 3rem;
      max-width: 540px;
      width: 100%;
      margin: 2.5rem auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      transition: box-shadow 0.2s;
    }
    .crear-tarea-card:hover {
      box-shadow: 0 12px 40px rgba(44,62,80,0.18);
    }
    .crear-tarea-card .icon-task {
      font-size: 3.2rem;
      color: #6c63ff;
      margin-bottom: 1.2rem;
      background: #fff;
      border-radius: 50%;
      padding: 0.7rem 1.1rem;
      box-shadow: 0 2px 10px rgba(108,99,255,0.08);
      display: inline-block;
    }
    .crear-tarea-card h2 {
      font-size: 2.2rem;
      font-weight: 700;
      color: #6c63ff;
      margin-bottom: 2.2rem;
      text-align: center;
      letter-spacing: 1px;
    }
    .crear-tarea-card .form-label {
      font-weight: 600;
      color: #22223b;
      margin-bottom: 0.3rem;
      font-size: 1.08em;
    }
    .crear-tarea-card .form-control {
      border-radius: 10px;
      border: 1.5px solid #eaf0fa;
      margin-bottom: 1.5rem;
      font-size: 1.08em;
      padding: 12px 16px;
      background: #f8fafd;
      transition: border-color 0.2s;
      box-shadow: 0 1px 4px rgba(44,62,80,0.03);
    }
    .crear-tarea-card .form-control:focus {
      border-color: #6c63ff;
      outline: none;
      background: #fff;
    }
    .crear-tarea-card .btn-success {
      background: linear-gradient(90deg, #6c63ff 60%, #4a90e2 100%);
      border: none;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1.15em;
      padding: 14px 0;
      margin-top: 0.7rem;
      transition: background 0.2s;
      box-shadow: 0 2px 8px rgba(108,99,255,0.08);
      letter-spacing: 0.5px;
    }
    .crear-tarea-card .btn-success:hover {
      background: linear-gradient(90deg, #5548c8 60%, #357ab8 100%);
    }
    .crear-tarea-card .btn-secondary {
      border-radius: 10px;
      margin-top: 1rem;
      font-size: 1em;
      padding: 12px 0;
      background: #eaf0fa;
      color: #6c63ff;
      border: none;
      font-weight: 600;
      transition: background 0.2s, color 0.2s;
    }
    .crear-tarea-card .btn-secondary:hover {
      background: #d6e0f5;
      color: #5548c8;
    }
    .botones-form {
      width: 100%;
      display: flex;
      justify-content: space-between;
      gap: 18px;
    }

    .crear-btn {
      min-width: 140px;
      padding: 12px 0;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1.08em;
      background: linear-gradient(90deg, #6c63ff 60%, #4a90e2 100%);
      border: none;
      box-shadow: 0 2px 8px rgba(108,99,255,0.08);
      transition: background 0.2s;
    }

    .crear-btn:hover {
      background: linear-gradient(90deg, #5548c8 60%, #357ab8 100%);
    }

    .btn-lista-tareas {
      min-width: 140px;
      padding: 12px 0;
      border-radius: 10px;
      background: #fff;
      color: #6c63ff;
      border: 2px solid #6c63ff;
      font-weight: 700;
      font-size: 1.08em;
      text-align: center;
      text-decoration: none;
      box-shadow: 0 2px 8px rgba(108,99,255,0.04);
      transition: background 0.2s, color 0.2s, border 0.2s;
    }

    .btn-lista-tareas:hover {
      background: #6c63ff;
      color: #fff;
      border: 2px solid #4a90e2;
    }

    @media (max-width: 700px) {
      .main-content-crear {
        margin-left: 0;
        padding: 1rem;
      }
      .crear-tarea-card {
        padding: 2rem 0.7rem;
        max-width: 98vw;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="avatar">
      <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar">
      <h3 style="margin-top: 1rem; color: #2d3436;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h3>
    </div>
    <nav class="nav-links">
      <a href="./dashboard.php"><i class="icon-home"></i> Inicio</a>
      <a href="./tareas.php" class="active"><i class="icon-tasks"></i> Tareas</a>
      <a href="./comunidad.php"><i class="icon-project"></i> Comunidad</a>
      <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </nav>
  </div>

  <div class="main-content-crear">
    <div class="crear-tarea-card">
      <span class="icon-task">📝</span>
      <h2>Crear Nueva Tarea</h2>
      <form method="POST" action="creartarea.php" enctype="multipart/form-data" style="width:100%;">
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
          <input type="date" name="fecha_limite" id="fecha_limite" class="form-control" required>
        </div>
        <div class="botones-form d-flex justify-content-between align-items-center mt-4">
          <button type="submit" class="btn btn-success crear-btn">Crear Tarea</button>
          <a href="./tareas.php" class="btn btn-lista-tareas">Lista de Tareas</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
