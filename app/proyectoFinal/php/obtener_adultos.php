<?php
session_start();

require_once "conexion.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode([]);
    exit;
}

$familiar_id = $_SESSION["usuario_id"];

try {
    $sql = "SELECT id, nombre_completo
            FROM adultos_mayores
            WHERE contacto_familiar_id = :familiar_id
            AND estado = 'activo'
            ORDER BY nombre_completo";

    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(":familiar_id", $familiar_id, PDO::PARAM_INT);

    $stmt->execute();

    $adultos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($adultos);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>