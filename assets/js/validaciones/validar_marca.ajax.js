function validar_marca(marca_id) {
    var xhr = new XMLHttpRequest();
    var url = "controladores/vehiculos/get_modelos_por_marca.php?marcas_idmarcas="+ marca_id;
    console.log(url);
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            document.getElementById("idmodelos").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
