const form = document.getElementById("formularioRegistro");

form.addEventListener("submit", async function (e) {
  e.preventDefault(); // Evita que la página se recargue

  const nombre = document.getElementById("nombre").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  // Usamos fetch para enviar los datos al backend
  const response = await fetch("../backend/register.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ nombre, email, password }),
  });

  const data = await response.json();

  alert(data.message); // Mostrar el mensaje de la respuesta

  // Si el registro es exitoso, redirigir al login
  if (data.redirect) {
    window.location.href = data.url;
  }
});
