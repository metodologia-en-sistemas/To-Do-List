<?php
// Definimos una clase llamada Usuario
class Usuario {
    // Propiedades privadas de la clase (no se pueden acceder desde fuera directamente)
    private $nombre;
    private $correo;
    private $contrasena;
    private $conexion;

    // Constructor que inicializa los datos al crear un objeto Usuario
    public function __construct($nombre, $correo, $contrasena, $conexion) {
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->contrasena = $contrasena;
        $this->conexion = $conexion;
    }

    // Método para registrar un nuevo usuario en la base de datos
    public function registrar() {
        // Encriptamos la contraseña
        $hashed = password_hash($this->contrasena, PASSWORD_DEFAULT);

        // Preparamos la consulta SQL para insertar un nuevo usuario
        $stmt = $this->conexion->prepare("INSERT INTO usuario (nombre, correo, contrasena) VALUES (?, ?, ?)");

        // Enviamos los 3 valores
        $stmt->bind_param("sss", $this->nombre, $this->correo, $hashed);
        return $stmt->execute(); // Ejecutamos la consulta
    }

    // Método para iniciar sesión (verificar si el usuario y contraseña coinciden)
    public function login() {
        // Preparamos la consulta para buscar la contraseña del usuario por correo
        $stmt = $this->conexion->prepare("SELECT contrasena FROM usuario WHERE correo = ?");

        // Vinculamos el parámetro del correo
        $stmt->bind_param("s", $this->correo);

        // Ejecutamos la consulta
        $stmt->execute();

        // Obtenemos el resultado de la consulta
        $result = $stmt->get_result();

        // Si existe una fila con ese correo, verificamos la contraseña
        if ($row = $result->fetch_assoc()) {
            // Comparamos la contraseña en texto con la contraseña encriptada
            return password_verify($this->contrasena, $row["contrasena"]);
        }

        // Si no se encuentra el usuario o no coincide, devolvemos false
        return false;
    }
}
?>
