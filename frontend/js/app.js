// app.js: funciones mínimas para togglear tema y filtrar

document.getElementById('toggleDark').addEventListener('click', () => {
  document.body.classList.toggle('dark');
});

// Filtrar tareas (buscador)
document.getElementById('searchTasks').addEventListener('input', e => {
  const q = e.target.value.toLowerCase();
  document.querySelectorAll('.task-item').forEach(item => {
    const title = item.textContent.toLowerCase();
    item.style.display = title.includes(q) ? '' : 'none';
  });
});
// Referencias
const dropdownBtn = document.querySelector('.dropdown .icon-btn');
const dropdownMenu = document.querySelector('.dropdown-content');

// Al hacer clic en la tuerca, mostramos u ocultamos
dropdownBtn.addEventListener('click', e => {
  e.stopPropagation();                  // Evita que el clic se "propague" y cierre el menú inmediatamente
  const expanded = dropdownBtn.getAttribute('aria-expanded') === 'true';
  dropdownBtn.setAttribute('aria-expanded', String(!expanded));
  dropdownMenu.classList.toggle('active');
});

// Al hacer clic en cualquier parte fuera del menú, lo cerramos
document.addEventListener('click', () => {
  dropdownMenu.classList.remove('active');
  dropdownBtn.setAttribute('aria-expanded', 'false');
});

// TODO: conectar con la API backend para cargar/crear tareas