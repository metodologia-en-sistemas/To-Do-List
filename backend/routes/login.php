<?php
session_start();
include ('../config/database.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = false;
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $contrasena = $_POST['password'];
    }
    //primera consulta de la tabla de usuarios
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $prepar = $conexion->prepare($sql);
    $prepar->execute();

    //recorrer la primera tabla
    foreach ($prepar as $email) {
        if (password_verify($contrasena, $email['password'])) {
            $login = true;
            $_SESSION['nombre'] = $email['nombre'];
        }
    }
    if ($login) {
        header('Location: ../../frontend/index.html');
    } else {
    echo '<script language = javascript>
    alert("Contraseña o correo incorrectos")
    self.location="../../frontend/login.html"</script>';
}
}
?>