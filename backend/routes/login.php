<?php
session_start();
include ('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $contrasena = $_POST['password'];

        // Consulta segura con parámetros
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['email' => $email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['password'])) {
            // Guardar id_usuario y nombre en sesión
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];

            header('Location: ../../frontend/dashboard.php');
            exit;
        } else {
            echo '<script>
                alert("Contraseña o correo incorrectos");
                window.location.href = "../../frontend/login.html";
            </script>';
        }
    } else {
        echo '<script>
            alert("Por favor complete todos los campos");
            window.location.href = "../../frontend/login.html";
        </script>';
    }
}
?>
