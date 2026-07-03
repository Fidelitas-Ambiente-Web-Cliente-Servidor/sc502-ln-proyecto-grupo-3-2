// Arreglo para guardar las citas en memoria (por ahora solo frontend)
const citas = [];

// Referencias al formulario y a la tabla
const formCita = document.getElementById("form-cita");
const tablaCitas = document.getElementById("tabla-citas");

// Escuchamos el envío del formulario
formCita.addEventListener("submit", function (event) {
    event.preventDefault();

    const adulto = document.getElementById("adulto").value.trim();
    const familiar = document.getElementById("familiar").value.trim();
    const fecha = document.getElementById("fecha").value;
    const hora = document.getElementById("hora").value;
    const motivo = document.getElementById("motivo").value.trim();

    // Validación simple
    if (!adulto || !familiar || !fecha || !hora || !motivo) {
        alert("Por favor complete todos los campos.");
        return;
    }

    // Crear un id simple (basado en cantidad de citas)
    const id = citas.length + 1;

    // Crear objeto cita
    const nuevaCita = {
        id,
        adulto,
        familiar,
        fecha,
        hora,
        motivo,
        estado: "Pendiente"
    };

    // Guardar en el arreglo
    citas.push(nuevaCita);

    // Actualizar la tabla
    pintarCitas();

    // Limpiar el formulario
    formCita.reset();
});

// Función para pintar las citas en la tabla
function pintarCitas() {
    tablaCitas.innerHTML = "";

    citas.forEach(function (cita) {
        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${cita.adulto}</td>
            <td>${cita.familiar}</td>
            <td>${cita.fecha}</td>
            <td>${cita.hora}</td>
            <td>${cita.motivo}</td>
            <td>${cita.estado}</td>
        `;

        tablaCitas.appendChild(fila);
    });
}