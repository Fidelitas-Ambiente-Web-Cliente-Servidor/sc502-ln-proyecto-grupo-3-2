// Imagen inicial de la página
let contenido = document.getElementById("contenido");
let imagen = document.createElement("img");

imagen.src = "../imagen/imagenIndex1.jpg";
imagen.alt = "Foto del centro diurno Vida Activa";
imagen.className = "hero__photo";

contenido.appendChild(imagen);

// Datos guardados cuando el usuario inicia sesión
let nombreUsuario = localStorage.getItem("usuario_nombre");
let rolUsuario = localStorage.getItem("usuario_rol");

// Elementos del menú
let enlaceLogin = document.getElementById("enlace-login");
let menuInscripcion = document.getElementById("menu-inscripcion");

// Tarjetas de accesos rápidos
let accesoCitas = document.getElementById("acceso-citas");
let accesoDashboard = document.getElementById("acceso-dashboard");
let accesoInscripcion = document.getElementById("acceso-inscripcion");
let accesoActividades = document.getElementById("acceso-actividades");
let accesoSolicitudes = document.getElementById("acceso-solicitudes");

// Oculta los accesos privados al cargar la página
accesoCitas.style.display = "none";
accesoDashboard.style.display = "none";
accesoInscripcion.style.display = "none";
accesoActividades.style.display = "none";
accesoSolicitudes.style.display = "none";

menuInscripcion.style.display = "none";

// Si hay una persona con sesión guardada
if (rolUsuario !== null) {
enlaceLogin.textContent = "Cerrar sesión";
enlaceLogin.href = "../php/cerrar_sesion.php";

    // Opciones para familiar: rol 1
    if (rolUsuario === "1") {
        accesoCitas.style.display = "block";
        accesoActividades.style.display = "block";
        accesoSolicitudes.style.display = "block";
    }

    // Opciones para administrador: rol 2
    if (rolUsuario === "2") {
        accesoCitas.style.display = "block";
        accesoDashboard.style.display = "block";
        accesoInscripcion.style.display = "block";
        accesoActividades.style.display = "block";
        accesoSolicitudes.style.display = "block";

        menuInscripcion.style.display = "inline-block";
    }
}