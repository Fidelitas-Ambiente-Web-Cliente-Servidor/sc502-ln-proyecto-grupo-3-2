<?php
session_start();
require_once "conexion.php";

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["usuario_rol"]) ||
    (int) $_SESSION["usuario_rol"] !== 2
) {
    header("Location: actividades.php?error=sin_permiso");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: actividades.php");
    exit;
}

$titulo = trim($_POST["titulo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$fecha = $_POST["fecha"] ?? "";
$hora = $_POST["hora"] ?? "";
$tipo = $_POST["tipo"] ?? "";

$creado_por = $_SESSION["usuario_id"];

if ($titulo === "" || $fecha === "" || $hora === "" || $tipo === "") {
    header("Location: actividades.php?error=1");
    exit;
}

try {
    $sql = "INSERT INTO actividades
                (titulo, descripcion, fecha, hora, tipo, creado_por)
            VALUES
                (:titulo, :descripcion, :fecha, :hora, :tipo, :creado_por)";

    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(":titulo", $titulo);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":fecha", $fecha);
    $stmt->bindParam(":hora", $hora);
    $stmt->bindParam(":tipo", $tipo);
    $stmt->bindParam(":creado_por", $creado_por, PDO::PARAM_INT);

    $stmt->execute();

    header("Location: actividades.php?exito=1");
    exit;

} catch (PDOException $e) {
    header("Location: actividades.php?error=1");
    exit;
}
?>