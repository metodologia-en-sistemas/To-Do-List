<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tareas</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="/frontend/css/style.css">


</head>

<body>
  <div class="sidebar">
    <h2>My Logo</h2>
    <div class="nav-links">
      <a href="#"><i class="icon-home"></i>🏠 Inicio</a>
      <a href="#"><i class="icon-tasks"></i>✅ Tareas</a>
      <a href="#"><i class="icon-calendar"></i>📅 Agenda</a>
      <a href="#"><i class="icon-project"></i>📁 Proyectos</a>
      <a href="#"><i class="icon-settings"></i>⚙️ Configuración</a>
      <a href="#"><i class="icon-logout"></i>🚪 Cerrar Sesion</a>
    </div>
  </div>

  <div class="main-content">
    <div class="topbar">
      <div class="search">
        <span>&#128269;</span>
        <input type="text" placeholder="Buscar Tarea">
      </div>
      <div class="profile"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></div>
    </div>

    <div class="greeting">
      <div class="avatar"></div>
      <div>
        <h2>Hola, Bienvenid@...</h2>
        <p><h2> Hoy es un buen día para crear tus proyectos!</h2></p>
      </div>
    </div>

    <div class="schedule-section">
      <h3>Programa tus Tareas</h3>
      <div class="calendar">
        <table>
          <thead>
            <tr>
              <th>Lunes</th>
              <th>Martes</th>
              <th>Miercoles</th>
              <th>Jueves</th>
              <th>Viernes</th>
              <th>Sábado</th>
              <th>Domingo</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <!--Tareas creadas-->
              <td></td>
              <td>
                <div class="task-box">Task A 08:00</div>
              </td>
              <td>
                <div class="task-box">Task B 09:00</div>
              </td>
              <td></td>
              <td>
                <div class="task-box">Task C 10:00</div>
              </td>
              <td>
                <div class="task-box">Task D 11:00</div>
              </td>
              <td></td>
            </tr>
          </tbody>
</body>

</html>