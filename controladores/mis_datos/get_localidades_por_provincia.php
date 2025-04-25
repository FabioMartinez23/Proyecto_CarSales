<?php

include_once('../../modelos/tablas_maestras/localidad.php');


if (isset($_GET['provincias_idprovincias'])) {
    $provincias_idprovincias = $_GET['provincias_idprovincias'];
    $localidad = new Localidades();
    $result_localidad = $localidad->traer_localidades_por_provincia($provincias_idprovincias);

    foreach ($result_localidad as $localidad) {
        echo "<option value='".$localidad['idlocalidades']."'>".$localidad['nombre_localidad']."</option>";
    }
}