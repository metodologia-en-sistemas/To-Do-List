<?php
$host = 'localhost';
$nombre = 'root';
$password = 'Cristian47';
$db = "gestion_tareas";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db", $nombre, $password);


} catch (Exception $e) {
    echo 'ocurrio un error ' . $e->getMessage();
}

?>
