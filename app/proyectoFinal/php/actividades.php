<?php
session_start();

require_once "conexion.php";

$esAdmin = isset($_SESSION["usuario_rol"]) &&
           (int) $_SESSION["usuario_rol"] === 2;

try {
    $sql = "SELECT id, titulo, descripcion, fecha, hora, tipo
            FROM actividades
            ORDER BY fecha ASC, hora ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $actividades = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades - Vida Activa</title>
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
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(
                circle,
                rgba(255, 255, 255, 0.18) 1.5px,
                transparent 1.5px
            );
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

        .seccion-titulo {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #003b73;
            margin: 1.5rem 0 0.8rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid #e6f0ff;
        }

        .fila-doble {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 600px) {
            .fila-doble {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Vida Activa</h1>

        <nav>
            <a href="../html/index.html">Inicio</a>
            
            <a href="actividades.php">Actividades</a>
            
            <a id="enlace-sesion" href="login.html">Iniciar sesión</a>
        </nav>
    </header>

    <div class="hero">
        <div class="hero-tag">Cronograma</div>
        <h2>Actividades del Centro</h2>
        <p>
            Consulte las actividades programadas para el centro diurno.
        </p>
    </div>

    <main>
        <div class="bloque">
            <h2>Cronograma de Actividades</h2>

            <?php if (count($actividades) === 0) { ?>
                <p style="color: #5a7080;">
                    No hay actividades programadas por el momento.
                </p>
            <?php } else { ?>
                <table>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                    </tr>

                    <?php foreach ($actividades as $actividad) { ?>
                        <tr>
                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $actividad["titulo"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $actividad["descripcion"] ?? "",
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                                ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($actividad["fecha"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($actividad["hora"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(ucfirst($actividad["tipo"])); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>

        <?php if ($esAdmin) { ?>
            <div class="bloque">
                <h2>Agregar Nueva Actividad</h2>

                <?php if (isset($_GET["exito"])) { ?>
                    <div class="alerta-exito">
                        Actividad agregada correctamente al cronograma.
                    </div>
                <?php } ?>

                <?php if (isset($_GET["error"])) { ?>
                    <div class="alerta-error">
                        Ocurrió un error. Verifique los datos e intente nuevamente.
                    </div>
                <?php } ?>

                <form action="procesar_actividad.php" method="POST">
                    <p class="seccion-titulo">Información de la actividad</p>

                    <div class="campo">
                        <label for="titulo">Título *</label>
                        <input
                            type="text"
                            id="titulo"
                            name="titulo"
                            placeholder="Nombre de la actividad"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="descripcion">Descripción</label>
                        <textarea
                            id="descripcion"
                            name="descripcion"
                            rows="3"
                            placeholder="Descripción de la actividad (opcional)"
                        ></textarea>
                    </div>

                    <div class="fila-doble">
                        <div class="campo">
                            <label for="fecha">Fecha *</label>
                            <input
                                type="date"
                                id="fecha"
                                name="fecha"
                                required
                            >
                        </div>

                        <div class="campo">
                            <label for="hora">Hora *</label>
                            <input
                                type="time"
                                id="hora"
                                name="hora"
                                required
                            >
                        </div>
                    </div>

                    <div class="campo">
                        <label for="tipo">Tipo de actividad *</label>

                        <select id="tipo" name="tipo" required>
                            <option value="recreativa">Recreativa</option>
                            <option value="terapeutica">Terapéutica</option>
                            <option value="social">Social</option>
                            <option value="educativa">Educativa</option>
                        </select>
                    </div>

                    <button type="submit">Agregar actividad</button>
                </form>
            </div>
        <?php } ?>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>Centro Diurno Vida Activa</h4>
                <p>
                    Brindamos cuidado profesional, calidez humana y bienestar
                    integral para adultos mayores y sus familias.
                </p>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <div class="info-item">San José, Costa Rica</div>
                <div class="info-item">Tel: 2200-0000</div>
                <div class="info-item">info@vidaactiva.cr</div>
                <div class="info-item">Lun - Vie, 7:00am - 5:00pm</div>
            </div>

            <div class="footer-col">
                <h4>Navegación</h4>
                <a href="../html/index.html">Inicio</a>
                <a href="../html/inscripcion.html">Inscripción</a>
                <a href="actividades.php">Actividades</a>
                <a href="../html/citas.html">Citas</a>
            </div>

            <div class="footer-col">
                <h4>Redes sociales</h4>

                <div class="redes">
                    <a href="#" class="red-social">Facebook</a>
                    <a href="#" class="red-social">Instagram</a>
                    <a href="#" class="red-social">WhatsApp</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2026 Centro Diurno Vida Activa — San José, Costa Rica.
            Todos los derechos reservados.
        </div>
    </footer>
    <script src="../js/sesion.js"></script>
</body>
</html>