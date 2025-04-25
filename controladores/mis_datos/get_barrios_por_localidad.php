<?php

include_once('../../modelos/tablas_maestras/barrio.php');


if (isset($_GET['localidades_idlocalidades'])) {
    $localidades_idlocalidades = $_GET['localidades_idlocalidades'];
    $barrio = new Barrios();
    $result_barrio = $barrio->traer_barrios_por_localidad($localidades_idlocalidades);

    foreach ($result_barrio as $barrio) {
        echo "<option value='".$barrio['idbarrios']."'>".$barrio['nombre_barrio']."</option>";
    }
}