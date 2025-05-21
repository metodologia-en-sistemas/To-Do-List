<?php
session_start();
include('../backend/config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ./login.html');
    exit;
}

// Mostrar alertas con mensajes de sesión
if (isset($_SESSION['exito'])) {
    echo "<script>alert('".addslashes($_SESSION['exito'])."');</script>";
    unset($_SESSION['exito']);
}
if (isset($_SESSION['error'])) {
    echo "<script>alert('".addslashes($_SESSION['error'])."');</script>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['info'])) {
    echo "<script>alert('".addslashes($_SESSION['info'])."');</script>";
    unset($_SESSION['info']);
}

// Mostrar notificaciones pendientes para el usuario
$query = "SELECT mensaje FROM notificaciones 
          WHERE id_usuario = ? AND leida = 0";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($notificaciones as $mensaje) {
    echo "<script>alert('".addslashes($mensaje)."');</script>";
    $query = "UPDATE notificaciones SET leida = 1 
              WHERE id_usuario = ? AND mensaje = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_SESSION['id_usuario'], $mensaje]);
}

// Obtener tareas de la comunidad
$query = "SELECT tc.id, tc.titulo, tc.descripcion, tc.fecha_limite, 
                 u.nombre as creador, tc.id_creador
          FROM tareas_comunitarias tc
          JOIN usuarios u ON tc.id_creador = u.id_usuario
          ORDER BY tc.fecha_limite ASC";

$stmt = $conexion->prepare($query);
$stmt->execute();
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar asignaciones
if (!empty($tareas)) {
    $query = "SELECT id_tarea_comunitaria 
              FROM tareas_asignadas 
              WHERE id_tarea_comunitaria IN (".implode(',', array_column($tareas, 'id')).")
              AND id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->execute([$_SESSION['id_usuario']]);
    $asignaciones = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tareas as &$tarea) {
        $tarea['asignado'] = in_array($tarea['id'], $asignaciones);
        if ($_SESSION['id_usuario'] == $tarea['id_creador']) {
            $tarea['asignado'] = true;
        }

        $tarea['completada'] = false;
        
        if ($tarea['asignado']) {
            if ($_SESSION['id_usuario'] == $tarea['id_creador']) {
                $tarea['completada'] = true;
            } else {
                $query = "SELECT completada 
                          FROM tareas_asignadas 
                          WHERE id_tarea_comunitaria = ? 
                          AND id_usuario = ?";
                $stmt = $conexion->prepare($query);
                $stmt->execute([$tarea['id'], $_SESSION['id_usuario']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $tarea['completada'] = $result['completada'] ?? false;
            }
        }
    }
    unset($tarea);
}
?>

<!DOCTYPE html>
<html lang="en">
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
              (strtotime($tarea['fecha_limite']) < time() && !$tarea['completada_global']) ? 'expired' : '' ?>">
            <div class="task-header">
              <h3>Titulo: <?= htmlspecialchars($tarea['titulo']) ?></h3>
              <span>Creada por: <?= htmlspecialchars($tarea['creador']) ?></span>
            </div>
            <p>Descripción: <?= htmlspecialchars($tarea['descripcion']) ?></p>
            <p><strong>Fecha límite:</strong> <?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></p>
            
            <div class="task-actions">
              <?php if ($_SESSION['id_usuario'] == $tarea['id_creador']): ?>
                <?php if ($tarea['completada']): ?>
                  <span class="badge completed">✅ Tarea completada por el asignado</span>
                <?php else: ?>
                  <span class="badge pending">⌛ Pendiente por el asignado</span>
                <?php endif; ?>

              <?php elseif ($tarea['asignado']): ?>
                <?php if ($tarea['completada']): ?>
                  <span class="badge completed">✅ Completada</span>
                <?php elseif (strtotime($tarea['fecha_limite']) > time()): ?>
                  <form method="post" action="../backend/comunidad/completar_tarea.php">
                    <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>" />
                    <button type="submit" class="btn-complete">Marcar como completada</button>
                  </form>
                <?php else: ?>
                  <span class="badge expired">⌛ Caducada</span>
                <?php endif; ?>

              <?php elseif (strtotime($tarea['fecha_limite']) > time()): ?>
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
