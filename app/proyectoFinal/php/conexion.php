<?php
$host = "localhost";
$usuario = "Pruebas";         
$password = "Pruebas123";     
$bd = "centro_diurno";        

$conexion = new mysqli($host, $usuario, $password, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>