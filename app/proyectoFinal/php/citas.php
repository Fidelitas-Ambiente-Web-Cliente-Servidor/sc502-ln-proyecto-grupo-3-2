<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $adulto_id   = $_POST["adulto_id"] ?? null;
    $familiar_id = $_POST["familiar_id"] ?? null;
    $fecha       = $_POST["fecha"] ?? null;
    $hora        = $_POST["hora"] ?? null;
    $motivo      = $_POST["motivo"] ?? null;

    if ($adulto_id && $familiar_id && $fecha && $hora && $motivo) {
        $creada_por = $familiar_id;

        try {
            $sql = "INSERT INTO citas (adulto_id, familiar_id, fecha, hora, motivo, estado, creada_por)
                    VALUES (:adulto_id, :familiar_id, :fecha, :hora, :motivo, 'pendiente', :creada_por)";

            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":adulto_id", $adulto_id, PDO::PARAM_INT);
            $stmt->bindParam(":familiar_id", $familiar_id, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":hora", $hora);
            $stmt->bindParam(":motivo", $motivo);
            $stmt->bindParam(":creada_por", $creada_por, PDO::PARAM_INT);
            $stmt->execute();

            // Después de guardar, volvemos a la página de citas
            header("Location: ../html/citas.html");
            exit;
        } catch (PDOException $e) {
            die("Error al guardar la cita: " . $e->getMessage());
        }
    } else {
        die("Complete todos los campos del formulario.");
    }
} else {
    // Si alguien entra directo, lo mandamos al HTML
    header("Location: ../html/citas.html");
    exit;
}
?>