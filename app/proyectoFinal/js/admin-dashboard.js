document.addEventListener("DOMContentLoaded", async () => {
    const tbody = document.querySelector("#tabla-citas tbody");
    const totalSpan = document.querySelector("#total-citas");
    const pendientesSpan = document.querySelector("#pendientes");
    const confirmadasSpan = document.querySelector("#confirmadas");
    const canceladasSpan = document.querySelector("#canceladas");

    try {
        const respuesta = await fetch("../php/admin-dashboard.php");
        const data = await respuesta.json();

        if (!data.ok) {
            tbody.innerHTML = `<tr><td colspan="8">Error: ${data.error}</td></tr>`;
            return;
        }

        const citas = data.citas;

        if (citas.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8">No hay citas registradas.</td></tr>`;
            totalSpan.textContent = "0";
            pendientesSpan.textContent = "0";
            confirmadasSpan.textContent = "0";
            canceladasSpan.textContent = "0";
            return;
        }

        let pendientes = 0;
        let confirmadas = 0;
        let canceladas = 0;

        tbody.innerHTML = "";

        citas.forEach(cita => {
            if (cita.estado === "pendiente") pendientes++;
            if (cita.estado === "confirmada") confirmadas++;
            if (cita.estado === "cancelada") canceladas++;

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${cita.id}</td>
                <td>${cita.adulto_id}</td>
                <td>${cita.familiar_id}</td>
                <td>${cita.fecha}</td>
                <td>${cita.hora}</td>
                <td>${cita.motivo}</td>
                <td>${cita.estado}</td>
                <td>
                    <!-- aquí podrías luego poner botones de acción -->
                </td>
            `;
            tbody.appendChild(tr);
        });

        totalSpan.textContent = citas.length;
        pendientesSpan.textContent = pendientes;
        confirmadasSpan.textContent = confirmadas;
        canceladasSpan.textContent = canceladas;
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8">Error al cargar citas: ${err}</td></tr>`;
    }
});