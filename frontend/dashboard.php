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

$imagen_usuario = $user ? $user['imagen'] : 'uploads/default.png';

// Tareas pendientes (ningún usuario la completó y aún no venció)
$stmtTareas = $conexion->prepare("
    SELECT tc.titulo
    FROM tareas_comunitarias tc
    WHERE tc.fecha_limite >= CURDATE()
    AND NOT EXISTS (
        SELECT 1 FROM tareas_asignadas ta 
        WHERE ta.id_tarea_comunitaria = tc.id 
        AND ta.completada = 1
    )
");
$stmtTareas->execute();
$tareasPendientes = $stmtTareas->fetchAll(PDO::FETCH_ASSOC);

// Tareas completadas (al menos un usuario la completó)
$stmtCompletadas = $conexion->prepare("
    SELECT DISTINCT tc.titulo
    FROM tareas_comunitarias tc
    INNER JOIN tareas_asignadas ta ON ta.id_tarea_comunitaria = tc.id
    WHERE ta.completada = 1
");
$stmtCompletadas->execute();
$tareasCompletadas = $stmtCompletadas->fetchAll(PDO::FETCH_ASSOC);

// Tareas caducadas (nadie la completó y ya venció)
$stmtCaducadas = $conexion->prepare("
    SELECT tc.titulo
    FROM tareas_comunitarias tc
    WHERE tc.fecha_limite < CURDATE()
    AND NOT EXISTS (
        SELECT 1 FROM tareas_asignadas ta 
        WHERE ta.id_tarea_comunitaria = tc.id 
        AND ta.completada = 1
    )
");
$stmtCaducadas->execute();
$tareasCaducadas = $stmtCaducadas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/estructura_das.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="sidebar">
        <div class="avatar">
            <form action="../backend/update_profile_image.php" method="POST" enctype="multipart/form-data" id="profileImageForm">
                <label for="profileInput">
                    <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar" class="profile-img" title="Haz clic para cambiar tu foto">
                </label>
                <input type="file" name="profile_image" id="profileInput" accept="image/*" style="display: none;" onchange="document.getElementById('profileImageForm').submit();">
            </form>
            <h3 style="margin-top: 1rem; color: #2d3436;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h3>
        </div>

        <nav class="nav-links">
            <a href="#" class="active"><i class="icon-home"></i> Inicio</a>
            <a href="./notificaciones.php" class="active"><i class="icon-home"></i> Notificaciones</a>
            <a href="./tareas.php"><i class="icon-tasks"></i> Tareas</a>
            <a href="./comunidad.php"><i class="icon-project"></i> Comunidad</a>
            <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="hero-header">
            <video autoplay muted loop class="background-video">
                <source src="../frontend/assets/4864927-uhd_2160_4096_25fps.mp4" type="video/mp4">
            </video>
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
        </div>

        <div class="dashboard-grid">
            <div class="card historial-tareas-card">
                <h2 class="historial-title">Historial de Tareas</h2>
                <div class="historial-section">
                    <h4 class="historial-subtitle completadas">
                        <i class="icon-check"></i> Tareas completadas (<?php echo count($tareasCompletadas); ?>)
                    </h4>
                    <div class="card-list">
                        <?php if (count($tareasCompletadas) > 0): ?>
                            <?php foreach ($tareasCompletadas as $tarea): ?>
                                <div class="mini-card">
                                    <?php echo htmlspecialchars($tarea['titulo']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="mini-card">No hay tareas completadas.</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="historial-section">
                    <h4 class="historial-subtitle caducadas">
                        <i class="icon-alert"></i> Tareas caducadas (<?php echo count($tareasCaducadas); ?>)
                    </h4>
                    <div class="card-list">
                        <?php if (count($tareasCaducadas) > 0): ?>
                            <?php foreach ($tareasCaducadas as $tarea): ?>
                                <div class="mini-card expired">
                                    <?php echo htmlspecialchars($tarea['titulo']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="mini-card">No hay tareas caducadas.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card historial-tareas-card">
                <h2 class="historial-title">Tareas Pendientes de la Comunidad</h2>
                <div class="historial-section">
                    <div class="card-list">
                        <?php if (count($tareasPendientes) > 0): ?>
                            <?php foreach ($tareasPendientes as $tarea): ?>
                                <div class="mini-card pending">
                                    <?php echo htmlspecialchars($tarea['titulo']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="mini-card">No hay tareas pendientes en la comunidad.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card actividad-personal-card">
                <h2 class="historial-title">Tu Actividad</h2>
                <canvas id="actividadChart" width="300" height="180"></canvas>
            </div>
        </div>

        <center>
            <div class="motivational-card">
                <?php
                $frases = [
                    "¡Hoy es un gran día para avanzar en tus metas!",
                    "La constancia es la clave del éxito.",
                    "Cada pequeño paso cuenta.",
                    "No te detengas, ¡vas muy bien!",
                    "El futuro depende de lo que hagas hoy."
                ];
                $frase = $frases[array_rand($frases)];
                ?>
                <i class="icon-idea"></i>
                <span><?php echo $frase; ?></span>
            </div>
        </center>
    </div>

    <script>
        const ctx = document.getElementById('actividadChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completadas', 'Pendientes', 'Caducadas'],
                datasets: [{
                    data: [<?php echo count($tareasCompletadas); ?>, <?php echo count($tareasPendientes); ?>, <?php echo count($tareasCaducadas); ?>],
                    backgroundColor: ['#00B894', '#ffe066', '#e74c3c'], // rojo para caducadas
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

</body>

</html>