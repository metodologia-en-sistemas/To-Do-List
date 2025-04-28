<?php 
require_once '../models/User.php'; // TRAEMOS EL MODELO DE USUARIO
session_start(); // SE ACTIVA LA SESIÓN PARA MANEJAR EL LOGIN/LOGOUT

class UserController {
    // REGISTRO DE USUARIO
    public function register($data) {
        $user = new User(); // Creamos instancia del modelo User
        $result = $user->create($data); // Llamamos a la función create()

        if ($result) {
            $_SESSION['id_usuario'] = $result; // Guardamos el ID del usuario en sesión
            return ['success' => true, 'message' => 'Usuario registrado correctamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar usuario.'];
        }
    }

    // LOGIN (inicio de sesión)
    public function login($correo, $contrasena) {
        $user = new User();
        $foundUser = $user->findByEmail($correo); // Buscamos usuario por correo

        if ($foundUser && password_verify($contrasena, $foundUser['contrasena'])) {
            $_SESSION['id_usuario'] = $foundUser['id_usuario'];
            return ['success' => true, 'message' => 'Inicio de sesión exitoso.'];
        } else {
            return ['success' => false, 'message' => 'Credenciales inválidas.'];
        }
    }

    // LOGOUT (cierre de sesión)
    public function logout() {
        session_destroy(); // Borra toda la información de la sesión
        return ['success' => true, 'message' => 'Sesión cerrada.'];
    }

    // OBTENER DATOS DEL USUARIO LOGUEADO
    public function getUser() {
        if (isset($_SESSION['id_usuario'])) {
            $user = new User(); // IMPORTANTE: se necesita instanciar user para usar el modelo
            return $user->findByID($_SESSION['id_usuario']);
        }
        return null;
    }
}
?>
