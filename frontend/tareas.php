<?php 
session_start();
include ('../backend/config/database.php');
$query = "SELECT nombre, imagen FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$imagen_usuario = isset($usuario['imagen']) ? '../frontend/' . $usuario['imagen'] : '../frontend/uploads/default-avatar.png';
$_SESSION['nombre'] = $usuario['nombre'];
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tareas</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./css/das.css" />
  <link rel="stylesheet" href="./css/comun.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    .sidebar {
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      background: #f8f9fa;
      padding: 20px 0;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      z-index: 1;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px 20px;
      min-height: 100vh;
      background: linear-gradient(to right, #c6ffdd, #fbd786, #f7797d);
    }

    .tarea-completada {
      background-color: #138d36 !important;
      color: #fff !important;
    }

    .tarea-completada .badge.bg-success {
      background-color: #0a4c1a !important;
      color: #fff !important;
    }

    .tarea-completada td {
      text-decoration: line-through;
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

  <div class="main-content">
    <div class="container mt-5">
      <h2 class="mb-4 text-center">Lista de Tareas Personales</h2>

      <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
          <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Fecha Límite</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($tareas)): ?>
            <?php foreach ($tareas as $tarea): ?>
              <tr class="<?= $tarea['completado'] ? 'tarea-completada' : '' ?>">
                <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                <td>
                  <?php if ($tarea['completado']): ?>
                    <span class="badge bg-success">Completada</span>
                  <?php else: ?>
                    <?= htmlspecialchars($tarea['estado']) ?>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($tarea['fecha_limite']) ?></td>
                <td>
                  <a href="../backend/routes/actualizar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-warning btn-sm">Editar</a>
                  <a href="../backend/routes/eliminar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                  <?php if (!$tarea['completado']): ?>
                    <a href="../backend/routes/completado.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-success btn-sm">Marcar como Completado</a>
                  <?php else: ?>
                    <span class="badge bg-success">Completada</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-center">No hay tareas registradas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="text-center">
        <a href="./creartarea.php" class="btn btn-primary">Agregar nueva Tarea</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
