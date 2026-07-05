<?php
include 'conexion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "SELECT id, nombre_completo, rol_id FROM usuarios WHERE correo = '$correo' AND password = '$password'";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
        $_SESSION['usuario_rol'] = $usuario['rol_id'];

        // Redirecciona saliendo a html/ según el rol
        if ($_SESSION['usuario_rol'] == 1) {
            header("Location: ../html/admin-dashboard.html");
        } else {
            header("Location: ../html/citas.html");
        }
        exit();
    } else {
        echo "<script>
                alert('Correo o contraseña incorrectos.');
                window.history.back();
              </script>";
    }
}
?>