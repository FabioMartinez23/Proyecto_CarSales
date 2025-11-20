<?php
ini_set('display_errors', 1);
require_once('../../modelos/caja.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['action'])) {
    $caja_controlador = new CajaControlador();

    switch ($_POST['action']) {

        /* ===========================================================
           ACCIONES DE CAJA
           =========================================================== */
        case 'verificar_o_abrir_caja_mensual':
            $caja_controlador->verificar_o_abrir_caja_mensual();
            break;

        case 'abrir_caja':
            $caja_controlador->abrir_caja();
            break;

        case 'cerrar_caja_mensual':
            $caja_controlador->cerrar_caja_mensual();
            break;

        case 'cerrar_caja_mensual':
            $caja_controlador->cerrar_caja_mensual();
            break;

        case 'traer_cajas_cerradas':
            $caja_controlador->traer_cajas_cerradas();
            break;

        /* ===========================================================
           MOVIMIENTOS Y GASTOS
           =========================================================== */
        case 'registrar_movimiento':
            $caja_controlador->registrar_movimiento();
            break;

        case 'registrar_gasto':
            $caja_controlador->registrar_gasto();
            break;

        case 'traer_movimientos':
            $caja_controlador->traer_movimientos();
            break;

        case 'traer_gastos':
            $caja_controlador->traer_gastos();
            break;

        case 'traer_historial_cierres':
        $caja_controlador->traer_historial_cierres();
        break;

        /* ===========================================================
           BALANCES (MENSUAL Y DIARIO)
           =========================================================== */
        case 'traer_balance_mensual':
            $caja_controlador->traer_balance_mensual();
            break;

        case 'traer_balance_diario':
            $caja_controlador->traer_balance_diario();
            break;

            /* ===========================================================
               GRAFICOS (MENSUAL Y DIARIO)
               =========================================================== */
        case 'grafico_diario':
            $caja_controlador->grafico_diario();
            break;

        case 'grafico_anual':
            $caja_controlador->grafico_anual();
            break;

        case 'grafico_mensual':
            $caja_controlador->grafico_mensual();
            break;
    }
}

/* ===========================================================
   CLASE CONTROLADORA
   =========================================================== */
class CajaControlador {

    /* ===========================================================
       ABRIR CAJA MANUAL (solo si se necesita)
       =========================================================== */
    public function abrir_caja() {
        $caja = new Caja();
        $saldo_inicial = $_POST['saldo_inicial'] ?? 0;
        $Usuarios_idusuarios = $_SESSION['idusuarios'] ?? null;

        $resultado = $caja->abrir_caja($saldo_inicial, $Usuarios_idusuarios);
        echo json_encode(['status' => 'success', 'mensaje' => 'Caja abierta con éxito', 'id' => $resultado]);
    }

    /* ===========================================================
       VERIFICAR O ABRIR CAJA MENSUAL AUTOMÁTICAMENTE
       =========================================================== */
    public function verificar_o_abrir_caja_mensual() {
        $caja = new Caja();
        $Usuarios_idusuarios = $_SESSION['idusuarios'] ?? 1;

        $resultado = $caja->verificar_o_abrir_caja_mensual($Usuarios_idusuarios);

        $data = [];
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
        }

