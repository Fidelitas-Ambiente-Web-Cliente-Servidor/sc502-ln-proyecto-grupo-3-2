<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_rol']) || (int) $_SESSION['usuario_rol'] !== 2) {
    header('Location: ../html/login.html');
    exit;
}

$mensaje = '';
$accion = $_GET['accion'] ?? 'listar';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$usuarioEditar = [
    'id' => '',
    'nombre' => '',
    'correo' => '',
    'telefono' => '',
    'rol_id' => 1
];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idPost = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $rolId = isset($_POST['rol_id']) ? (int) $_POST['rol_id'] : 1;
        $password = $_POST['password'] ?? '';

        if ($nombre === '' || $correo === '') {
            throw new Exception('El nombre y el correo son obligatorios.');
        }

        if ($rolId !== 1 && $rolId !== 2) {
            $rolId = 1;
        }

        if ($idPost === 0) {
            if ($password === '') {
                throw new Exception('La contraseña es obligatoria para crear un usuario.');
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $sql = 'INSERT INTO usuarios
                    (nombre, correo, password, telefono, rol_id, fecha_creacion)
                    VALUES
                    (:nombre, :correo, :password, :telefono, :rol_id, NOW())';

            $sentencia = $conexion->prepare($sql);
            $sentencia->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':password' => $passwordHash,
                ':telefono' => $telefono,
                ':rol_id' => $rolId
            ]);

            header('Location: usuarios.php?mensaje=creado');
            exit;
        }

        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $sql = 'UPDATE usuarios
                    SET nombre = :nombre,
                        correo = :correo,
                        telefono = :telefono,
                        rol_id = :rol_id,
                        password = :password
                    WHERE id = :id';

            $sentencia = $conexion->prepare($sql);
            $sentencia->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':telefono' => $telefono,
                ':rol_id' => $rolId,
                ':password' => $passwordHash,
                ':id' => $idPost
            ]);
        } else {
            $sql = 'UPDATE usuarios
                    SET nombre = :nombre,
                        correo = :correo,
                        telefono = :telefono,
                        rol_id = :rol_id
                    WHERE id = :id';

            $sentencia = $conexion->prepare($sql);
            $sentencia->execute([
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':telefono' => $telefono,
                ':rol_id' => $rolId,
                ':id' => $idPost
            ]);
        }

        header('Location: usuarios.php?mensaje=actualizado');
        exit;
    }

    if ($accion === 'eliminar' && $id > 0) {
        if ($id === (int) $_SESSION['usuario_id']) {
            throw new Exception('No es posible eliminar el usuario con sesión activa.');
        }

        $sql = 'DELETE FROM usuarios WHERE id = :id';
        $sentencia = $conexion->prepare($sql);
        $sentencia->execute([':id' => $id]);

        header('Location: usuarios.php?mensaje=eliminado');
        exit;
    }

    if ($accion === 'editar' && $id > 0) {
        $sql = 'SELECT id, nombre, correo, telefono, rol_id
                FROM usuarios
                WHERE id = :id';

        $sentencia = $conexion->prepare($sql);
        $sentencia->execute([':id' => $id]);

        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            throw new Exception('El usuario seleccionado no existe.');
        }

        $usuarioEditar = $resultado;
    }

    $sql = 'SELECT id, nombre, correo, telefono, rol_id, fecha_creacion
            FROM usuarios
            ORDER BY id DESC';

    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['mensaje'])) {
        if ($_GET['mensaje'] === 'creado') {
            $mensaje = 'Usuario creado correctamente.';
        }

        if ($_GET['mensaje'] === 'actualizado') {
            $mensaje = 'Usuario actualizado correctamente.';
        }

        if ($_GET['mensaje'] === 'eliminado') {
            $mensaje = 'Usuario eliminado correctamente.';
        }
    }
} catch (Exception $e) {
    $mensaje = $e->getMessage();

    $sql = 'SELECT id, nombre, correo, telefono, rol_id, fecha_creacion
            FROM usuarios
            ORDER BY id DESC';

    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de usuarios</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .tabla-contenedor {
            width: 100%;
            overflow-x: auto;
        }

        .mensaje {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            background-color: #e7f4e9;
            color: #0f5132;
        }

        .btn-usuario {
            display: inline-block;
            padding: 0.45rem 0.75rem;
            margin: 0.2rem;
            border: none;
            border-radius: 5px;
            background-color: #0d6efd;
            color: #ffffff;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-usuario:hover {
            opacity: 0.85;
        }

        .btn-eliminar {
            background-color: #dc3545;
        }

        .btn-volver {
            background-color: #6c757d;
        }

        .acciones-usuario {
            min-width: 150px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Administración de usuarios</h1>

        <nav>
            <a href="../html/index.html">Inicio</a>
            <a href="../html/admin-dashboard.html">Dashboard administrativo</a>
            <a href="../php/cerrar_sesion.php">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <section class="bloque">
            <h2><?= $accion === 'editar' ? 'Editar usuario' : 'Registrar usuario' ?></h2>

            <?php if ($mensaje !== ''): ?>
                <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>

            <form action="usuarios.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($usuarioEditar['id']) ?>">

                <div class="campo">
                    <label for="nombre">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?= htmlspecialchars($usuarioEditar['nombre']) ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        value="<?= htmlspecialchars($usuarioEditar['correo']) ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        value="<?= htmlspecialchars($usuarioEditar['telefono']) ?>"
                    >
                </div>

                <div class="campo">
                    <label for="rol_id">Rol</label>
                    <select id="rol_id" name="rol_id" required>
                        <option value="1" <?= (int) $usuarioEditar['rol_id'] === 1 ? 'selected' : '' ?>>
                            Familiar
                        </option>
                        <option value="2" <?= (int) $usuarioEditar['rol_id'] === 2 ? 'selected' : '' ?>>
                            Administrador
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label for="password">
                        <?= $accion === 'editar'
                            ? 'Nueva contraseña (opcional)'
                            : 'Contraseña' ?>
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        <?= $accion === 'editar' ? '' : 'required' ?>
                    >
                </div>

                <button type="submit">
                    <?= $accion === 'editar' ? 'Guardar cambios' : 'Crear usuario' ?>
                </button>

                <?php if ($accion === 'editar'): ?>
                    <a class="btn-usuario btn-volver" href="usuarios.php">
                        Cancelar edición
                    </a>
                <?php endif; ?>
            </form>
        </section>

        <section class="bloque">
            <h2>Usuarios registrados</h2>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Fecha de creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($usuarios) === 0): ?>
                            <tr>
                                <td colspan="7">No hay usuarios registrados.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['id']) ?></td>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                                <td>
                                    <?= (int) $usuario['rol_id'] === 2
                                        ? 'Administrador'
                                        : 'Familiar' ?>
                                </td>
                                <td><?= htmlspecialchars($usuario['fecha_creacion']) ?></td>
                                <td class="acciones-usuario">
                                    <a
                                        class="btn-usuario"
                                        href="usuarios.php?accion=editar&id=<?= $usuario['id'] ?>"
                                    >
                                        Editar
                                    </a>

                                    <?php if ((int) $usuario['id'] !== (int) $_SESSION['usuario_id']): ?>
                                        <a
                                            class="btn-usuario btn-eliminar"
                                            href="usuarios.php?accion=eliminar&id=<?= $usuario['id'] ?>"
                                            onclick="return confirm('¿Desea eliminar este usuario?');"
                                        >
                                            Eliminar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            &copy; 2026 Centro Diurno Vida Activa — San José, Costa Rica.
            Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>