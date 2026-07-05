<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo      = $_POST["titulo"] ?? "";
    $descripcion = $_POST["descripcion"] ?? "";
    $fecha       = $_POST["fecha"] ?? "";
    $hora        = $_POST["hora"] ?? "";
    $tipo        = $_POST["tipo"] ?? "";
    $creado_por  = $_POST["creado_por"] ?? "";

    // validacion de campos obligatorios
    if ($titulo == "" || $fecha == "" || $hora == "" || $creado_por == "") {
        header("Location: actividades.php?error=1");
        exit;
    }

    try {
        $sql = "INSERT INTO actividades (titulo, descripcion, fecha, hora, tipo, creado_por)
                VALUES (:titulo, :descripcion, :fecha, :hora, :tipo, :creado_por)";

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

} else {
    header("Location: actividades.php");
    exit;
}
?>