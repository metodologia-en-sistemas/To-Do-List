<?php
require_once '../config/database.php';
require_once '../models/User.php';

$data = json_decode(file_get_contents("php://input"), true);

// Extraemos los datos del request
$nombre = $data['nombre'];
$email = $data['email'];
$password = $data['password'];

// Creamos un objeto de usuario
$user = new User($nombre, $email, $password);

// Verificamos si el email ya está registrado
if ($user->findByEmail($email)) {
    echo json_encode([
        "success" => false,
        "message" => "El correo electrónico ya está registrado"
    ]);
    exit;
}

// Si no está registrado, intentamos crear el usuario
if ($user->create($data)) {
    echo json_encode([
        "success" => true,
        "message" => "Registro exitoso",
        "redirect" => true,
        "url" => "../frontend/login.html"  // redirigir al login
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error al registrar usuario. Intente nuevamente."
    ]);
}
?>
