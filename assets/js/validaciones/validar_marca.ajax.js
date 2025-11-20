function validar_marca(marca_id) {

    // Obtener los datos actuales
    const modeloActual = document.getElementById("idmodelos").getAttribute("data-modelo-actual");

    var xhr = new XMLHttpRequest();
    var url = "controladores/vehiculos/get_modelos_por_marca.php?marcas_idmarcas=" + marca_id;

    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {

            let contenedor = document.getElementById("idmodelos");
            contenedor.innerHTML = xhr.responseText;

            // Si estamos editando y el modelo pertenece a esta marca → seleccionarlo
            if (modeloActual) {
                let option = contenedor.querySelector(`option[value="${modeloActual}"]`);
                if (option) {
                    option.selected = true;
                }
            }
        }
    };
    xhr.send();
}

