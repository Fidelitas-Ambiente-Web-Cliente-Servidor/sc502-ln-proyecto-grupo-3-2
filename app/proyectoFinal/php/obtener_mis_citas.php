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
    $sql = "SELECT
                c.id,
                a.nombre_completo AS adulto_mayor,
                c.fecha,
                c.hora,
                c.motivo,
                c.estado
            FROM citas c
            INNER JOIN adultos_mayores a
                ON c.adulto_id = a.id
            WHERE c.familiar_id = :familiar_id
            ORDER BY c.fecha ASC, c.hora ASC";

    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(":familiar_id", $familiar_id, PDO::PARAM_INT);

    $stmt->execute();

    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($citas);

} catch (PDOException $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
?>