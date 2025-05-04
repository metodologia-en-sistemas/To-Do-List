<?php
session_start(); 
require_once './config/database.php';
require_once './models/User.php';
// esta variable convierte el json en un array asociativo
$data = json_decode(file_get_contents("php://input"), true);
$nombre = $data['nombre'];
$email = $data['email'];
$password = $data['password'];

$usuario = new User($nombre, $email, $password, $conexion);

if ($usuario->login($email, $password)) {
  {
    $_SESSION['usuario_email'] = $email;
    echo json_encode([
      "message" => "Sesion exitosa",
      "redirect" => true,
      "url" => "../../frontend/index.html" // redirigir al index
    ]);
  } echo json_encode([
  "message" => "Usuario o contraseña incorrectas",
  "redirect" => false
]);
}