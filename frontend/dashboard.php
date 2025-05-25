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
            <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar">
            <h3 style="margin-top: 1rem; color: #2d3436;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h3>
        </div>
        <nav class="nav-links">
            <a href="#" class="active"><i class="icon-home"></i> Inicio</a>
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

        <div class="search">
            <span>🔍</span>
            <input type="text" placeholder="Buscar tarea...">
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Progreso Semanal</h2>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <div class="card">
                <h2>Próximas Tareas</h2>
                <div class="task-list">
                    <div class="task-box">Reunión de equipo - 10:00</div>
                    <div class="task-box">Revisión de diseño - 14:30</div>
                    <div class="task-box">Entrega de proyecto - 16:00</div>
                </div>
            </div>
        </div>

        <div class="calendar">
            <h3>Calendario de Tareas</h3>
            <table>
                <thead>
                    <tr>
                        <th>Lun</th><th>Mar</th><th>Mié</th><th>Jue</th>
                        <th>Vie</th><th>Sáb</th><th>Dom</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="task-box">Informe 09:00</div></td>
                        <td></td>
                        <td><div class="task-box">Revisión 11:00</div></td>
                        <td></td>
                        <td><div class="task-box">Presentación 15:00</div></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="theme-selector">
            <button data-theme="light">Claro</button>
            <button data-theme="dark">Oscuro</button>
            <button data-theme="nature">Naturaleza</button>
        </div>
    </div>

    <script>
        // Gráfico de rendimiento
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Productividad',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    borderColor: '#4A90E2',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(74, 144, 226, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Sistema de temas
        document.querySelectorAll('.theme-selector button').forEach(button => {
            button.addEventListener('click', () => {
                document.body.setAttribute('data-theme', button.dataset.theme);
            });
        });
    </script>
</body>
</html>