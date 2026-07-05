<?php
require_once "conexion.php";

if (isset($_GET["accion"]) && isset($_GET["id"])) {
    $id     = $_GET["id"];
    $accion = $_GET["accion"];

    if ($accion == "aprobar") {
        $nuevoEstado = "aprobada";
    } else if ($accion == "rechazar") {
        $nuevoEstado = "rechazada";
    } else {
        $nuevoEstado = "";
    }

    if ($nuevoEstado != "") {
        try {
            $sql = "UPDATE solicitudes_inscripcion SET estado = :estado WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(":estado", $nuevoEstado);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $sql2 = "UPDATE adultos_mayores SET estado = :estado
                     WHERE id = (SELECT adulto_id FROM solicitudes_inscripcion WHERE id = :id)";
            $stmt2 = $conexion->prepare($sql2);
            $stmt2->bindParam(":estado", $nuevoEstado);
            $stmt2->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt2->execute();
        } catch (PDOException $e) {
            die("Error al actualizar: " . $e->getMessage());
        }
    }
}

try {
    $sql = "SELECT s.id, s.fecha_solicitud, s.estado, a.nombre_completo, a.cedula
            FROM solicitudes_inscripcion s
            JOIN adultos_mayores a ON s.adulto_id = a.id
            ORDER BY
                CASE s.estado
                    WHEN 'pendiente' THEN 1
                    WHEN 'aprobada' THEN 2
                    WHEN 'rechazada' THEN 3
                END,
                s.fecha_solicitud DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pendientes = 0; $aprobadas = 0; $rechazadas = 0;
    foreach ($solicitudes as $s) {
        if ($s["estado"] == "pendiente") $pendientes++;
        else if ($s["estado"] == "aprobada") $aprobadas++;
        else if ($s["estado"] == "rechazada") $rechazadas++;
    }
} catch (PDOException $e) {
    die("Error al consultar: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revision de Solicitudes - Centro Diurno Vida Activa</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #003b73 0%, #005bb5 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.18) 1.5px, transparent 1.5px);
            background-size: 16px 16px;
        }

        .hero-tag {
            display: inline-block;
            background: rgba(255, 204, 77, 0.25);
            color: #ffcc4d;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .hero h2 {
            font-size: 2rem;
            margin: 0 0 0.7rem 0;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .hero p {
            opacity: 0.85;
            font-size: 0.97rem;
            margin: 0 auto;
            max-width: 500px;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }

        .stats {
            display: flex;
            gap: 1.2rem;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 150px;
            background: white;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0,59,115,0.10);
            border-top: 4px solid;
        }

        .stat-card.pendiente { border-color: #ffcc4d; }
        .stat-card.aprobada  { border-color: #28a745; }
        .stat-card.rechazada { border-color: #dc3545; }

        .stat-card .numero {
            font-size: 2.2rem;
            font-weight: 700;
            color: #003b73;
            line-height: 1;
        }

        .stat-card .etiqueta {
            font-size: 0.85rem;
            color: #5a7080;
            margin-top: 0.3rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <header>
        <h1>Centro Diurno Vida Activa</h1>
        <nav>
            <a href="../html/index.html">Inicio</a>
            <a href="../html/inscripcion.html">Inscripcion</a>
            <a href="actividades.php">Actividades</a>
            <a href="../html/citas.html">Citas</a>
        </nav>
    </header>

    <div class="hero">
        <div class="hero-tag">Administracion</div>
        <h2>Revision de Solicitudes</h2>
        <p>Gestione las solicitudes de inscripcion de adultos mayores al centro diurno.</p>
    </div>

    <main>

        <div class="stats">
            <div class="stat-card pendiente">
                <div class="numero"><?php echo $pendientes; ?></div>
                <div class="etiqueta">Pendientes</div>
            </div>
            <div class="stat-card aprobada">
                <div class="numero"><?php echo $aprobadas; ?></div>
                <div class="etiqueta">Aprobadas</div>
            </div>
            <div class="stat-card rechazada">
                <div class="numero"><?php echo $rechazadas; ?></div>
                <div class="etiqueta">Rechazadas</div>
            </div>
        </div>

        <div class="bloque">
            <h2>Listado de Solicitudes</h2>

            <?php if (count($solicitudes) == 0) { ?>
                <p style="color: #5a7080;">No hay solicitudes registradas.</p>
            <?php } else { ?>
                <table>
                    <tr>
                        <th>Nombre del adulto mayor</th>
                        <th>Cedula</th>
                        <th>Fecha de solicitud</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                    <?php foreach ($solicitudes as $fila) { ?>
                        <tr>
                            <td><?php echo $fila["nombre_completo"]; ?></td>
                            <td><?php echo $fila["cedula"]; ?></td>
                            <td><?php echo $fila["fecha_solicitud"]; ?></td>
                            <td>
                                <span class="badge <?php echo $fila['estado']; ?>">
                                    <?php echo ucfirst($fila["estado"]); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($fila["estado"] == "pendiente") { ?>
                                    <button class="btn-aprobar" onclick="window.location='revision_solicitudes.php?accion=aprobar&id=<?php echo $fila['id']; ?>'">Aprobar</button>
                                    <button class="btn-rechazar" onclick="window.location='revision_solicitudes.php?accion=rechazar&id=<?php echo $fila['id']; ?>'">Rechazar</button>
                                <?php } else { ?>
                                    <span style="color:#aaa;font-size:0.85rem;">Procesada</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>

    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>Centro Diurno Vida Activa</h4>
                <p>Brindamos cuidado profesional, calidez humana y bienestar integral para adultos mayores y sus familias.</p>
            </div>
            <div class="footer-col">
                <h4>Contacto</h4>
                <div class="info-item">San Jose, Costa Rica</div>
                <div class="info-item">Tel: 2200-0000</div>
                <div class="info-item">info@vidaactiva.cr</div>
                <div class="info-item">Lun - Vie, 7:00am - 5:00pm</div>
            </div>
            <div class="footer-col">
                <h4>Navegacion</h4>
                <a href="../html/index.html">Inicio</a>
                <a href="../html/inscripcion.html">Inscripcion</a>
                <a href="actividades.php">Actividades</a>
                <a href="../html/citas.html">Citas</a>
            </div>
            <div class="footer-col">
                <h4>Redes Sociales</h4>
                <div class="redes">
                    <a href="#" class="red-social">Facebook</a>
                    <a href="#" class="red-social">Instagram</a>
                    <a href="#" class="red-social">WhatsApp</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 Centro Diurno Vida Activa &mdash; San Jose, Costa Rica. Todos los derechos reservados.
        </div>
    </footer>

</body>
</html>