<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../modelos/vehiculos.php');

if (isset($_POST['patente']) && !empty(trim($_POST['patente']))) {
    $patente = trim($_POST['patente']);
    $datos_vehiculo = new Vehiculos();
    $datos = $datos_vehiculo->traer_vehiculo_por_patente_ventas($patente);

    // Convertir null en array vacío para compatibilidad con JS
    echo json_encode($datos ? [$datos] : []);
} else {
    echo json_encode([]); // ← muy importante para evitar errores
}
?>
