<?php
class Database {
private $host = '127.0.0.1';
private $dbname = 'gestiontareas';
private $user = 'root';  
private $password = '7351346';  
private $conn = null; //Se guarda de manera segura la conexion a la base de datos

public function connect(){
    if ($this->conn === null){
try {
    $conexion = new PDO("mysql:host={$this->host};dbname={$this->dbname}",
    $this->user, 
    $this->password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<script language = javascript> alert('Conexion Fallida') self.location='../index.php'</script> ".
    $e->getMessage();
}
}
return $this->conn;
}
}
?>