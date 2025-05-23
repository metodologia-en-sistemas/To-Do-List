<?php
session_start();
include('../backend/config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$query = $conexion->prepare("SELECT imagen FROM usuarios WHERE id_usuario = :id_usuario");
$query->bindParam(':id_usuario', $id_usuario);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

$imagen_usuario = $user ? $user['imagen'] : 'uploads/default.png'; // Imagen por defecto si no hay
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Task Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./css/das.css" />
</head>

<body>
  <div class="sidebar">
    <div class="avatar" style="margin: 20px 0; text-align: center;">
      <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%;" />
      <p style="color: black; margin-top: 8px;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>
    <div class="nav-links">
      <a href="#" class="active"><i class="icon-home"></i> Inicio</a>
      <a href="./tareas.php"><i class="icon-tasks"></i> Tareas</a>
      <a href="./comunidad.php"><i class="icon-project"></i> Comunidad</a>
      <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </div>
  </div>

  <div class="main-content">
    <div class="topbar">
      <div class="search">
        <span>&#128269;</span>
        <input type="text" placeholder="Search Task" />
      </div>
    </div>

    <div class="schedule-section">
      <h3>Programa tus Tareas</h3>
      <div class="calendar">
        <table>
          <thead>
            <tr>
              <th>Lunes</th>
              <th>Martes</th>
              <th>Miércoles</th>
              <th>Jueves</th>
              <th>Viernes</th>
              <th>Sábado</th>
              <th>Domingo</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td></td>
              <td><div class="task-box">Task A 08:00</div></td>
              <td><div class="task-box">Task B 09:00</div></td>
              <td></td>
              <td><div class="task-box">Task C 10:00</div></td>
              <td><div class="task-box">Task D 11:00</div></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="right-panel">
      <form method="post" action="../backend/routes/crear.php">
        <button type="submit" class="create-btn" name="action" value="create">
          <i class="fas fa-plus"></i>📝 Crear Nueva Tarea
        </button>
      </form>

      <div class="priority-tasks">
        <h4>🛠️ Acciones</h4>

        <form method="post" action="../backend/routes/mostrar.php" class="priority-form">
          <input type="hidden" name="id_tarea" value="<?php echo $selected_task_id ?? ''; ?>">
          <button type="submit" class="priority-item" name="action" value="pending">
            <i class="fas fa-clock"></i> <strong>⏳Pendiente</strong>
          </button>
        </form>

        <form method="post" action="../backend/routes/mostrar.php" class="priority-form">
          <input type="hidden" name="task_id" value="<?php echo $selected_task_id ?? ''; ?>">
          <button type="submit" class="priority-item" name="action" value="ready">
            <i class="fas fa-thumbs-up"></i> <strong>✅ Listo</strong>
          </button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
