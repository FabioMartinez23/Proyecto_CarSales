function validar_pais(pais_id) {
    var xhr = new XMLHttpRequest();
    var url = "../controladores/domicilios/get_provincias_por_pais.php?paises_idpaises="+ pais_id;
    console.log(url);
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            console.log(xhr.responseText);
            document.getElementById("idprovincias").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
