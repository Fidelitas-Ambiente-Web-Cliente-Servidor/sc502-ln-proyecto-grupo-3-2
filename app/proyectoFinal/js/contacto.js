// Arreglo para guardar los mensajes en memoria (siguiendo tu ejemplo)
const mensajes = [];

// Referencias al formulario y a la tabla de contactos
const formContacto = document.querySelector(".contact-form-wrapper form");
// Nota: Para que funcione la tabla, debes tener un <tbody> con id="tabla-mensajes" en tu HTML
const tablaMensajes = document.getElementById("tabla-mensajes");

// Escuchamos el envío del formulario
formContacto.addEventListener("submit", function (event) {
    event.preventDefault();

    // Capturamos los datos usando los IDs del formulario de contacto
    const nombre = document.getElementById("nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const mensaje = document.getElementById("mensaje").value.trim();

    // Validación simple exactamente igual a la tuya
    if (!nombre || !correo || !telefono || !mensaje) {
        alert("Por favor complete todos los campos.");
        return;
    }

    // Crear un id dinámico basado en la cantidad de elementos
    const id = mensajes.length + 1;

    // Crear el objeto con la estructura de contacto
    const nuevoMensaje = {
        id,
        nombre,
        correo,
        telefono,
        mensaje,
        fechaRegistro: new Date().toLocaleDateString() // Guarda la fecha del día de hoy
    };

    // Guardar en el arreglo en memoria
    mensajes.push(nuevoMensaje);

    // Dar retroalimentación al usuario
    alert(`¡Gracias ${nombre}! Tu mensaje ha sido registrado.`);

    // Actualizar la tabla si existe en el HTML
    if (tablaMensajes) {
        pintarMensajes();
    }

    // Limpiar el formulario
    formContacto.reset();
});

// Función para pintar los mensajes recibidos en la tabla
function pintarMensajes() {
    tablaMensajes.innerHTML = "";

    mensajes.forEach(function (item) {
        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${item.nombre}</td>
            <td>${item.correo}</td>
            <td>${item.telefono}</td>
            <td>${item.mensaje}</td>
            <td>${item.fechaRegistro}</td>
        `;

        tablaMensajes.appendChild(fila);
    });
}