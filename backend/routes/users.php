<?php
// Definimos una clase llamada Usuario
class Usuario {
  // Propiedades privadas de la clase (no se pueden acceder desde fuera directamente)
  private $nombre;
  private $email;
  private $password;
  private $conexion;

  // Constructor que inicializa los datos al crear un objeto Usuario
  public function __construct($nombre, $email, $password, $conexion) {
    $this->nombre = $nombre;          
    $this->email = $email;          
    $this->password = $password;      
    $this->conexion = $conexion;      
  }

  // Método para registrar un nuevo usuario en la base de datos
  public function registrar() {
    
    // Encriptamos la contraseña 
      $hashed = password_hash($this->password, PASSWORD_DEFAULT); 

      // Preparamos la consulta SQL para insertar un nuevo usuario
      $stmt = $this->conexion->prepare("INSERT INTO usuarios (nombre, correo_electronico, contraseña) VALUES (?, ?, ?)");

    // Enviamos los 3 valores
      $stmt->bind_param("sss", $this->nombre, $this->email, $hashed); 
      return $stmt->execute(); // Ejecutamos la consulta
    
    
  }

  // Método para iniciar sesión (verificar si el usuario y contraseña coinciden)
  public function login() {
    // Preparamos la consulta para buscar la contraseña del usuario por email
    $stmt = $this->conexion->prepare("SELECT contraseña FROM usuarios WHERE correo_electronico = ?");

    // Vinculamos el parámetro del email
    $stmt->bind_param("s", $this->email);

    // Ejecutamos la consulta
    $stmt->execute();

    // Obtenemos el resultado de la consulta
    $result = $stmt->get_result();

    // Si existe una fila con ese email, verificamos la contraseña
    if ($row = $result->fetch_assoc()) {
      // Comparamos la contraseña en texto con la contraseña encriptada
      return password_verify($this->password, $row["contraseña"]);
    }

    // Si no se encuentra el usuario o no coincide, devolvemos false
    return false;
  }
}
