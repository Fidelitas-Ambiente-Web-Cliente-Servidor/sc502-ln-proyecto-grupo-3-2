<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../html/contacto.html");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$mensaje = trim($_POST["mensaje"] ?? "");

if (
    $nombre === "" ||
    $correo === "" ||
    $telefono === "" ||
    $mensaje === "" ||
    !filter_var($correo, FILTER_VALIDATE_EMAIL)
) {
    header("Location: ../html/contacto.html?error=1");
    exit;
}

try {
    $sql = "INSERT INTO contactos_centro
                (nombre, correo, telefono, mensaje)
            VALUES
                (:nombre, :correo, :telefono, :mensaje)";

    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":correo", $correo);
    $stmt->bindParam(":telefono", $telefono);
    $stmt->bindParam(":mensaje", $mensaje);

    $stmt->execute();

    header("Location: ../html/contacto.html?exito=1");
    exit;

} catch (PDOException $e) {
    header("Location: ../html/contacto.html?error=1");
    exit;
}
?>