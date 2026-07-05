<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre_completo       = $_POST["nombre_completo"] ?? "";
    $cedula                = $_POST["cedula"] ?? "";
    $fecha_nacimiento      = $_POST["fecha_nacimiento"] ?? "";
    $direccion             = $_POST["direccion"] ?? "";
    $condiciones_medicas   = $_POST["condiciones_medicas"] ?? "";
    $contacto_familiar_id  = $_POST["contacto_familiar_id"] ?? "";

    // validacion basica de campos obligatorios
    if ($nombre_completo == "" || $cedula == "" || $fecha_nacimiento == "" || $direccion == "" || $contacto_familiar_id == "") {
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
        $stmt->bindParam(":nombre_completo", $nombre_completo);
        $stmt->bindParam(":fecha_nacimiento", $fecha_nacimiento);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":condiciones_medicas", $condiciones_medicas);
        $stmt->bindParam(":contacto_familiar_id", $contacto_familiar_id, PDO::PARAM_INT);
        $stmt->execute();

        // paso 2: obtener el id del adulto que acabamos de insertar
        $adulto_id = $conexion->lastInsertId();

        // paso 3: crear la solicitud de inscripcion ligada al adulto
        $sql2 = "INSERT INTO solicitudes_inscripcion (adulto_id, estado)
                 VALUES (:adulto_id, 'pendiente')";

        $stmt2 = $conexion->prepare($sql2);
        $stmt2->bindParam(":adulto_id", $adulto_id, PDO::PARAM_INT);
        $stmt2->execute();

        // todo salio bien, redirigir con mensaje de exito
        header("Location: ../html/inscripcion.html?exito=1");
        exit;

    } catch (PDOException $e) {
        // si hay error de base de datos, redirigir con mensaje de error
        header("Location: ../html/inscripcion.html?error=1");
        exit;
    }

} else {
    // si alguien entra directo al php sin enviar el formulario
    header("Location: ../html/inscripcion.html");
    exit;
}
?>