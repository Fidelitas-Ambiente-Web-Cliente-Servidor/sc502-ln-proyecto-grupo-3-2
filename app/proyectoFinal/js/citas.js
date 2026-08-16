$(document).ready(function () {
    cargarAdultos();
    cargarMisCitas();

    $("#form-cita").on("submit", function (event) {
        let adulto = $("#adulto_id").val();
        let fecha = $("#fecha").val();
        let hora = $("#hora").val();
        let motivo = $("#motivo").val().trim();

        if (adulto === "" || fecha === "" || hora === "" || motivo === "") {
            event.preventDefault();
            alert("Por favor complete todos los campos.");
        }
    });
});

function cargarAdultos() {
    let selectAdulto = $("#adulto_id");

    $.ajax({
        url: "../php/obtener_adultos.php",
        type: "GET",
        dataType: "json",

        success: function (adultos) {
            selectAdulto.empty();

            selectAdulto.append(
                '<option value="">Seleccione un adulto mayor</option>'
            );

            if (adultos.length === 0) {
                selectAdulto.append(
                    '<option value="">No hay adultos mayores asociados</option>'
                );
                return;
            }

            adultos.forEach(function (adulto) {
                selectAdulto.append(
                    '<option value="' + adulto.id + '">' +
                    adulto.nombre_completo +
                    "</option>"
                );
            });
        },

        error: function () {
            selectAdulto.empty();

            selectAdulto.append(
                '<option value="">No se pudieron cargar los adultos mayores</option>'
            );
        }
    });
}

function cargarMisCitas() {
    let contenedor = $("#contenedorMisCitas");

    $.ajax({
        url: "../php/obtener_mis_citas.php",
        type: "GET",
        dataType: "json",

        success: function (citas) {
            if (citas.error) {
                contenedor.html("<p>No se pudieron cargar las citas.</p>");
                return;
            }

            if (citas.length === 0) {
                contenedor.html("<p>No tienes citas agendadas.</p>");
                return;
            }

            let tabla = `
                <div class="tabla-responsive">
                    <table class="tabla-citas">
                        <thead>
                            <tr>
                                <th>Adulto mayor</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            citas.forEach(function (cita) {
                tabla += `
                    <tr>
                        <td>${cita.adulto_mayor}</td>
                        <td>${cita.fecha}</td>
                        <td>${cita.hora}</td>
                        <td>${cita.motivo}</td>
                        <td>${cita.estado}</td>
                    </tr>
                `;
            });

            tabla += `
                        </tbody>
                    </table>
                </div>
            `;

            contenedor.html(tabla);
        },

        error: function () {
            contenedor.html("<p>No se pudieron cargar las citas.</p>");
        }
    });
}