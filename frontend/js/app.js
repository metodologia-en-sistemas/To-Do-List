// Función para iniciar sesión
function login() {
  // Obtenemos el email y la contraseña del formulario
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  // Enviamos los datos al backend usando fetch con POST
  fetch("../backend/routes/login.php", {
    method: "POST", // Tipo de solicitud
    headers: { "Content-Type": "application/json" }, // Indicamos que enviamos JSON
    body: JSON.stringify({ email, password }) // Convertimos el objeto JS a JSON
  })
  .then(res => res.json()) // Convertimos la respuesta a objeto JS
  .then(data => {
    if (data.redirect) {
      // Si el backend responde con redirección, vamos a esa URL
      window.location.href = data.url;
    } else {
      // Si no hay redirección, mostramos el mensaje del backend
      alert(data.message);
    }
  });
}
// Función para registrar usuario
function registrar() {
  // Obtenemos email, contraseña y nombre del formulario de registro
  const nombre = document.getElementById("nombre").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;
  // Enviamos los datos al backend con fetch
  fetch("../backend/routes/registro.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nombre, email, password })
  })
  .then(res => res.json())
  .then(data => {
    if (data.redirect) {
      window.location.href = data.url;
    } else {
      alert(data.message);
    }
  });
}




//crud tareas

const appState = {
  tasks: [],
  nextId: 1
};

// Referencias DOM
const taskForm = document.getElementById('task-form');
const taskInput = document.getElementById('task-input');
const taskList = document.getElementById('task-list');
const emptyState = document.getElementById('empty-state');
const totalTasksElement = document.getElementById('total-tasks');
const completedTasksElement = document.getElementById('completed-tasks');
const pendingTasksElement = document.getElementById('pending-tasks');

// Función para renderizar la lista de tareas
function renderTasks() {
  // Actualizar estadísticas
  const completedTasks = appState.tasks.filter(task => task.completed).length;
  totalTasksElement.textContent = appState.tasks.length;
  completedTasksElement.textContent = completedTasks;
  pendingTasksElement.textContent = appState.tasks.length - completedTasks;
  
  // Mostrar u ocultar el estado vacío
  if (appState.tasks.length === 0) {
      emptyState.style.display = 'block';
      taskList.innerHTML = '';
      return;
  }
  
  emptyState.style.display = 'none';

  // Generar HTML para cada tarea
  taskList.innerHTML = appState.tasks.map(task => `
      <li class="task-item ${task.completed ? 'completed' : ''}" data-id="${task.id}">
          <div class="d-flex justify-content-between align-items-center">
              <div>
                  <input type="checkbox" class="form-check-input me-2 task-checkbox" 
                      ${task.completed ? 'checked' : ''}>
                  <span class="task-text">${task.text}</span>
              </div>
              <div class="task-actions">
                  <button class="btn btn-sm btn-danger delete-task">
                      <i class="fas fa-trash"></i>
                  </button>
              </div>
          </div>
      </li>
  `).join('');
  
  // Agregar event listeners para los checkboxes y botones de eliminar
  document.querySelectorAll('.task-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', toggleTaskComplete);
  });
  
  document.querySelectorAll('.delete-task').forEach(button => {
      button.addEventListener('click', deleteTask);
  });
}

// Función para añadir una nueva tarea
function addTask(text) {
  const newTask = {
      id: appState.nextId++,
      text: text,
      completed: false,
      createdAt: new Date()
  };
  
  appState.tasks.push(newTask);
  saveToLocalStorage();
  renderTasks();
}

// Función para marcar/desmarcar una tarea como completada
function toggleTaskComplete(e) {
  const taskItem = e.target.closest('.task-item');
  const taskId = parseInt(taskItem.dataset.id);
  
  const task = appState.tasks.find(t => t.id === taskId);
  if (task) {
      task.completed = e.target.checked;
      saveToLocalStorage();
      renderTasks();
  }
}

// Función para eliminar una tarea
function deleteTask(e) {
  const taskItem = e.target.closest('.task-item');
  const taskId = parseInt(taskItem.dataset.id);
  
  appState.tasks = appState.tasks.filter(task => task.id !== taskId);
  saveToLocalStorage();
  renderTasks();
}

// Guardar en localStorage
function saveToLocalStorage() {
  localStorage.setItem('taskCollab', JSON.stringify(appState));
}

// Cargar desde localStorage
function loadFromLocalStorage() {
  const saved = localStorage.getItem('taskCollab');
  if (saved) {
      const parsed = JSON.parse(saved);
      appState.tasks = parsed.tasks || [];
      appState.nextId = parsed.nextId || 1;
  }
}

// Event listeners
taskForm.addEventListener('submit', function(e) {
  e.preventDefault();
  const text = taskInput.value.trim();
  
  if (text) {
      addTask(text);
      taskInput.value = '';
      taskInput.focus();
  }
});

// Inicializar la aplicación
document.addEventListener('DOMContentLoaded', function() {
  loadFromLocalStorage();
  renderTasks();
});

const toggleButton = document.getElementById('dark-mode-toggle');
 toggleButton.addEventListener('click', () => {
     document.body.classList.toggle('dark-mode');
 });