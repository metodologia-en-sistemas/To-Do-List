<?php 
session_start();
include ('../backend/config/database.php');
$query = "SELECT nombre, imagen FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->execute([$_SESSION['id_usuario']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$imagen_usuario = isset($usuario['imagen']) ? '../frontend/' . $usuario['imagen'] : '../frontend/uploads/default-avatar.png';
$_SESSION['nombre'] = $usuario['nombre'];
// Validar sesión al inicio
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../frontend/login.html');
    exit;
}

// Cargar tareas del usuario logueado
$id_usuario = $_SESSION['id_usuario'];

// Cambia esta consulta para traer SOLO las tareas de este usuario:
$sql = "SELECT * FROM tareas WHERE id_usuario = :id_usuario ";
$stmt = $conexion->prepare($sql);
$stmt->execute(['id_usuario' => $id_usuario]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tareas</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../frontend/css/estructura_das.css">
  <link rel="stylesheet" href="../frontend/css/das.css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
  <style>
    .tarea-completada {
      background-color: #138d36 !important; /* Verde fuerte personalizado */
      color: #fff !important;
    }
    .tarea-completada .badge.bg-success {
      background-color: #0a4c1a !important; /* Badge aún más oscuro */
      color: #fff !important;
    }
    .tarea-completada td {
      text-decoration: line-through;
    }
  </style>
</head>
<body>
  <div class="sidebar">
     <div class="avatar" style="margin: 20px 0; text-align: center;">
      <img src="<?php echo htmlspecialchars($imagen_usuario); ?>" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%;" />
      <p style="color: black; margin-top: 8px;"><?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
    </div>  
    <div class="nav-links">
      <a href="./dashboard.php" ><i class="icon-home"></i> Inicio</a>
      <a href="./tareas.php" class="active"><i ></i> Tareas</a>
      <a href="./comunidad.php" ><i class="icon-project"></i> Comunidad</a>
      <a href="../backend/routes/cerrar.php"><i class="icon-logout"></i> Cerrar Sesión</a>
    </div>
  </div>

<div class="container left-30">
    <h2 class="mb-4 text-center">Lista de Tareas Personales</h2>

    <div class="row mb-3">
      <div class="col-md-6 offset-md-3">
        <input type="text" id="buscador-tarea" class="form-control form-control-lg shadow-sm" placeholder="Buscar tarea por título o descripción...">
      </div>
    </div>

    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha imite</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($tareas)): ?>
                <?php foreach ($tareas as $tarea): ?>
                    <tr 
                      class="<?= $tarea['completado'] ? 'tarea-completada' : '' ?>"
                      data-titulo="<?= htmlspecialchars(strtolower($tarea['titulo'])) ?>"
                      data-descripcion="<?= htmlspecialchars(strtolower($tarea['descripcion'])) ?>"
                    >
                        <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                        <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                        <td>
                            <?php if ($tarea['completado']): ?>
                                <span class="badge bg-success">Completada</span>
                            <?php else: ?>
                                <?= htmlspecialchars($tarea['estado']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($tarea['fecha_limite']) ?></td>
                        <td>
                            <a href="../backend/routes/actualizar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="../backend/routes/eliminar.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            <?php if (!$tarea['completado']): ?>
                                <a href="../backend/routes/completado.php?id_tarea=<?= $tarea['id_tarea'] ?>" class="btn btn-success btn-sm">Marcar como Completado</a>
                            <?php else: ?>
                                <span class="badge bg-success">Completada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No hay tareas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="./creartarea.php" class="btn btn-primary">Agregar nueva Tarea</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
document.getElementById('buscador-tarea').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('tbody tr[data-titulo]').forEach(function(row) {
        const titulo = row.getAttribute('data-titulo');
        const descripcion = row.getAttribute('data-descripcion');
        if (titulo.includes(filtro) || descripcion.includes(filtro)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