        echo json_encode($data);
    }

    /* ===========================================================
       CERRAR CAJA (manual diaria o de turno)
       =========================================================== */
    public function cerrar_caja() {
        $caja = new Caja();
        $idcaja = $_POST['idcaja'] ?? null;
        $observaciones = $_POST['observaciones'] ?? '';

        $resultado = $caja->cerrar_caja($idcaja, $observaciones);
        echo json_encode(['status' => 'success', 'mensaje' => 'Caja cerrada correctamente', 'resultado' => $resultado]);
    }

    /* ===========================================================
       CERRAR CAJA MENSUAL MANUAL
       =========================================================== */
    public function cerrar_caja_mensual() {
        $caja = new Caja();
        $anio = $_POST['anio'] ?? date('Y');
        $mes = $_POST['mes'] ?? date('m');
        $observaciones = $_POST['observaciones'] ?? '';

        $resultado = $caja->cerrar_caja_mensual($anio, $mes, $observaciones);

        echo json_encode([
            'status' => 'success',
            'mensaje' => 'Caja mensual cerrada correctamente',
            'resultado' => $resultado
        ]);
    }

    /* ===========================================================
       HISTORIAL DE CAJAS CERRADAS
       =========================================================== */
    public function traer_cajas_cerradas() {
        $caja = new Caja();
        $resultado = $caja->traer_cajas_cerradas();

        $data = [];
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
        }

        echo json_encode($data);
    }

    /* ===========================================================
       REGISTRAR MOVIMIENTO
       =========================================================== */
    public function registrar_movimiento() {
        $caja = new Caja();

        $tipo = $_POST['tipo'] ?? '';
        $monto = $_POST['monto'] ?? 0;
        $descripcion = $_POST['descripcion'] ?? '';
        $referencia_tabla = $_POST['referencia_tabla'] ?? null;
        $referencia_id = $_POST['referencia_id'] ?? null;
        $caja_idcaja = $_POST['caja_idcaja'] ?? null;
        $tipo_movimiento = $_POST['tipo_movimiento_idtipo_movimiento'] ?? null;
        $tipo_pago = $_POST['tipo_pago_idtipo_pago'] ?? null;
        $Usuarios_idusuarios = $_SESSION['idusuarios'] ?? null;

        $resultado = $caja->registrar_movimiento(
            $tipo,
            $monto,
            $descripcion,
            $referencia_tabla,
            $referencia_id,
            $caja_idcaja,
            $tipo_movimiento,
            $tipo_pago,
            $Usuarios_idusuarios
        );

        echo json_encode([
            'status' => 'success',
            'mensaje' => 'Movimiento registrado con éxito',
            'id' => $resultado
        ]);
    }

    /* ===========================================================
       REGISTRAR GASTO
       =========================================================== */
    public function registrar_gasto() {
        $caja = new Caja();

        $descripcion = $_POST['descripcion'] ?? '';
        $monto = $_POST['monto'] ?? 0;
        $tipo_gasto = $_POST['tipo_gasto_idtipo_gasto'] ?? 1;
        $Usuarios_idusuarios = $_SESSION['idusuarios'] ?? null;

        $resultado = $caja->registrar_gasto($descripcion, $monto, $tipo_gasto, $Usuarios_idusuarios);

        echo json_encode([
            'status' => 'success',
            'mensaje' => 'Gasto registrado con éxito',
            'id' => $resultado
        ]);
    }

    /* ===========================================================
       LISTAR MOVIMIENTOS
       =========================================================== */
    public function traer_movimientos() {
        $caja = new Caja();
        $desde = $_POST['desde'] ?? '';
        $hasta = $_POST['hasta'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        $resultado = $caja->traer_movimientos($desde, $hasta, $tipo);

        $data = [];
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
        }

        echo json_encode($data);
    }

    /* ===========================================================
       LISTAR GASTOS
       =========================================================== */
    public function traer_gastos() {
        $caja = new Caja();
        $desde = $_POST['desde'] ?? '';
        $hasta = $_POST['hasta'] ?? '';

        $resultado = $caja->traer_gastos($desde, $hasta);

        $data = [];
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
        }

        echo json_encode($data);
    }

    /* ===========================================================
       HISTORIAL DE CIERRES DE CAJA MENSUAL
       =========================================================== */

       public function traer_historial_cierres() {
            $caja = new Caja();
            $resultado = $caja->traer_historial_cierres();

            $cierres = [];
            while($fila = $resultado->fetch_assoc()) {
                $cierres[] = [
                    'fecha_cierre' => $fila['fecha_cierre'],
                    'total_ingresos' => $fila['total_ingresos'],
                    'total_egresos' => $fila['total_egresos'],
                    'balance_final' => $fila['balance_final'],
                    'saldo_final' => $fila['saldo_final'],
                    'usuario' => $fila['usuario'],
                    'observaciones' => $fila['observaciones']
                ];
            }

            echo json_encode($cierres);
        }


    /* ===========================================================
       BALANCES (MENSUAL Y DIARIO)
       =========================================================== */
    public function traer_balance_mensual() {
        $caja = new Caja();
        $anio = $_POST['anio'] ?? date('Y');
        $mes = $_POST['mes'] ?? date('m');

        $resultado = $caja->traer_balance_mensual($anio, $mes);

        $data = [];
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
        }

        echo json_encode($data);
    }

    public function traer_balance_diario() {
        $caja = new Caja();
        $resultado = $caja->traer_balance_diario();

        $data = [];
        while ($fila = $resultado->fetch_assoc()) {
            $data[] = $fila;
        }

        echo json_encode($data);
    }

    public function grafico_diario() {
        $caja = new Caja();
        $resultado = $caja->grafico_diario();

        $labels = [];
        $ingresos = [];
        $egresos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $labels[] = $fila['dia'];
            $ingresos[] = (float)$fila['total_ingresos'];
            $egresos[] = (float)$fila['total_egresos'];
        }

        echo json_encode([
            'labels' => $labels,
            'ingresos' => $ingresos,
            'egresos' => $egresos
        ]);
    }

    public function grafico_anual() {
        $caja = new Caja();
        $resultado = $caja->grafico_anual();

        $meses = [];
        $balances = [];

        while ($fila = $resultado->fetch_assoc()) {
            $meses[] = $fila['mes_nombre'];
            $balances[] = (float)$fila['balance'];
        }

        echo json_encode([
            'meses' => $meses,
            'balances' => $balances
        ]);
    }

    public function grafico_mensual() {
        $caja = new Caja();
        $anio = $_POST['anio'] ?? date('Y');
        $mes = $_POST['mes'] ?? date('m');

        $resultado = $caja->traer_balance_mensual_grafico($anio, $mes);

        $labels = [];
        $ingresos = [];
        $egresos = [];
        $balance = [];

        while ($fila = $resultado->fetch_assoc()) {
            $labels[] = $fila['periodo'];
            $ingresos[] = (float)$fila['total_ingresos'];
            $egresos[] = (float)$fila['total_egresos'];
            $balance[] = (float)$fila['balance_mensual'];
        }

        echo json_encode([
            'labels' => $labels,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'balance' => $balance
        ]);
    }



}

