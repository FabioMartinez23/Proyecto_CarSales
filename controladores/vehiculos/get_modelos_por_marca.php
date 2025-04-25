<?php

include_once('../../modelos/tablas_maestras/modelo_vehiculo.php');


if (isset($_GET['marcas_idmarcas'])) {
    $marcas_idmarcas = $_GET['marcas_idmarcas'];
    $modelo = new Modelos_Vehiculos();
    $result_modelo = $modelo->traer_modelos_por_marca($marcas_idmarcas);

    foreach ($result_modelo as $modelo) {
        echo "<option value='".$modelo['idmodelos']."'>".$modelo['nombre_modelo']."</option>";
    }
}



