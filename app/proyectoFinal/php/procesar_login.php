<?php
require_once 'conexion.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $sql = 'SELECT id, nombre, correo, password, rol_id
                FROM usuarios
                WHERE correo = :correo';

        $sentencia = $conexion->prepare($sql);

        $sentencia->execute([
            ':correo' => $correo
        ]);

        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol_id'];

            $nombre = htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8');
            $rol = (int) $usuario['rol_id'];

            echo "<script>
                    localStorage.setItem('usuario_nombre', '$nombre');
                    localStorage.setItem('usuario_rol', '$rol');
                    window.location.href = '../html/index.html';
                  </script>";
            exit;
        }

        echo "<script>
                alert('Correo o contraseña incorrectos.');
                window.history.back();
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Ocurrió un error al iniciar sesión.');
                window.history.back();
              </script>";
    }
}
?>