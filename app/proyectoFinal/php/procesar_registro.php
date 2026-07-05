<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $rol_id = $_POST['rol_id'];

    $sql = "INSERT INTO usuarios (nombre_completo, correo, password, rol_id) 
            VALUES ('$nombre', '$correo', '$password', '$rol_id')";
    
    if ($conexion->query($sql) === TRUE) {
        // Alerta tradicional de clase y redirección al login.html
        echo "<script>
                alert('¡Usuario registrado con éxito!');
                window.location.href = '../html/login.html';
              </script>";
    } else {
        echo "Error al registrar: " . $conexion->error;
    }
}
?>