function validar_localidad(localidad_id) {
    var xhr = new XMLHttpRequest();
    var url = "controladores/mis_datos/get_barrios_por_localidad.php?localidades_idlocalidades="+ localidad_id;
    console.log(url);
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            document.getElementById("idbarrios").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}