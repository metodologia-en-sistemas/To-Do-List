<?php
session_start();
include('../config/database.php');

// Manejo de POST para creación de tarea grupal
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_limite = $_POST['fecha_limite'];
    $usuarios_asignados = $_POST['usuarios'] ?? [];

    try {
        $conexion->beginTransaction();

        // Insertar tarea comunitaria
        $sql = "INSERT INTO tareas_comunitarias (titulo, descripcion, fecha_limite, id_creador) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $titulo,
            $descripcion,
            $fecha_limite,
            $_SESSION['id_usuario']
        ]);
        $tarea_id = $conexion->lastInsertId();

        // Asignar creador
        $sql2 = "INSERT INTO tareas_asignadas (id_tarea_comunitaria, id_usuario) VALUES (?, ?)";
        $stmt2 = $conexion->prepare($sql2);
        $stmt2->execute([$tarea_id, $_SESSION['id_usuario']]);

        // Asignar usuarios seleccionados
        if (!empty($usuarios_asignados)) {
            foreach ($usuarios_asignados as $uid) {
                $stmt2->execute([$tarea_id, $uid]);
            }
        }

        $conexion->commit();
        $_SESSION['mensaje_exito'] = "Tarea grupal creada exitosamente";
    } catch (PDOException $e) {
        $conexion->rollBack();
        $_SESSION['mensaje_error'] = "Error al crear tarea: " . $e->getMessage();
        error_log($e->getMessage());
    }

    header('Location: ../../frontend/comunidad.php');
    exit;
}

// Obtener usuarios para el formulario
$stmt = $conexion->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_usuario != ?");
$stmt->execute([$_SESSION['id_usuario']]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Tarea Grupal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../frontend/css/das.css">
  <link rel="stylesheet" href="../../frontend/css/comun.css">
  <link rel="stylesheet" href="../../frontend/css/crear_grupal_styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <!-- Sidebar compartida -->
  <div class="sidebar">
    <h2>My Logo</h2>
    <div class="nav-links">
      <a href="../../frontend/dashboard.php"><i class="icon-home"></i> Inicio</a>
      <a href="../routes/mostrar.php"><i class="icon-tasks"></i> Tareas</a>
      <a href="#"><i class="icon-calendar"></i> Agenda</a>
      <a href="../../frontend/comunidad.php" class="active"><i class="icon-project"></i> Comunidad</a>
      <a href="#"><i class="icon-settings"></i> Configuración</a>
      <a href="../routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </div>
  </div>

  <div class="main-content">
    <!-- Encabezado azul fijo -->
    <header class="page-header">
      <h1>Crear Nueva Tarea Grupal</h1>
    </header>

    <!-- Formulario dentro de contenedor blanco -->
    <div class="form-container">
      <form method="POST" action="./crear_grupal.php">
        <div class="form-group">
          <label for="titulo">Título:</label>
          <input type="text" id="titulo" name="titulo" required>
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción:</label>
          <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
        </div>

        <div class="form-group">
          <label for="fecha_limite">Fecha Límite:</label>
          <input type="datetime-local" id="fecha_limite" name="fecha_limite" required>
        </div>

        <div class="form-group">
          <label>Asignar a miembros:</label>
          <div class="user-select">
            <?php foreach ($usuarios as $usuario): ?>
              <div>
                <input type="checkbox"
                       id="usuario_<?php echo $usuario['id_usuario'] ?>"
                       name="usuarios[]"
                       value="<?php echo $usuario['id_usuario'] ?>">
                <label for="usuario_<?php echo $usuario['id_usuario'] ?>">
                  <?php echo htmlspecialchars($usuario['nombre']) ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <button class="create-btn">
           <i class="fas fa-plus"></i> Crear Tarea Grupal
        </button>
      </form>
    </div>
  </div>
  <script>
document.addEventListener('DOMContentLoaded', () => {
  const dateInput = document.getElementById('fecha_limite');
  dateInput.showPicker = () => {}; // Neutraliza el picker nativo
});
</script>
</body>
</html>
