<?php
require_once '../config/database.php';

$db = new Database();
$conexion = $db->connect();

if ($conexion) {
    echo "Conexión establecida correctamente.";
} else {
    echo "Fallo la conexión.";
}
?>
