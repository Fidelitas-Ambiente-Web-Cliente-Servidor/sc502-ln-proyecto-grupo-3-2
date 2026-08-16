document.addEventListener("DOMContentLoaded", function () {
    const formContacto = document.getElementById("form-contacto");

    if (!formContacto) {
        return;
    }

    formContacto.addEventListener("submit", function (event) {
        const nombre = document.getElementById("nombre").value.trim();
        const correo = document.getElementById("correo").value.trim();
        const telefono = document.getElementById("telefono").value.trim();
        const mensaje = document.getElementById("mensaje").value.trim();

        if (!nombre || !correo || !telefono || !mensaje) {
            event.preventDefault();
            alert("Por favor complete todos los campos.");
        }
    });
});