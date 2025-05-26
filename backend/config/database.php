<?php
$host = 'localhost';
$nombre = 'root';
$password = '';
<<<<<<< HEAD
$db = "gestion_tarea";
=======
$db = "gestion_tareas";
>>>>>>> d26290157ba5549493fe00e79d986c66aaf7637d

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db", $nombre, $password);


} catch (Exception $e) {
    echo 'ocurrio un error ' . $e->getMessage();
}

?>
