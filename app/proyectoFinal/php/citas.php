<?php
session_start();

require_once "conexion.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../html/login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $familiar_id = $_SESSION["usuario_id"];
    $adulto_id = $_POST["adulto_id"] ?? "";
    $fecha = $_POST["fecha"] ?? "";
    $hora = $_POST["hora"] ?? "";
    $motivo = trim($_POST["motivo"] ?? "");

    if ($adulto_id == "" || $fecha == "" || $hora == "" || $motivo == "") {
        header("Location: ../html/citas.html?error=1");
        exit;
    }

    try {
        $sqlAdulto = "SELECT id
                      FROM adultos_mayores
                      WHERE id = :adulto_id
                      AND contacto_familiar_id = :familiar_id
                      AND estado = 'activo'";

        $stmtAdulto = $conexion->prepare($sqlAdulto);

        $stmtAdulto->bindParam(":adulto_id", $adulto_id, PDO::PARAM_INT);
        $stmtAdulto->bindParam(":familiar_id", $familiar_id, PDO::PARAM_INT);

        $stmtAdulto->execute();

        $adulto = $stmtAdulto->fetch(PDO::FETCH_ASSOC);

        if (!$adulto) {
            header("Location: ../html/citas.html?error=1");
            exit;
        }

        $creada_por = $familiar_id;

        $sqlCita = "INSERT INTO citas
                    (adulto_id, familiar_id, fecha, hora, motivo, estado, creada_por)
                    VALUES
                    (:adulto_id, :familiar_id, :fecha, :hora, :motivo, 'pendiente', :creada_por)";

        $stmtCita = $conexion->prepare($sqlCita);

        $stmtCita->bindParam(":adulto_id", $adulto_id, PDO::PARAM_INT);
        $stmtCita->bindParam(":familiar_id", $familiar_id, PDO::PARAM_INT);
        $stmtCita->bindParam(":fecha", $fecha);
        $stmtCita->bindParam(":hora", $hora);
        $stmtCita->bindParam(":motivo", $motivo);
        $stmtCita->bindParam(":creada_por", $creada_por, PDO::PARAM_INT);

        $stmtCita->execute();

        header("Location: ../html/citas.html?exito=1");
        exit;
    } catch (PDOException $e) {
        header("Location: ../html/citas.html?error=1");
        exit;
    }
}

header("Location: ../html/citas.html");
exit;
?>