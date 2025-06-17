// Variable global para almacenar los datos del vehículo y ficha técnica
let vehiculoData = null;
let fichaTecnicaData = null;

// Evento para buscar el vehículo al hacer clic en el botón "Buscar Auto"
document.getElementById("buscar_auto_btn").addEventListener("click", function() {
    let patente = document.getElementById("patente").value;
    console.log("Patente enviada:", patente);

    let formData = new FormData();
    formData.append("action", "consultar_auto");
    formData.append("patente", patente);

    // Primera solicitud fetch para obtener datos del vehículo
    fetch('controladores/ventas/ventas.controlador.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Respuesta completa del servidor:", data);

        if (data.error) {
            alert(data.error);
        } else {
            // Almacenar datos del vehículo
            vehiculoData = data.vehiculo || null;
            console.log("Rellenando datos del vehículo");

            if (vehiculoData) {
                document.getElementById("id_patente").value = vehiculoData.patente || '';
                document.getElementById("id_chasis").value = vehiculoData.chasis || '';
                document.getElementById("id_motor").value = vehiculoData.motor || '';
                document.getElementById("id_anio").value = vehiculoData.anio || '';
                document.getElementById("id_kilometraje").value = vehiculoData.kilometraje || '';
                document.getElementById("id_precio").value = data.precio?.precio || '';
                document.getElementById("idvehiculos_1").value = data.vehiculo.idvehiculos || '';

                // Actualizar selects
                document.getElementById("id_colores").value = vehiculoData.idcolores || '';
                document.getElementById("id_marcas").value = vehiculoData.idmarcas || '';
                document.getElementById("idmodelos").value = vehiculoData.idmodelos || '';
                document.getElementById("id_tipo_vehiculos").value = vehiculoData.idtipo_vehiculos || '';
            }

            let modalElement = document.getElementById("buscarAutoModal");
            let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);

            modal.hide();

            modalElement.addEventListener('hidden.bs.modal', () => {
                // Devuelve foco
                document.getElementById('buscar_auto_btn').focus();
                
                // Limpia scroll y backdrop
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            });
        }
    })
    .catch(error => {
        console.error("Error en la primera solicitud:", error);
    });
});

// Evento para el botón "Ver Ficha Técnica"
document.getElementById("verFichaTecnicaBtn").addEventListener("click", function() {
    if (vehiculoData) {
        console.log("Contenido de vehiculoData:", vehiculoData);
        console.log("ID del vehículo:", vehiculoData.idvehiculos);
        let formData = new FormData();
        formData.append("action", "consultar_ficha_tecnica");
        formData.append("id_vehiculo", vehiculoData.idvehiculos); // Usar el ID del vehículo para obtener la ficha técnica

        // Segunda solicitud fetch para obtener datos de la ficha técnica
        fetch('controladores/ventas/ventas.controlador.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Verificar si la respuesta es correcta
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.text(); // Cambiar a texto primero para depurar
        })
        .then(text => {
            console.log("Respuesta cruda del servidor:", text); // Ver qué se recibe
            try {
                const data = JSON.parse(text); // Intenta convertir a JSON
                console.log("Datos de ficha técnica:", data.ficha_tecnica); // Agrega este log para ver los datos
                if (data.error) {
                    alert(data.error);
                } else {
                    // Almacenar datos de la ficha técnica
                    fichaTecnicaData = data.ficha_tecnica || null;
                    console.log("Rellenando datos de la ficha técnica");

                    if (fichaTecnicaData) {
                        document.getElementById("vencimientoBateria").value = fichaTecnicaData.vencimiento_bateria.split(" ")[0] || '';
                        document.getElementById("vencimientoRTO").value = fichaTecnicaData.vencimiento_RTO.split(" ")[0] || '';
                        document.getElementById("vencimientoService").value = fichaTecnicaData.vencimiento_service.split(" ")[0] || '';

                        // Actualizar checkboxes
                        document.getElementById("switch08").checked = fichaTecnicaData.form_08 === "1";
                        document.getElementById("switch12").checked = fichaTecnicaData.form_12 === "1";
                        document.getElementById("switchTitulo").checked = fichaTecnicaData.titulo_vehiculo === "1";
                        document.getElementById("switchCedula").checked = fichaTecnicaData.cedula_vehiculo === "1";
                        document.getElementById("switchSeguro").checked = fichaTecnicaData.seguro === "1";
                        document.getElementById("switchMunicipalidad").checked = fichaTecnicaData.municipalidad === "1";
                        document.getElementById("switchDominio").checked = fichaTecnicaData.informe_dominio === "1";
                        document.getElementById("switchMultas").checked = fichaTecnicaData.form_13i === "1";
                        document.getElementById("switchPrenda").checked = fichaTecnicaData.prenda === "1";

                        // Actualizar selects
                        document.getElementById("descripcionCarroceria").value = fichaTecnicaData.carroceria_idcarroceria || '';
                        document.getElementById("descripcionNeumaticos").value = fichaTecnicaData.neumaticos_idneumaticos || '';
                        document.getElementById("descripcionCristales").value = fichaTecnicaData.cristales_idcristales || '';
                    }
                }
            } catch (e) {
                console.error("Error al analizar JSON:", e);
                console.error("Respuesta cruda del servidor que causó el error:", text); // Para ver la respuesta completa
            }
        })
        .catch(error => {
            console.error("Error en la segunda solicitud:", error);
        });
    } else {
        console.log("No hay datos del vehículo disponibles.");
    }
});



