<?php
session_start();
include('../backend/config/database.php');
$query = "SELECT nombre, imagen FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$imagen_usuario = isset($usuario['imagen']) ? '../frontend/' . $usuario['imagen'] : '../frontend/uploads/default-avatar.png';
$_SESSION['nombre'] = $usuario['nombre'];

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ./login.html');
    exit;
}


// Obtener tareas comunitarias
$query = "SELECT tc.id, tc.titulo, tc.descripcion, tc.fecha_limite, u.nombre as creador, tc.id_creador
          FROM tareas_comunitarias tc
          JOIN usuarios u ON tc.id_creador = u.id_usuario
          ORDER BY tc.fecha_limite ASC";
$stmt = $conexion->prepare($query);
$stmt->execute();
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar asignaciones
if (!empty($tareas)) {
    $ids = implode(',', array_column($tareas, 'id'));
    $stmt = $conexion->prepare("SELECT id_tarea_comunitaria FROM tareas_asignadas WHERE id_tarea_comunitaria IN ($ids) AND id_usuario = ?");
    $stmt->execute([$_SESSION['id_usuario']]);
    $asignaciones = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tareas as &$tarea) {
        $tarea['asignado'] = in_array($tarea['id'], $asignaciones);
        $tarea['completada'] = false;

        // Si está asignado, verificar si la completó
        if ($tarea['asignado']) {
            $stmt = $conexion->prepare("SELECT completada FROM tareas_asignadas WHERE id_tarea_comunitaria = ? AND id_usuario = ?");
            $stmt->execute([$tarea['id'], $_SESSION['id_usuario']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $tarea['completada'] = $result ? (bool)$result['completada'] : false;
        }

        // Si alguien completó la tarea (para todos)
        $stmt = $conexion->prepare("SELECT COUNT(*) FROM tareas_asignadas WHERE id_tarea_comunitaria = ? AND completada = 1");
        $stmt->execute([$tarea['id']]);
        $tarea['completada'] = $stmt->fetchColumn() > 0 ? true : $tarea['completada'];
    }
    unset($tarea);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Comunidad</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  
  <link rel="stylesheet" href="./css/estructura_das.css" />
  <link rel="stylesheet" href="./css/comun.css" />
</head>
<body>
  <div class="sidebar">
    <div class="avatar">
        <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar">
        <h3 style="margin-top: 1rem; color: #2d3436;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h3>
    </div>
    <nav class="nav-links">
        <a href="./dashboard.php" class="active"><i class="icon-home"></i> Inicio</a>
        <a href="./notificaciones.php" class="active"><i class="icon-home"></i> Notificaciones</a>
        <a href="./tareas.php"><i class="icon-tasks"></i> Tareas</a>
        <a href="./comunidad.php"><i class="icon-project"></i> Comunidad</a>
        <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </nav>
</div>

  <div class="main-content">
    <div class="topbar">
      <div class="search">
        
        <input type="text" id="buscador-tarea" placeholder="Buscar tarea..." />
      </div>
    </div>

    <br>

    <!-- Botón crear tarea colaborativa -->
<a href="../backend/comunidad/crear_grupal.php" class="btn-create">
  <i class="fas fa-plus"></i> Crear Tarea Colaborativa
</a>

    <div class="task-community">
      <?php if (empty($tareas)): ?>
        <p>No hay tareas compartidas en la comunidad.</p>
      <?php else: ?>
        <?php foreach ($tareas as $tarea): ?>
          <div class="community-task <?= 
              (strtotime($tarea['fecha_limite']) < time() && !$tarea['completada']) ? 'expired' : '' ?>" 
     data-titulo="<?= htmlspecialchars(strtolower($tarea['titulo'])) ?>" 
     data-descripcion="<?= htmlspecialchars(strtolower($tarea['descripcion'])) ?>">
    <div class="task-header">
      <span class="task-creator">👤 Creado por: <strong><?= htmlspecialchars($tarea['creador']) ?></strong></span>
    </div>
    <h3 class="task-title"><?= htmlspecialchars($tarea['titulo']) ?></h3>
    <p class="task-desc"><?= htmlspecialchars($tarea['descripcion']) ?></p>
    <p class="task-assigned">
      <strong>Asignado a:</strong>
      <?php
        // Mostrar nombres de usuarios asignados, excluyendo al creador
        $stmt = $conexion->prepare("SELECT u.nombre 
            FROM tareas_asignadas ta 
            JOIN usuarios u ON ta.id_usuario = u.id_usuario 
            WHERE ta.id_tarea_comunitaria = ? AND ta.id_usuario != ?");
        $stmt->execute([$tarea['id'], $tarea['id_creador']]);
        $asignados = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo $asignados ? htmlspecialchars(implode(', ', $asignados)) : '<span class="no-assigned">Nadie</span>';
      ?>
    </p>
    <p class="task-date"><i class="fas fa-calendar-alt"></i> <strong>Fecha límite:</strong> <?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></p>
    <div class="task-actions">
      <?php if ($tarea['completada']): ?>
        <span class="badge completed">✅ Completada</span>
      <?php elseif (strtotime($tarea['fecha_limite']) < time()): ?>
        <span class="badge expired">⏰ Caducada</span>
      <?php elseif ($_SESSION['id_usuario'] == $tarea['id_creador']): ?>
        <span class="badge pending">⌛ Pendiente por el asignado</span>
        <form method="post" action="../backend/comunidad/eliminar_tarea.php" style="display:inline;">
          <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>" />
          <button type="submit" class="btn-eliminar-comunidad" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?')">
            <i class="fas fa-trash-alt"></i> Eliminar
          </button>
        </form>
      <?php elseif ($tarea['asignado']): ?>
        <form method="post" action="../backend/comunidad/completar_tarea.php">
          <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>" />
          <button type="submit" class="btn-complete">Marcar como completada</button>
        </form>
      <?php else: ?>
        <form method="post" action="../backend/comunidad/asignar.php">
          <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>" />
          <button type="submit" class="btn-assign">Asignarme esta tarea</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <script>
document.getElementById('buscador-tarea').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('.community-task').forEach(function(card) {
        const titulo = card.getAttribute('data-titulo');
        const descripcion = card.getAttribute('data-descripcion');
        if (titulo.includes(filtro) || descripcion.includes(filtro)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
