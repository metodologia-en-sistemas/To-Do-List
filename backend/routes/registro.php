<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Si no es POST, no hay nada que hacer
    header('Location: ../../frontend/registro.html');
    exit;
}

// 1) Leer y validar datos
$nombre   = trim($_POST['nombre']   ?? '');
$email    = trim($_POST['email']    ?? '');
$password =            $_POST['password'] ?? '';

if (!$nombre || !$email || !$password) {
    die('Faltan datos obligatorios.');
}

// 2) Verificar si el email ya existe
$sql = "SELECT id_usuario FROM usuarios WHERE email = :email";
$stmt = $conexion->prepare($sql);
$stmt->execute([':email' => $email]);

if ($stmt->fetch()) {
    // Ya existe: volvemos al registro con alerta
    echo "<script>
            alert('El correo ya existe');
            window.location = '../../frontend/registro.html';
          </script>";
    exit;
}

// 3) Insertar nuevo usuario
$hash = password_hash($password, PASSWORD_DEFAULT);
$sql  = "INSERT INTO usuarios (nombre, email, password, imagen)
         VALUES (:nombre, :email, :password, :imagen)";
$stmt = $conexion->prepare($sql);

$params = [
    ':nombre'   => $nombre,
    ':email'    => $email,
    ':password' => $hash,
    ':imagen'   => 'default.png',  // O NULL, según cómo tenga tu tabla
];

if ($stmt->execute($params)) {
    // Registro OK: al login
    header('Location: ../../frontend/login.html');
    exit;
} else {
    // Mostrar el error real (solo en desarrollo)
    $err = $stmt->errorInfo();
    die("Error al registrar: {$err[2]}");
}
