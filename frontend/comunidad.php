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

// Obtener asignaciones y asignados
$asignaciones = [];
$asignados = [];

if (!empty($tareas)) {
    $ids = implode(',', array_column($tareas, 'id'));
    
    // Obtener asignaciones del usuario actual
    $stmt = $conexion->prepare("SELECT id_tarea_comunitaria FROM tareas_asignadas WHERE id_usuario = ?");
    $stmt->execute([$_SESSION['id_usuario']]);
    $asignaciones = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Obtener lista de asignados por tarea
    $queryAsignados = "SELECT ta.id_tarea_comunitaria, 
                      GROUP_CONCAT(u.nombre SEPARATOR ', ') as asignados 
                      FROM tareas_asignadas ta 
                      JOIN usuarios u ON ta.id_usuario = u.id_usuario 
                      WHERE ta.id_tarea_comunitaria IN ($ids) 
                      AND ta.completada = 0 
                      GROUP BY ta.id_tarea_comunitaria";
    
    $stmt = $conexion->prepare($queryAsignados);
    $stmt->execute();
    $asignados = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Procesar cada tarea
    foreach ($tareas as &$tarea) {
        $tarea['asignado'] = in_array($tarea['id'], $asignaciones);
        $tarea['completada'] = false;

        // Verificar si el usuario completó la tarea
        if ($tarea['asignado']) {
            $stmt = $conexion->prepare("SELECT completada FROM tareas_asignadas WHERE id_tarea_comunitaria = ? AND id_usuario = ?");
            $stmt->execute([$tarea['id'], $_SESSION['id_usuario']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $tarea['completada'] = $result ? (bool)$result['completada'] : false;
        }

        // Verificar si alguien completó la tarea
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
                <span>🔍</span>
                <input type="text" placeholder="Buscar tarea..." />
            </div>
        </div>

        <div class="community-header">
            <div class="header-content">
                <h1>Tareas de la Comunidad</h1>
                <p>Colabora con otros usuarios en estas tareas compartidas</p>
            </div>
            <a href="../backend/comunidad/crear_grupal.php" class="btn-create">
                <span>+</span> Nueva Tarea Colaborativa
            </a>
        </div>

        <?php if (empty($tareas)): ?>
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>No hay tareas compartidas</h3>
                <p>Sé el primero en crear una tarea colaborativa para la comunidad</p>
            </div>
        <?php else: ?>
            <div class="task-table-container">
                <table class="community-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Creada por</th>
                            <th>Descripción</th>
                            <th>Fecha Límite</th>
                            <th>Estado</th>
                            <th>Asignado a / Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $tarea): ?>
                            <tr class="task-row <?= (strtotime($tarea['fecha_limite']) < time() && !$tarea['completada']) ? 'expired' : '' ?>">
                                <td class="task-title"><?= htmlspecialchars($tarea['titulo']) ?></td>
                                <td class="creator"><?= htmlspecialchars($tarea['creador']) ?></td>
                                <td class="description"><?= htmlspecialchars($tarea['descripcion']) ?></td>
                                <td class="date"><?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></td>
                                <td class="status">
                                    <?php
                                    if ($tarea['completada']) {
                                        echo '<span class="status-badge completed">✅ Completada</span>';
                                    } elseif (strtotime($tarea['fecha_limite']) < time()) {
                                        echo '<span class="status-badge expired">⏰ Vencida</span>';
                                    } elseif ($tarea['asignado']) {
                                        echo '<span class="status-badge assigned">👤 Asignada</span>';
                                    } else {
                                        echo '<span class="status-badge pending">⌛ Pendiente</span>';
                                    }
                                    ?>
                                </td>
                                <td class="actions">
                                    <?php if ($_SESSION['id_usuario'] == $tarea['id_creador']): ?>
                                        <div class="assigned-users">
                                            <?= !empty($asignados[$tarea['id']]) ? htmlspecialchars($asignados[$tarea['id']]) : '<span class="no-assigned">Sin asignar</span>' ?>
                                        </div>
                                    <?php else: ?>
                                        <?php if (!$tarea['completada'] && $tarea['asignado']): ?>
                                            <form method="post" action="../backend/comunidad/completar_tarea.php" class="action-form">
                                                <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>" />
                                                <button type="submit" class="btn-action btn-complete">Completar</button>
                                            </form>
                                        <?php elseif (!$tarea['asignado'] && !$tarea['completada'] && strtotime($tarea['fecha_limite']) >= time()): ?>
                                            <form method="post" action="../backend/comunidad/asignar.php" class="action-form">
                                                <input type="hidden" name="id_tarea" value="<?= $tarea['id'] ?>" />
                                                <button type="submit" class="btn-action btn-assign">Asignar</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>