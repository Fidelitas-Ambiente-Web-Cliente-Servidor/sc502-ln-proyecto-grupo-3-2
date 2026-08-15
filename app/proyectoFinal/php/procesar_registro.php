<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $rol_id = 1;

    if ($nombre === '' || $correo === '' || $password === '') {
        die('Debe completar nombre, correo y contraseña.');
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die('El correo electrónico no tiene un formato válido.');
    }

    try {
        $verificar = $conexion->prepare(
            'SELECT id FROM usuarios WHERE correo = :correo'
        );

        $verificar->execute([
            ':correo' => $correo
        ]);

        if ($verificar->fetch()) {
            die('Ese correo ya está registrado.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO usuarios (nombre, correo, password, telefono, rol_id)
                VALUES (:nombre, :correo, :password, :telefono, :rol_id)';

        $sentencia = $conexion->prepare($sql);

        $sentencia->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':password' => $passwordHash,
            ':telefono' => $telefono !== '' ? $telefono : null,
            ':rol_id' => $rol_id
        ]);

        echo "<script>
                alert('¡Cuenta creada con éxito! Ahora puede iniciar sesión.');
                window.location.href = '../html/login.html';
              </script>";
        exit;
    } catch (PDOException $e) {
        die('No fue posible registrar la cuenta.');
    }
}
?>