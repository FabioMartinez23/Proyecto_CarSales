<?php
require_once __DIR__ . '/../../modelos/reportes_costos.php';

class CostosVehiculoControlador {

    public function procesar($idvehiculo) {

        $reporte = new ReportesCostos();
        $datos = $reporte->traer_costos_vehiculo($idvehiculo);

        return $datos;
    }
}

