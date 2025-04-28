<?php 
require_once '../models/User.php'; //TRAEMOS EL MODELO DE USUARIO
session_start();// SE ACTIVA LA SESION PARA MANEJAR EL LOGIN/LOGOUT

class UserController {
    //REGISTRO DE USUARIO
    public function register($data) {
        $usuario = new Usuario(); //Creamos instancia del modelo User
        $result = $usuario->create($data); //llamamos a la funcion create()

        if ($result) {
            $_SESSION['id_usuario'] = $result; //Guardamos el ID del usuario en sesion
            return ['success' => true, 'message'=> 'Usuario registrado correctamente.'];
        }
        else{
            return ['success' => false, 'message' => 'Error al registrar usuario.'];
        }
    }

    //LOGIN (incio de sesion)
    public function login($email, $password) {
        $usuario = new Usuario();
        $foundUser = $usuario->findByEmail($email); //Buscamos usuario por correo

        if ($foundUser && password_verify($password, $foundUser['password'])) {
            $_SESSION['id_usuario	'] =$foundUser['id'];
            return['success' => true, 'message' => 'Inicio de sesión exitoso.'];
        }
        else {
            return['success' => false, 'message' => 'Credenciales inválidas.'];
        }
    }

    //LOGOUT (cierre de sesion)
        public function logout() {
            session_destroy(); //Borra toda la informacion de la sesion
            return['success'=> true, 'message'=>'Sesión cerrada.'];
        }
    //OBTENER DATOS DEL USUARIO LOGUEADO
        public function getUser() {
            if (isset($_SESSION['id_usuario'])) {
                $usuario = new Usuario();// IMPORTANTE: se necesita instanciar user para usar el modelo
                return $usuario->findByID($_SESSION['id_usuario']);
            }
            return null; 
        }

}  
?>