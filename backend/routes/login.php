<?php
require_once './config/database.php';
require_once 'usuario.php';

$data = json_decode(file_get_contents("php://input"), true);
$nombre = $data['nombre'];
$email = $data['email'];
$password = $data['password'];

$usuario = new Usuario($nombre, $email, $password, $conexion);
$usuario2 = new Usuario($nombre, $email, $password, $conexion);

if ($usuario->login()) {
  if ($usuario->registrar()) {
    echo json_encode([
      "message" => "Registro exitoso",
      "redirect" => true,
      "url" => "../../frontend/index.html" // redirigir al index
    ]);
  } echo json_encode([
  "message" => "Credenciales incorrectas",
  "redirect" => false
]);
}