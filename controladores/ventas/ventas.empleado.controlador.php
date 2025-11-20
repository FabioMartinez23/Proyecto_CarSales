<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../../modelos/vender_vehiculos.php'); // ESTE ES TU VenderVehiculo.php

header("Content-Type: application/json");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$ventas_controlador = new VentasControlador();

if (isset($_POST['action'])) {

    switch ($_POST['action']) {

        case 'ventas_empleado_mes':
            $ventas_controlador->ventas_empleado_mes();
            break;

        case 'ventas_empleado_total':
            $ventas_controlador->ventas_empleado_total();
            break;

        case 'ventas_empleado_anuladas':
            $ventas_controlador->ventas_empleado_anuladas();
            break;

        default:
            echo json_encode(['error' => 'Acción no reconocida']);
    }
}



class VentasControlador {

    public function ventas_empleado_mes() {

        $idusuario = $_POST['idusuario'] ?? null;

        if (!$idusuario) {
            echo json_encode(['error' => 'ID usuario no recibido']);
            return;
        }

        $venta = new VenderVehiculo();
        $res = $venta->ventas_empleado_mes($idusuario);

        $dias = [];
        $ventas = [];

        while ($fila = $res->fetch_assoc()) {
            $dias[] = $fila['dia'];
            $ventas[] = intval($fila['cantidad']);
        }

        echo json_encode([
            'dias' => $dias,
            'ventas' => $ventas
        ]);
    }


    public function ventas_empleado_total() {

        $idusuario = $_POST['idusuario'] ?? null;

        $venta = new VenderVehiculo();
        $total = $venta->ventas_empleado_total($idusuario);

        echo json_encode([
            'total' => intval($total['total'] ?? 0)
        ]);
    }


    public function ventas_empleado_anuladas() {

        $idusuario = $_POST['idusuario'] ?? null;

        $venta = new VenderVehiculo();
        $total = $venta->ventas_empleado_anuladas($idusuario);

        echo json_encode([
            'total' => intval($total['total'] ?? 0)
        ]);
    }
}

