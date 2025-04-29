
// Modo oscuro persistente
window.addEventListener('DOMContentLoaded', () => {
  if (localStorage.getItem('darkMode') === 'enabled') {
    document.body.classList.add('dark');
  }
});

document.getElementById('toggleDark')?.addEventListener('click', () => {
  document.body.classList.toggle('dark');
  if (document.body.classList.contains('dark')) {
    localStorage.setItem('darkMode', 'enabled');
  } else {
    localStorage.setItem('darkMode', '');
  }
});

// Dropdown de configuración
const dropdownBtn = document.querySelector('.dropdown .icon-btn');
const dropdownMenu = document.querySelector('.dropdown-content');
dropdownBtn?.addEventListener('click', e => {
  e.stopPropagation();
  dropdownMenu.classList.toggle('active');
});
document.addEventListener('click', () => {
  dropdownMenu?.classList.remove('active');
});

// Tabla: búsqueda + paginación
(function() {
  const tableBody = document.querySelector('#tasksTable tbody');
  const paginationDiv = document.getElementById('pagination');
  const searchInput = document.getElementById('searchTasks');

  if (!tableBody) return;

  let currentPage = 1;
  const pageSize = 5;
  let currentSearch = '';

  function loadTasks() {
    const url = new URL('/api/tasks', window.location.origin);
    url.searchParams.set('page', currentPage);
    url.searchParams.set('limit', pageSize);
    url.searchParams.set('search', currentSearch);

    fetch(url)
      .then(res => res.json())
      .then(({ data, total }) => {
        renderTable(data);
        renderPagination(total);
      })
      .catch(err => console.error('Error cargando tareas:', err));
  }

  function renderTable(tasks) {
    tableBody.innerHTML = '';
    tasks.forEach(task => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><input type="checkbox" ${task.completed ? 'checked' : ''}></td>
        <td>${task.title}</td>
        <td>${task.description}</td>
        <td>
          <button class="icon-btn" title="Editar">✏️</button>
          <button class="icon-btn" title="Eliminar">🗑️</button>
        </td>
      `;
      tableBody.appendChild(tr);
    });
  }

  function renderPagination(totalItems) {
    const pageCount = Math.ceil(totalItems / pageSize);
    paginationDiv.innerHTML = '';
    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      if (i === currentPage) btn.disabled = true;
      btn.addEventListener('click', () => {
        currentPage = i;
        loadTasks();
      });
      paginationDiv.appendChild(btn);
    }
  }

  searchInput?.addEventListener('input', e => {
    currentSearch = e.target.value.trim();
    currentPage = 1;
    loadTasks();
  });

  document.addEventListener('DOMContentLoaded', loadTasks);
})();