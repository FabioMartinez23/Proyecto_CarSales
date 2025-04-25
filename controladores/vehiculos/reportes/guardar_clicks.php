<?php
require_once('../../../modelos/reportes/reporte_consulta_vehiculos.php');

// Configuración para capturar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// Capturar y limpiar la salida para evitar contenido no deseado
ob_start();

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $idvehiculo = $data['idvehiculos'] ?? null;
    if (!$idvehiculo || !is_numeric($idvehiculo)) {
        throw new Exception("El ID del vehículo es inválido o falta.");
    }

    $fecha_actual = date('Y-m-d');

    $reporte = new ReporteConsultaVehiculo();
    $reporte->setFecha_consulta($fecha_actual);
    $reporte->setVehiculos_idvehiculos($idvehiculo);

    $resultado = $reporte->guardar_clicks();
    if (!$resultado) {
        throw new Exception("No se pudo guardar el clic en la base de datos.");
    }

    $response = ['success' => true];
} catch (Exception $e) {
    error_log("Error en guardar_clicks.php: " . $e->getMessage());
    $response = ['error' => $e->getMessage()];
}

// Limpiar cualquier salida previa y enviar el JSON
ob_end_clean();
echo json_encode($response);
exit;

