<?php
$host = 'localhost';
$nombre = 'root';
$password = '';
$db = "gestion_tarea";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db", $nombre, $password);


} catch (Exception $e) {
    echo 'ocurrio un error ' . $e->getMessage();
}

?>
