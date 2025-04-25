<?php

include_once('../../modelos/tablas_maestras/provincia.php');


if (isset($_GET['paises_idpaises'])) {
    $paises_idpaises = $_GET['paises_idpaises'];
    $provincia = new Provincias();
    $result_provincia = $provincia->traer_provincias_por_pais($paises_idpaises);

    foreach ($result_provincia as $provincia) {
        echo "<option value='".$provincia['idprovincias']."'>".$provincia['nombre_provincia']."</option>";
    }
}
