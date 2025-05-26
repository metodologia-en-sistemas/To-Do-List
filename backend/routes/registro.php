<?php
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = $_POST['password'];

    // Verificar si el email ya existe
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $prepar = $conexion->prepare($sql);
    $prepar->bindParam(':email', $email);
    $prepar->execute();

    if ($prepar->rowCount() > 0) {
        echo '<script language="javascript">
            alert("Correo ya existente");
            self.location="../../frontend/registro.html";
        </script>';
        exit();
    }

    // Hashear la contraseña
    $contrasenaEncrip = password_hash($contrasena, PASSWORD_DEFAULT);

    // Procesar imagen
    $imagenNombre = $_FILES['imagen']['name'];
    $imagenTemp = $_FILES['imagen']['tmp_name'];
    $rutaDestino = "../../frontend/uploads/" . $imagenNombre;

    if (!move_uploaded_file($imagenTemp, $rutaDestino)) {
        echo "Error al subir la imagen.";
        exit();
    }

    $rutaEnBD = "uploads/" . $imagenNombre;

    // Insertar usuario nuevo
    $insert = "INSERT INTO usuarios(nombre, email, password, imagen) 
               VALUES (:nombre, :email, :password, :imagen)";
    $stmt = $conexion->prepare($insert);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $contrasenaEncrip);
    $stmt->bindParam(':imagen', $rutaEnBD);

    if ($stmt->execute()) {
        header('Location: ../../frontend/login.html');
        exit();
    } else {
        echo "Error al registrar el usuario.";
    }
}
?>
