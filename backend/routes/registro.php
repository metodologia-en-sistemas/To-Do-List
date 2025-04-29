<?php
require_once './config/database.php';
require_once 'users.php';

// Obtenemos los datos enviados en formato JSON desde el frontend (por ejemplo, con fetch en JS)
$data = json_decode(file_get_contents("php://input"), true);

// Extraemos los datos desde el array asociativo 
$nombre = $data['nombre']; 
$email = $data['email'];
$password = $data['password'];


// Creamos un nuevo objeto Usuario con los datos recibidos y la conexión a la base de datos
$usuario = new Usuario($nombre, $email, $password, $conexion);

// Intentamos registrar al usuario con el método registrar()
if ($usuario->registrar()) {
  echo json_encode([
    "message" => "Registro exitoso",
    "redirect" => true,
    "url" => "../frontend/login.html" // redirigir al login
  ]);
} else {
  echo json_encode([
    "message" => "Error al registrar (¿usuario ya existe?)",
    "redirect" => false
  ]);
}