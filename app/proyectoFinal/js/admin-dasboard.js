const citasAdmin = [
    { id: 1, adulto: "Carlos Gómez", familiar: "Ana Gómez", fecha: "2026-07-10", hora: "09:00", motivo: "Visita general", estado: "Pendiente" },
    { id: 2, adulto: "María López", familiar: "Luis López", fecha: "2026-07-11", hora: "10:30", motivo: "Reunión con trabajadora social", estado: "Confirmada" },
    { id: 3, adulto: "José Ramírez", familiar: "Pedro Ramírez", fecha: "2026-07-12", hora: "14:00", motivo: "Evaluación médica", estado: "Cancelada" }
];

const tablaAdmin = document.getElementById("tabla-admin-citas");
const totalCitasSpan = document.getElementById("total-citas");
const pendientesSpan = document.getElementById("citas-pendientes");
const confirmadasSpan = document.getElementById("citas-confirmadas");
const canceladasSpan = document.getElementById("citas-canceladas");

// Pintar tabla y resumen al cargar
pintarDashboard();

// Función principal
function pintarDashboard() {
    pintarTabla();
    actualizarResumen();
}

// Pintar las citas en la tabla con controles
function pintarTabla() {
    tablaAdmin.innerHTML = "";

    citasAdmin.forEach(function (cita, index) {
        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${cita.id}</td>
            <td>${cita.adulto}</td>
            <td>${cita.familiar}</td>
            <td>${cita.fecha}</td>
            <td>${cita.hora}</td>
            <td>${cita.motivo}</td>
            <td>${cita.estado}</td>
            <td>
                <button data-index="${index}" data-estado="Confirmada">Confirmar</button>
                <button data-index="${index}" data-estado="Cancelada">Cancelar</button>
                <button data-index="${index}" data-estado="Pendiente">Marcar pendiente</button>
            </td>
        `;

        tablaAdmin.appendChild(fila);
    });

    // Agregar eventos a los botones
    const botones = tablaAdmin.querySelectorAll("button");
    botones.forEach(function (btn) {
        btn.addEventListener("click", function () {
            const idx = parseInt(btn.getAttribute("data-index"), 10);
            const nuevoEstado = btn.getAttribute("data-estado");

            citasAdmin[idx].estado = nuevoEstado;
            pintarDashboard(); // Re-pinta tabla y resumen
        });
    });
}

// Actualizar los contadores del resumen
function actualizarResumen() {
    const total = citasAdmin.length;
    const pendientes = citasAdmin.filter(c => c.estado === "Pendiente").length;
    const confirmadas = citasAdmin.filter(c => c.estado === "Confirmada").length;
    const canceladas = citasAdmin.filter(c => c.estado === "Cancelada").length;

    totalCitasSpan.textContent = total;
    pendientesSpan.textContent = pendientes;
    confirmadasSpan.textContent = confirmadas;
    canceladasSpan.textContent = canceladas;
}