function validar_provincia(provincia_id) {
    var xhr = new XMLHttpRequest();
    var url = "../controladores/domicilios/get_localidades_por_provincia.php?provincias_idprovincias="+ provincia_id;
    console.log(url);
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            document.getElementById("idlocalidades").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}