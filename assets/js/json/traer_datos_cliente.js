document.getElementById("buscar_cliente_btn").addEventListener("click", function(event) {
    event.preventDefault(); // Prevenir envío de formulario

    // Recolectar los datos del formulario
    let dni = document.getElementById("dni").value;
    let tipo_sexo_idtipo_sexo = document.getElementById("id_tipo_sexo").value;
    console.log("DNI:", dni);
    console.log("Tipo de Sexo:", tipo_sexo_idtipo_sexo);

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
        console.log("Estado de la respuesta:", response.status); // Verificar el código de estado
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.statusText);
        }
        return response.text(); // Obtener respuesta como texto para depuración
    })
    .then(text => {
        //console.log("Respuesta del servidor (texto):", text); // Mostrar la respuesta cruda
        let data;
        try {
            data = JSON.parse(text); // Intentar convertir la respuesta a JSON
            console.log("Datos JSON:", data);
        } catch (e) {
            console.error("La respuesta no es un JSON válido:", e);
            alert('El tipo de sexo es incorrecto.');
            return; // Salir de la función si hay error en el JSON
        }

        // Verificar si el servidor envió un error
        if (data.error) {
            alert(data.error); // Mostrar el mensaje de error
        } else {

            console.log("Campo id_documento:", document.getElementById("id_documento"));
            console.log("Campo id_domicilio:", document.getElementById("id_domicilio"));
            console.log("Campo id_contacto:", document.getElementById("id_contacto"));

            // Rellenar el formulario con los datos recibidos
            document.getElementById("id_nombre").value = data.persona?.nombre || '';
            document.getElementById("id_apellido").value = data.persona?.apellido || '';
            document.getElementById("id_fecha_nacimiento").value = data.persona?.fecha_nacimiento || '';
            document.getElementById("id_tipo_documento").value = data.documento?.idTipo_documento || '';
            document.getElementById("id_email").value = data.usuario?.email || '';
            document.getElementById("id_username").value = data.usuario?.username || '';
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

            // Guardar los IDs en campos ocultos
            document.getElementById("id_personas").value = data.persona?.idpersonas || ''; // Almacena el ID de persona
            document.getElementById("id_usuarios").value = data.usuario?.idusuarios || ''; // Almacena el ID de usuario
            document.getElementById("id_contactos").value = data.contacto?.idcontactos || ''; // Almacena el ID de contacto
            document.getElementById("id_domicilios").value = data.domicilio?.iddomicilios || ''; // Almacena el ID de domicilio
            document.getElementById("id_documentos").value = data.documento?.iddocumentos || ''; // Almacena el ID de documento

            // Cerrar el modal
            let modal = bootstrap.Modal.getInstance(document.getElementById("buscarClienteModal"));
            if (modal) {
                modal.hide();
            }

        }
    })
    .catch(error => {
        console.error("Error al buscar cliente:", error);
        alert('Hubo un error al procesar la solicitud.'); // Mensaje de alerta al usuario
    });

});







