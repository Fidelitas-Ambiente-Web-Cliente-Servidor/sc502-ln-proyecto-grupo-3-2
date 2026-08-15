<?php
session_start();

session_unset();
session_destroy();

echo "<script>
        localStorage.removeItem('usuario_nombre');
        localStorage.removeItem('usuario_rol');
        window.location.href = '../html/index.html';
      </script>";
exit;
?>