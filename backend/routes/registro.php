<?php
require_once './config/database.php';
require_once 'Users.php'; // Asegúrate de incluir la clase User

// Obtenemos los datos enviados en formato JSON desde el frontend (por ejemplo, con fetch en JS)
$data = json_decode(file_get_contents("php://input"), true);

// Extraemos los datos desde el array asociativo 
$nombre = $data['nombre']; 
$email = $data['email'];
$password = $data['password'];

// Creamos una nueva instancia del modelo User con los datos recibidos
$user = new User();

// Intentamos registrar al usuario con el método create()
$result = $user->create(['nombre' => $nombre, 'correo' => $email, 'contrasena' => $password]);

if ($result) {
  echo json_encode([
    "message" => "Registro exitoso",
    "redirect" => true,
    "url" => "../frontend/login.html" // Redirigir al login
  ]);
} else {
  echo json_encode([
    "message" => "Error al registrar (¿usuario ya existe?)",
    "redirect" => false
  ]);
}
?>