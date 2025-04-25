<?php
$host = '127.0.0.1';
$dbname = 'gestiontareas';
$user = 'root';  
$password = '7351346';  

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<script language = javascript> alert('Conexion Fallida') self.location='../index.php'</script> ".
    $e->getMessage();
}
?>
