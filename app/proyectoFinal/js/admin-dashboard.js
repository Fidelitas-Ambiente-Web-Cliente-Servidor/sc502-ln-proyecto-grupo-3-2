document.addEventListener("DOMContentLoaded", function () {
    cargarDashboard();
});

async function cargarDashboard() {
    const cuerpoCitas = document.querySelector("#tabla-citas tbody");
    const cuerpoConsultas = document.querySelector("#tabla-consultas tbody");

    try {
        const respuesta = await fetch("../php/admin-dashboard.php");
        const data = await respuesta.json();

        if (!data.ok) {
            cuerpoCitas.innerHTML =
                `<tr><td colspan="8">Error: ${escapeHtml(data.error)}</td></tr>`;

            cuerpoConsultas.innerHTML =
                `<tr><td colspan="8">Error: ${escapeHtml(data.error)}</td></tr>`;

            return;
        }

        pintarCitas(data.citas);
        pintarConsultas(data.consultas);

    } catch (error) {
        cuerpoCitas.innerHTML =
            "<tr><td colspan='8'>No fue posible cargar las citas.</td></tr>";

        cuerpoConsultas.innerHTML =
            "<tr><td colspan='8'>No fue posible cargar las consultas.</td></tr>";
    }
}

function pintarCitas(citas) {
    const tbody = document.querySelector("#tabla-citas tbody");

    let pendientes = 0;
    let confirmadas = 0;
    let realizadas = 0;
    let canceladas = 0;

    tbody.innerHTML = "";

    if (citas.length === 0) {
        tbody.innerHTML =
            "<tr><td colspan='8'>No hay citas registradas.</td></tr>";
    }

    citas.forEach(function (cita) {
        if (cita.estado === "pendiente") pendientes++;
        if (cita.estado === "confirmada") confirmadas++;
        if (cita.estado === "realizada") realizadas++;
        if (cita.estado === "cancelada") canceladas++;

        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${escapeHtml(cita.id)}</td>
            <td>${escapeHtml(cita.adulto_id)}</td>
            <td>${escapeHtml(cita.familiar_id)}</td>
            <td>${escapeHtml(cita.fecha)}</td>
            <td>${escapeHtml(cita.hora)}</td>
            <td>${escapeHtml(cita.motivo)}</td>
            <td>
                <span class="estado ${escapeHtml(cita.estado)}">
                    ${escapeHtml(cita.estado)}
                </span>
            </td>
            <td>${crearAccionesCita(cita)}</td>
        `;

        tbody.appendChild(fila);
    });

    document.getElementById("total-citas").textContent = citas.length;
    document.getElementById("pendientes").textContent = pendientes;
    document.getElementById("confirmadas").textContent = confirmadas;
    document.getElementById("realizadas").textContent = realizadas;
    document.getElementById("canceladas").textContent = canceladas;

    document.querySelectorAll(".btn-cita").forEach(function (boton) {
        boton.addEventListener("click", function () {
            actualizarEstado(
                "cita",
                this.dataset.id,
                this.dataset.estado,
                this.dataset.estadoActual
            );
        });
    });
}

function crearAccionesCita(cita) {
    const id = escapeHtml(cita.id);
    const estado = escapeHtml(cita.estado);

    if (cita.estado === "pendiente") {
        return `
            <div class="acciones">
                <button class="btn-accion btn-confirmar btn-cita"
                    data-id="${id}"
                    data-estado="confirmada"
                    data-estado-actual="${estado}">
                    Confirmar
                </button>

                <button class="btn-accion btn-realizada btn-cita"
                    data-id="${id}"
                    data-estado="realizada"
                    data-estado-actual="${estado}">
                    Realizada
                </button>

                <button class="btn-accion btn-cancelar btn-cita"
                    data-id="${id}"
                    data-estado="cancelada"
                    data-estado-actual="${estado}">
                    Cancelar
                </button>
            </div>
        `;
    }

    if (cita.estado === "confirmada") {
        return `
            <div class="acciones">
                <button class="btn-accion btn-realizada btn-cita"
                    data-id="${id}"
                    data-estado="realizada"
                    data-estado-actual="${estado}">
                    Realizada
                </button>

                <button class="btn-accion btn-cancelar btn-cita"
                    data-id="${id}"
                    data-estado="cancelada"
                    data-estado-actual="${estado}">
                    Cancelar
                </button>
            </div>
        `;
    }

    return "<span class='procesada'>Procesada</span>";
}

function pintarConsultas(consultas) {
    const tbody = document.querySelector("#tabla-consultas tbody");

    let pendientes = 0;
    let atendidas = 0;

    tbody.innerHTML = "";

    if (consultas.length === 0) {
        tbody.innerHTML =
            "<tr><td colspan='8'>No hay consultas registradas.</td></tr>";
    }

    consultas.forEach(function (consulta) {
        if (consulta.estado === "pendiente") pendientes++;
        if (consulta.estado === "atendida") atendidas++;

        const fila = document.createElement("tr");

        fila.innerHTML = `
            <td>${escapeHtml(consulta.id)}</td>
            <td>${escapeHtml(consulta.nombre)}</td>
            <td>${escapeHtml(consulta.correo)}</td>
            <td>${escapeHtml(consulta.telefono)}</td>
            <td class="mensaje-consulta">${escapeHtml(consulta.mensaje)}</td>
            <td>${formatearFecha(consulta.fecha_creacion)}</td>
            <td>
                <span class="estado ${escapeHtml(consulta.estado)}">
                    ${escapeHtml(consulta.estado)}
                </span>
            </td>
            <td>${crearAccionesConsulta(consulta)}</td>
        `;

        tbody.appendChild(fila);
    });

    document.getElementById("total-consultas").textContent = consultas.length;
    document.getElementById("consultas-pendientes").textContent = pendientes;
    document.getElementById("consultas-atendidas").textContent = atendidas;

    document.querySelectorAll(".btn-consulta").forEach(function (boton) {
        boton.addEventListener("click", function () {
            actualizarEstado(
                "consulta",
                this.dataset.id,
                this.dataset.estado,
                this.dataset.estadoActual
            );
        });
    });
}

function crearAccionesConsulta(consulta) {
    const id = escapeHtml(consulta.id);
    const estado = escapeHtml(consulta.estado);

    if (consulta.estado === "pendiente") {
        return `
            <div class="acciones">
                <button class="btn-accion btn-atender btn-consulta"
                    data-id="${id}"
                    data-estado="atendida"
                    data-estado-actual="${estado}">
                    Marcar atendida
                </button>
            </div>
        `;
    }

    return `
        <div class="acciones">
            <button class="btn-accion btn-pendiente btn-consulta"
                data-id="${id}"
                data-estado="pendiente"
                data-estado-actual="${estado}">
                Marcar pendiente
            </button>
        </div>
    `;
}

async function actualizarEstado(tipo, id, nuevoEstado, estadoActual) {
    const confirmar = confirm(
        `¿Desea cambiar el estado de "${estadoActual}" a "${nuevoEstado}"?`
    );

    if (!confirmar) {
        return;
    }

    try {
        const respuesta = await fetch("../php/admin-dashboard.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                tipo: tipo,
                id: id,
                estado: nuevoEstado
            })
        });

        const data = await respuesta.json();

        if (!data.ok) {
            alert(data.error || "No fue posible actualizar el estado.");
            return;
        }

        cargarDashboard();

    } catch (error) {
        alert("Ocurrió un error al actualizar el estado.");
    }
}

function formatearFecha(fecha) {
    if (!fecha) {
        return "";
    }

    const fechaConvertida = new Date(fecha.replace(" ", "T"));

    if (isNaN(fechaConvertida.getTime())) {
        return fecha;
    }

    return fechaConvertida.toLocaleString("es-CR");
}

function escapeHtml(valor) {
    const elemento = document.createElement("div");
    elemento.textContent = valor ?? "";
    return elemento.innerHTML;
}