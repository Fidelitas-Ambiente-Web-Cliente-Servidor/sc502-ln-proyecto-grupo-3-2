<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre_completo     = $_POST["nombre_completo"] ?? "";
    $cedula              = $_POST["cedula"] ?? "";
    $fecha_nacimiento    = $_POST["fecha_nacimiento"] ?? "";
    $direccion           = $_POST["direccion"] ?? "";
    $condiciones_medicas = $_POST["condiciones_medicas"] ?? "";

    // tomar el id del familiar desde la sesion (no del formulario)
    $contacto_familiar_id = $_SESSION['usuario_id'] ?? null;

    // validacion de campos obligatorios
    if ($nombre_completo == "" || $cedula == "" || $fecha_nacimiento == "" || $direccion == "" || $contacto_familiar_id == null) {
        header("Location: ../html/inscripcion.html?error=1");
        exit;
    }

    try {
        // paso 1: insertar el adulto mayor
        $sql = "INSERT INTO adultos_mayores
                    (nombre_completo, fecha_nacimiento, cedula, direccion, condiciones_medicas, contacto_familiar_id, estado)
                VALUES
                    (:nombre_completo, :fecha_nacimiento, :cedula, :direccion, :condiciones_medicas, :contacto_familiar_id, 'pendiente')";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":nombre_completo",     $nombre_completo);
        $stmt->bindParam(":fecha_nacimiento",    $fecha_nacimiento);
        $stmt->bindParam(":cedula",              $cedula);
        $stmt->bindParam(":direccion",           $direccion);
        $stmt->bindParam(":condiciones_medicas", $condiciones_medicas);
        $stmt->bindParam(":contacto_familiar_id", $contacto_familiar_id, PDO::PARAM_INT);
        $stmt->execute();

        // paso 2: obtener el id del adulto recien insertado
        $adulto_id = $conexion->lastInsertId();

        // paso 3: crear la solicitud de inscripcion
        $sql2 = "INSERT INTO solicitudes_inscripcion (adulto_id, estado)
                 VALUES (:adulto_id, 'pendiente')";

        $stmt2 = $conexion->prepare($sql2);
        $stmt2->bindParam(":adulto_id", $adulto_id, PDO::PARAM_INT);
        $stmt2->execute();

        header("Location: ../html/inscripcion.html?exito=1");
        exit;

    } catch (PDOException $e) {
        header("Location: ../html/inscripcion.html?error=1");
        exit;
    }

} else {
    header("Location: ../html/inscripcion.html");
    exit;
}
?>