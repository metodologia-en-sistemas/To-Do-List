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