document.getElementById("buscar_cliente_btn").addEventListener("click", function(event) {
    event.preventDefault(); // Prevenir envío de formulario

    // Recolectar los datos del formulario
    let dni = document.getElementById("dni").value;
    let tipo_sexo_idtipo_sexo = document.getElementById("id_tipo_sexo").value;

    // Crear un objeto FormData
    let formData = new FormData();
    formData.append("action", "consultar_usuario");
    formData.append("dni", dni);
    formData.append("tipo_sexo_idtipo_sexo", tipo_sexo_idtipo_sexo);

    fetch('controladores/ventas/ventas.controlador.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.statusText);
        }
        return response.text(); // Obtener respuesta como texto para depuración
    })
    .then(text => {
        let data;
        try {
            data = JSON.parse(text); // Intentar convertir la respuesta a JSON
        } catch (e) {
            console.error("La respuesta no es un JSON válido:", e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El tipo de sexo es incorrecto.'
            });
            return; // Salir de la función si hay error en el JSON
        }

        // Verificar si el servidor envió un error
        if (data.error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Cliente encontrado',
                text: 'La información se cargó correctamente',
                timer: 3000,
                showConfirmButton: false
            });


            // Rellenar el formulario con los datos recibidos
            document.getElementById("id_personas").value = data.persona?.idpersonas || '';
            document.getElementById("id_nombre").value = data.persona?.nombre || '';
            document.getElementById("id_apellido").value = data.persona?.apellido || '';      

            document.getElementById("id_tipo_sexo_1").value = data.persona?.tipo_sexo_idtipo_sexo || '';

            document.getElementById("id_fecha_nacimiento").value = data.persona?.fecha_nacimiento || '';
            document.getElementById("id_tipo_documento").value = data.documento?.idTipo_documento || '';
            document.getElementById("id_tipo_contacto").value = data.contacto?.idtipo_contacto || '';
            document.getElementById("id_tipo_domicilio").value = data.domicilio?.idtipo_domicilio || '';
            document.getElementById("idbarrios").value = data.domicilio?.idbarrios || '';
            document.getElementById("idlocalidades").value = data.domicilio?.idlocalidades || '';
            document.getElementById("idprovincias").value = data.domicilio?.idprovincias || '';
            document.getElementById("idpaises").value = data.domicilio?.idpaises || '';

            if (data.documento && data.documento.valor) {
                document.getElementById("id_documento").value = data.documento.valor;
            } else {
                console.error("El valor de documento no está disponible");
            }
            
            if (data.domicilio && data.domicilio.nombre_domicilio) {
                document.getElementById("id_domicilio").value = data.domicilio.nombre_domicilio;
            } else {
                console.error("El valor de domicilio no está disponible");
            }
            
            if (data.contacto && data.contacto.valor) {
                document.getElementById("id_contacto").value = data.contacto.valor;
            } else {
                console.error("El valor de contacto no está disponible");
            }

            // Cerrar el modal
            let modalElement = document.getElementById("buscarClienteModal");
            let modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
            document.activeElement.blur(); // opcional: quitar foco para evitar warnings
            modal.hide(); // ✅ correcta forma de cerrar
            }
        }
    })
    .catch(error => {
        console.error("Error al buscar cliente:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al procesar la solicitud.'
        });
    });

});
