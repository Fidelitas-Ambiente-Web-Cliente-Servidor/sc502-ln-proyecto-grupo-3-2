<?php
require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

try {
    $sql = "SELECT id, adulto_id, familiar_id, fecha, hora, motivo, estado
            FROM citas
            ORDER BY fecha, hora";
    $stmt = $conexion->query($sql);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok"    => true,
        "citas" => $citas
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "ok"    => false,
        "error" => $e->getMessage()
    ]);
}
?>