<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once('../../modelos/vehiculos.php');
require_once('../../modelos/vehiculos_taller.php');

if (!isset($_GET['idvehiculo'])) {
    header('Location: ../../index.php?page=listado_vehiculos&mensaje=Vehículo no especificado.&status=error');
    exit;
}

$idvehiculo = (int) $_GET['idvehiculo'];

// 1) Cambiar estado del vehículo a TALLER
$vehiculo = new Vehiculos();
$vehiculo->setIdvehiculos($idvehiculo);

if (!$vehiculo->enviar_a_taller()) {
    header('Location: ../../index.php?page=listado_vehiculos&mensaje=' . urlencode('No se pudo cambiar el estado del vehículo a Taller.') . '&status=error');
    exit;
}

// 2) Crear registro en vehiculos_taller
$vt = new Vehiculos_Taller();
$usuarioIngreso = $_SESSION['idusuarios'] ?? null;

if (!$vt->registrar_ingreso($idvehiculo, $usuarioIngreso)) {
    header('Location: ../../index.php?page=listado_vehiculos&mensaje=' . urlencode('No se pudo registrar el ingreso del vehículo al taller.') . '&status=error');
    exit;
}

// 3) Todo OK
header('Location: ../../index.php?page=listado_vehiculos&mensaje=' . urlencode('Vehículo enviado a taller correctamente.') . '&status=success');
exit;

