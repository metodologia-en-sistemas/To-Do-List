<?php
session_start();
include('../backend/config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ./login.html');
    exit;
}

// Alertas de sesión
foreach (['exito', 'error', 'info'] as $tipo) {
    if (isset($_SESSION[$tipo])) {
        echo "<script>alert('".addslashes($_SESSION[$tipo])."');</script>";
        unset($_SESSION[$tipo]);
    }
}

// Notificaciones
$query = "SELECT mensaje FROM notificaciones WHERE id_usuario = ? AND leida = 0";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($notificaciones as $mensaje) {
    echo "<script>alert('".addslashes($mensaje)."');</script>";
    $stmt = $conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE id_usuario = ? AND mensaje = ?");
    $stmt->execute([$_SESSION['id_usuario'], $mensaje]);
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
  <link rel="stylesheet" href="./css/das.css" />
  <link rel="stylesheet" href="./css/comun.css" />
</head>
<body>
  <div class="sidebar">
    <h2>My Logo</h2>
    <div class="nav-links">
      <a href="./dashboard.php"><i class="icon-home"></i> Inicio</a>
      <a href="../backend/routes/mostrar.php"><i class="icon-tasks"></i> Tareas</a>
      <a href="#"><i class="icon-calendar"></i> Agenda</a>
      <a href="./comunidad.php" class="active"><i class="icon-project"></i> Comunidad</a>
      <a href="#"><i class="icon-settings"></i> Configuración</a>
      <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </div>
  </div>

  <div class="main-content">
    <div class="topbar">
      <div class="search">
        <span>&#128269;</span>
        <input type="text" placeholder="Buscar tarea..." />
      </div>
    </div>

    <div class="greeting">
      <div class="avatar"></div>
      <div>
        <h2>Tareas de la Comunidad</h2>
        <p>Colabora con otros usuarios en estas tareas compartidas</p>
      </div>
    </div>

    <div class="action-buttons">
      <a href="../backend/comunidad/crear_grupal.php" class="btn-create">
        <i class="fas fa-plus"></i> Crear Tarea Colaborativa
      </a>
    </div>

    <div class="task-community">
      <?php if (empty($tareas)): ?>
        <p>No hay tareas compartidas en la comunidad.</p>
      <?php else: ?>
        <?php foreach ($tareas as $tarea): ?>
          <div class="community-task <?= 
              (strtotime($tarea['fecha_limite']) < time() && !$tarea['completada']) ? 'expired' : '' ?>">
            <div class="task-header">
              <h3>Titulo: <?= htmlspecialchars($tarea['titulo']) ?></h3>
              <span>Creada por: <?= htmlspecialchars($tarea['creador']) ?></span>
            </div>
            <p>Descripción: <?= htmlspecialchars($tarea['descripcion']) ?></p>
            <p><strong>Fecha límite:</strong> <?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></p>
            
            <div class="task-actions">
              <?php if ($tarea['completada']): ?>
                <span class="badge completed">✅ Completada</span>

              <?php elseif (strtotime($tarea['fecha_limite']) < time()): ?>
                <span class="badge expired">⏰ Caducada</span>

              <?php elseif ($_SESSION['id_usuario'] == $tarea['id_creador']): ?>
                <span class="badge pending">⌛ Pendiente por el asignado</span>

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
</body>
</html>
