<?php
require_once "conexion.php";

try {
    $sql = "SELECT id, titulo, descripcion, fecha, hora, tipo, estado
            FROM actividades
            ORDER BY fecha ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar actividades: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades - Centro Diurno Vida Activa</title>
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
            .fila-doble { grid-template-columns: 1fr; }
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
        <div class="hero-tag">Cronograma</div>
        <h2>Actividades del Centro</h2>
        <p>Consulte las actividades programadas y agregue nuevas al cronograma del centro diurno.</p>
    </div>

    <main>

        <div class="bloque">
            <h2>Cronograma de Actividades</h2>

            <?php if (count($actividades) == 0) { ?>
                <p style="color: #5a7080;">No hay actividades programadas por el momento.</p>
            <?php } else { ?>
                <table>
                    <tr>
                        <th>Titulo</th>
                        <th>Descripcion</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                    </tr>
                    <?php foreach ($actividades as $actividad) { ?>
                        <tr>
                            <td><?php echo $actividad["titulo"]; ?></td>
                            <td><?php echo $actividad["descripcion"]; ?></td>
                            <td><?php echo $actividad["fecha"]; ?></td>
                            <td><?php echo $actividad["hora"]; ?></td>
                            <td><?php echo $actividad["tipo"]; ?></td>
                            <td>
                                <span class="badge <?php echo $actividad['estado']; ?>">
                                    <?php echo ucfirst($actividad["estado"]); ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>

        <div class="bloque">
            <h2>Agregar Nueva Actividad</h2>

            <?php if (isset($_GET["exito"])): ?>
                <div class="alerta-exito">Actividad agregada correctamente al cronograma.</div>
            <?php elseif (isset($_GET["error"])): ?>
                <div class="alerta-error">Ocurrio un error. Verifique los datos e intente nuevamente.</div>
            <?php endif; ?>

            <form action="procesar_actividad.php" method="POST">

                <p class="seccion-titulo">Informacion de la actividad</p>

                <div class="campo">
                    <label for="titulo">Titulo *</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Nombre de la actividad" required>
                </div>

                <div class="campo">
                    <label for="descripcion">Descripcion</label>
                    <textarea id="descripcion" name="descripcion" rows="3" placeholder="Descripcion de la actividad (opcional)"></textarea>
                </div>

                <div class="fila-doble">
                    <div class="campo">
                        <label for="fecha">Fecha *</label>
                        <input type="date" id="fecha" name="fecha" required>
                    </div>
                    <div class="campo">
                        <label for="hora">Hora *</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>
                </div>

                <div class="fila-doble">
                    <div class="campo">
                        <label for="tipo">Tipo de actividad</label>
                        <select id="tipo" name="tipo">
                            <option value="recreativa">Recreativa</option>
                            <option value="terapeutica">Terapeutica</option>
                            <option value="social">Social</option>
                            <option value="educativa">Educativa</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="creado_por">ID del administrador *</label>
                        <input type="number" id="creado_por" name="creado_por" placeholder="ID del admin" required>
                    </div>
                </div>

                <button type="submit">Agregar Actividad</button>

            </form>
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