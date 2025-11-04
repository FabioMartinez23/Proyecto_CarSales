<?php
require_once '../../modelos/conexion.php';
require_once '../../modelos/vehiculos.php';

$vehiculo = new Vehiculos();
$faltantes = $vehiculo->contarVehiculosConFaltante();

echo json_encode(['faltantes' => $faltantes]);