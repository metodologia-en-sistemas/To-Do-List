const form = document.getElementById("formularioLogin");

form.addEventListener("submit", async function (e) {
  e.preventDefault(); // Evita que la página se recargue

  const email = document.getElementById("login-email").value;
  const password = document.getElementById("login-password").value;

  const response = await fetch("../backend/controllers/UserController.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ email, password }),
  });

  const data = await response.json();

  if (data.success) {
    // Si el login es exitoso, redirigir al usuario a la página principal
    window.location.href = "../frontend/index.html";  // Asegúrate de tener esta página
  } else {
    alert(data.message); // Mostrar el mensaje de error
  }
});