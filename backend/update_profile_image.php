<?php
session_start();
include ('../backend/config/database.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $imagen = $_FILES['profile_image'];
    $ext = pathinfo($imagen['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('perfil_') . '.' . $ext;
    $rutaDestino = '../frontend/uploads/' . $nombreArchivo;

    // Validar tipo MIME y tamaño (opcional pero recomendado)
    $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
    if (in_array($imagen['type'], $permitidos) && $imagen['size'] <= 2 * 1024 * 1024) { // 2MB máx.
        if (move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
            // Actualizar en BD
            $query = $conexion->prepare("UPDATE usuarios SET imagen = :imagen WHERE id_usuario = :id");
            $query->bindParam(':imagen', $rutaDestino);
            $query->bindParam(':id', $id_usuario);
            $query->execute();
        }
    }
}

header("Location: ../frontend/dashboard.php");
exit;
