<?php
session_start(); 
 // Si no está logueado, redirigir al inicio
if (!isset($_SESSION['usuario_email'])) {
  header("Location: ./login.html"); 
  exit();
}
?>