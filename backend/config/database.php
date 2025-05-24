<?php
$host = 'localhost';
$nombre = 'root';
$password = 'Ored*2541*';
$db = "gestion_tareas_res1";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$db", $nombre, $password);


} catch (Exception $e) {
    echo 'ocurrio un error ' . $e->getMessage();
}

?>