document.addEventListener("DOMContentLoaded", function () {
    const enlaceSesion = document.getElementById("enlace-sesion");

    if (!enlaceSesion) {
        return;
    }

    const usuarioNombre = localStorage.getItem("usuario_nombre");

    if (usuarioNombre) {
        enlaceSesion.textContent = "Cerrar sesión";
        enlaceSesion.href = "../php/cerrar_sesion.php";
    } else {
        enlaceSesion.textContent = "Iniciar sesión";
        enlaceSesion.href = "login.html";
    }
});