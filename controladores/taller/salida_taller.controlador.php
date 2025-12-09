<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once('../../modelos/vehiculos_taller.php');
require_once('../../modelos/vehiculos.php');
require_once('../../modelos/documentos.php');

// Validar método y datos
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idvehiculos_taller'])) {
    header('Location: ../../index.php?page=listado_vehiculos_taller&mensaje=' . urlencode('Solicitud inválida para salida de taller.') . '&status=error');
    exit;
}

$idvehiculos_taller = (int)$_POST['idvehiculos_taller'];
$idusuario          = $_SESSION['idusuarios'] ?? null;

$km_salida        = $_POST['km_salida'] ?? null;
$trabajo_realizado = $_POST['trabajo_realizado'] ?? null;

// 1) Traer datos del registro en taller
$vtModel  = new Vehiculos_Taller();
$registro = $vtModel->traer_por_id($idvehiculos_taller);

if (!$registro) {
    header('Location: ../../index.php?page=listado_vehiculos_taller&mensaje=' . urlencode('No se encontró el registro de taller.') . '&status=error');
    exit;
}

if ($registro['estado_taller'] !== 'en_proceso') {
    header('Location: ../../index.php?page=listado_vehiculos_taller&mensaje=' . urlencode('Este vehículo ya fue dado de salida del taller.') . '&status=warning');
    exit;
}

$idvehiculo = (int)$registro['vehiculos_idvehiculos'];

// 2) Evaluar documentación para decidir estado final
$docModel    = new Documentacion();
$estadoFinal = $docModel->determinar_estado_para_vehiculo($idvehiculo);
// 'falta_documento' | 'falta_digitalizacion' | 'disponible'

// 3) Actualizar estado del vehículo
$vehModel = new Vehiculos();
$vehModel->actualizar_disponible($idvehiculo, $estadoFinal);

// 4) Marcar salida del taller con km y descripción de trabajo
$vtModel->finalizar_taller(
    $idvehiculos_taller,
    $km_salida,
    $trabajo_realizado,
    $idusuario
);

// 5) Redirigir
$mensaje = "Vehículo salió del taller. Estado final: $estadoFinal.";
header('Location: ../../index.php?page=listado_vehiculos_taller&mensaje=' . urlencode($mensaje) . '&status=success');
exit;
