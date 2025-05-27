<?php

session_start();
include('../backend/config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Traer todas las notificaciones del usuario, más nuevas primero
$stmt = $conexion->prepare("SELECT mensaje, leida, fecha FROM notificaciones WHERE id_usuario = ? ORDER BY fecha DESC, id DESC");
$stmt->execute([$id_usuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Marcar todas como leídas al entrar
$conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE id_usuario = ?")->execute([$id_usuario]);

// Obtener imagen de usuario
$query = $conexion->prepare("SELECT imagen, nombre FROM usuarios WHERE id_usuario = ?");
$query->execute([$id_usuario]);
$user = $query->fetch(PDO::FETCH_ASSOC);
$imagen_usuario = $user && $user['imagen'] ? $user['imagen'] : 'uploads/default.png';
$nombre_usuario = $user ? $user['nombre'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    
    <title>Notificaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/estructura_das.css">
    <link rel="stylesheet" href="./css/notis.css">
    
</head>
<body>
    <div class="sidebar">
        <div class="avatar">
            <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar">
            <h3 style="margin-top: 1rem; color: #2d3436;"><?php echo htmlspecialchars($nombre_usuario); ?></h3>
        </div>
        <nav class="nav-links">
            <a href="./dashboard.php"><i class="icon-home"></i> Inicio</a>
            <a href="./notificaciones.php" class="active"><i class="icon-home"></i> Notificaciones</a>
            <a href="./tareas.php"><i class="icon-tasks"></i> Tareas</a>
            <a href="./comunidad.php"><i class="icon-project"></i> Comunidad</a>
            <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
        </nav>
    </div>
    <div class="main-content">
        <div class="notificaciones-card">
            <h2 class="notificaciones-title">
                <i class="icon-bell"></i> Tus notificaciones
            </h2>
            <div class="notificaciones-feed">
                <?php if (empty($notificaciones)): ?>
                    <div class="notificacion-item">
                        <span class="notificacion-icon">🔔</span>
                        <div>
                            <div class="notificacion-mensaje">No tienes notificaciones.</div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($notificaciones as $noti): ?>
                        <div class="notificacion-item<?= $noti['leida'] ? '' : ' notificacion-nueva' ?>">
                            <span class="notificacion-icon"><?= $noti['leida'] ? '🔔' : '🆕' ?></span>
                            <div>
                                <div class="notificacion-mensaje"><?= htmlspecialchars($noti['mensaje']) ?></div>
                                <div class="notificacion-fecha">
                                    <?= date('d/m/Y H:i', strtotime($noti['fecha'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>