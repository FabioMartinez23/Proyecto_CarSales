<?php
ini_set('display_errors', 1);

require_once('../../modelos/gastos_generales.php');
require_once('../../modelos/caja.php'); // Necesario para obtener caja abierta

class GastosControlador {

    /* ============================================================
       GUARDAR GASTO GENERAL / VEHÍCULO / VENTA / EMPLEADO
    ============================================================ */
    public function guardar() {

        header('Content-Type: application/json');

        // Validación inicial mínima
        if (!isset($_POST['descripcion']) || !isset($_POST['monto']) || !isset($_POST['tipo_gasto'])) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Faltan datos obligatorios.'
            ]);
            return;
        }

        // ===============================
        //  Datos recibidos del formulario
        // ===============================
        $descripcion = trim($_POST['descripcion']);
        $monto = floatval($_POST['monto']);
        $tipo_gasto = intval($_POST['tipo_gasto']);
        $origen = $_POST['origen']; // general / vehiculo / venta / empleado

        session_start();
        $idUsuario = $_SESSION['idusuarios'] ?? 1;

        // ===============================
        //  Origen dinámico
        // ===============================
        $idVehiculo = $_POST['vehiculos_idvehiculos'] ?? null;
        $idVenta    = $_POST['ventas_idventas'] ?? null;
        $idEmpleado = $_POST['empleados_idempleados'] ?? null;

        // ===============================
        //  Obtener la caja abierta actual
        // ===============================
        $cajaModel = new Caja();
        $caja_abierta = $cajaModel->traer_caja_abierta();

        if (!$caja_abierta || $caja_abierta->num_rows == 0) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'No hay una caja abierta para registrar este gasto.'
            ]);
            return;
        }

        $filaCaja = $caja_abierta->fetch_assoc();
        $idcaja = $filaCaja['idcaja'];

        // ===============================
        //  Tipo de movimiento = egreso
        // ===============================
        $descripcionMovimiento = "Gasto: " . $descripcion;
        $tipo_movimiento_id = $cajaModel->obtener_id_tipo_movimiento('Gasto General');

        if (!$tipo_movimiento_id) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'No se encontró el tipo de movimiento "Gasto General".'
            ]);
            return;
        }

        // Tipo de pago
        $tipo_pago = $_POST['tipo_pago'] ?? 1;

        // ===============================
        //  Preparar array para el modelo
        // ===============================
        $data = [
            'descripcion' => $descripcion,
            'monto' => $monto,
            'Usuarios_idusuarios' => $idUsuario,
            'tipo_gasto_idtipo_gasto' => $tipo_gasto,
            'origen' => $origen,

            'vehiculos_idvehiculos' => $idVehiculo,
            'ventas_idventas' => $idVenta,
            'empleados_idempleados' => $idEmpleado,

            'caja_idcaja' => $idcaja,
            'tipo_movimiento_idtipo_movimiento' => $tipo_movimiento_id,
            'tipo_pago_idtipo_pago' => $tipo_pago
        ];

        // ===============================
        //  Registrar gasto + movimiento
        // ===============================
        $gastoModel = new GastoGeneral();
        $respuesta = $gastoModel->registrar_gasto_con_movimiento($data);

        // Respuesta para SweetAlert
        echo json_encode($respuesta);
    }
}

/* ============================================================
   CONTROLADOR GLOBAL
============================================================ */
if (isset($_POST['action'])) {

    $controlador = new GastosControlador();

    switch ($_POST['action']) {

        case 'guardar':
            $controlador->guardar();
            break;

        default:
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Acción no válida.'
            ]);
            break;
    }
}
