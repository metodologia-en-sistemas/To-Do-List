<?php
session_start();
include('../config/database.php');

// Obtener nombre e imagen del usuario para la sidebar
$stmt = $conexion->prepare("SELECT nombre, imagen FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$imagen_usuario = isset($usuario['imagen']) && $usuario['imagen'] 
    ? '../../frontend/' . $usuario['imagen'] 
    : '../../frontend/uploads/default-avatar.png';
$_SESSION['nombre'] = $usuario['nombre'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_limite = $_POST['fecha_limite'];
    $usuarios_asignados = $_POST['usuarios'] ?? [];

    try {
        $conexion->beginTransaction();

        $query = "INSERT INTO tareas_comunitarias 
        (titulo, descripcion, fecha_limite, id_creador) 
        VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($query);
$stmt->execute([
    $_POST['titulo'],
    $_POST['descripcion'],
    $_POST['fecha_limite'],
    $_SESSION['id_usuario']
]);
        $tarea_id = $conexion->lastInsertId();

        // 2. Asignar automáticamente al creador
        $stmt = $conexion->prepare("INSERT INTO tareas_asignadas 
                             (id_tarea_comunitaria, id_usuario) 
                             VALUES (?, ?)");
        $stmt->execute([$tarea_id, $_SESSION['id_usuario']]);

       // Asignar usuarios seleccionados
if (!empty($_POST['usuarios'])) {
    $query = "INSERT INTO tareas_asignadas 
             (id_tarea_comunitaria, id_usuario) 
             VALUES (?, ?)";
    $stmt = $conexion->prepare($query);
    
    foreach ($_POST['usuarios'] as $usuario_id) {
        $stmt->execute([$tarea_id, $usuario_id]);
    }
}

        $conexion->commit();
        $_SESSION['mensaje_exito'] = "Tarea grupal creada exitosamente";
    } catch (PDOException $e) {
        $conexion->rollBack();
        $_SESSION['mensaje_error'] = "Error al crear tarea grupal: " . $e->getMessage();
        // Para debuggear:
        error_log("Error en crear_grupal.php: " . $e->getMessage());
        echo "Error detallado: " . $e->getMessage(); // Solo para desarrollo
    }

    header("Location: ../../frontend/comunidad.php");
    exit;
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
</head>
<body>
    <div class="sidebar">
        <div class="avatar" style="margin: 20px 0; text-align: center;">
            <img src="<?php echo htmlspecialchars($imagen_usuario ?? '../frontend/uploads/default-avatar.png'); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%;" />
            <p style="color: black; margin-top: 8px;"><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></p>
        </div>
        <div class="nav-links">
            <a href="../../frontend/dashboard.php"><i class="icon-home"></i> Inicio</a>
            <a href="../../frontend/tareas.php"><i></i> Tareas</a>
            <a href="../../frontend/comunidad.php" class="active"><i class="icon-project"></i> Comunidad</a>
            <a href="../routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
        </div>
    </div>

    <div class="container-dashboard">
        <div class="main-content">
            <div class="greeting">
                <h2>Crear Nueva Tarea Grupal</h2>
            </div>
            <a href="../../frontend/comunidad.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Volver</a>
            <form method="POST" action="./crear_grupal.php" class="task-form">
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
                    <?php
                    // Obtener todos los usuarios excepto el actual
                    $stmt = $conexion->prepare("SELECT id_usuario, nombre FROM usuarios WHERE id_usuario != ?");
                    $stmt->execute([$_SESSION['id_usuario']]);
                    $usuarios = $stmt->fetchAll();

                    foreach ($usuarios as $usuario): ?>
                        <div>
                            <input type="checkbox" id="usuario_<?= $usuario['id_usuario'] ?>" 
                                name="usuarios[]" value="<?= $usuario['id_usuario'] ?>">
                            <label for="usuario_<?= $usuario['id_usuario'] ?>">
                                <?= htmlspecialchars($usuario['nombre']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="create-btn">
                    <i class="fas fa-users"></i> Crear Tarea Grupal
                </button>
            </form>
        </div>
    </div>
</body>
</html>