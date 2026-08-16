<?php
session_start();

require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

if (
    !isset($_SESSION["usuario_rol"]) ||
    (int) $_SESSION["usuario_rol"] !== 2
) {
    http_response_code(403);

    echo json_encode([
        "ok" => false,
        "error" => "No tiene permisos para acceder al dashboard."
    ]);

    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos = json_decode(file_get_contents("php://input"), true);

    $tipo = $datos["tipo"] ?? "";
    $id = filter_var($datos["id"] ?? null, FILTER_VALIDATE_INT);
    $estado = $datos["estado"] ?? "";

    if (!$id) {
        http_response_code(400);

        echo json_encode([
            "ok" => false,
            "error" => "El identificador no es válido."
        ]);

        exit;
    }

    try {
        if ($tipo === "cita") {
            $estadosPermitidos = [
                "pendiente",
                "confirmada",
                "realizada",
                "cancelada"
            ];

            if (!in_array($estado, $estadosPermitidos, true)) {
                throw new Exception("El estado de la cita no es válido.");
            }

            $sql = "UPDATE citas
                    SET estado = :estado
                    WHERE id = :id";

        } elseif ($tipo === "consulta") {
            $estadosPermitidos = [
                "pendiente",
                "atendida"
            ];

            if (!in_array($estado, $estadosPermitidos, true)) {
                throw new Exception("El estado de la consulta no es válido.");
            }

            $sql = "UPDATE contactos_centro
                    SET estado = :estado
                    WHERE id = :id";

        } else {
            throw new Exception("El tipo de registro no es válido.");
        }

        $stmt = $conexion->prepare($sql);

        $stmt->execute([
            ":estado" => $estado,
            ":id" => $id
        ]);

        echo json_encode([
            "ok" => true,
            "mensaje" => "Estado actualizado correctamente."
        ]);

        exit;

    } catch (Exception $e) {
        http_response_code(400);

        echo json_encode([
            "ok" => false,
            "error" => $e->getMessage()
        ]);

        exit;
    }
}

try {
    $sqlCitas = "SELECT id, adulto_id, familiar_id, fecha, hora, motivo, estado
                  FROM citas
                  ORDER BY fecha ASC, hora ASC";

    $stmtCitas = $conexion->query($sqlCitas);
    $citas = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);

    $sqlConsultas = "SELECT id, nombre, correo, telefono, mensaje,
                            fecha_creacion, estado
                     FROM contactos_centro
                     ORDER BY
                        CASE WHEN estado = 'pendiente' THEN 0 ELSE 1 END,
                        fecha_creacion DESC";

    $stmtConsultas = $conexion->query($sqlConsultas);
    $consultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "ok" => true,
        "citas" => $citas,
        "consultas" => $consultas
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "error" => "No fue posible cargar la información del dashboard."
    ]);
}
?>