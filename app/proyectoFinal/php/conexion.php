<?php
$host = "db";
$bd   = "centro_diurno";
$usuario = "Pruebas";
$password = "Pruebas123";

try {
    $dsn = "mysql:host=$host;dbname=$bd;charset=utf8";
    $conexion = new PDO($dsn, $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>