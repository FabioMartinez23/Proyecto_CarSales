<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verificar que se haya enviado el parámetro idmarcas
if (isset($_POST['idmarcas'])) {
    // Incluir el archivo donde se define la función traer_modelo_por_marca
    require_once($_SERVER['DOCUMENT_ROOT'] . '/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02/modelos/tablas_maestras/modelo_vehiculo.php');

    $idmarcas = $_POST['idmarcas'];

    // Llamar a la función PHP que obtiene los modelos por marca
    $modelo = new Modelos_Vehiculos();
    $modelos = $modelo->traer_modelos_por_marca($idmarcas);

    // Devolver los resultados en formato JSON
    echo json_encode($modelos);
}
?>
