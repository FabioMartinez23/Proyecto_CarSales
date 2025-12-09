<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once('../../modelos/conexion.php');
require_once('../../modelos/gastos_taller.php');
require_once('../../modelos/caja.php');
require_once('../../modelos/vehiculos_taller.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'registrar_gasto_taller':
            registrar_gasto_taller();
            break;

        case 'anular_gasto_taller':
            anular_gasto_taller();
            break;

        default:
            header('Location: ../../index.php?page=listado_vehiculos_taller&mensaje=' . urlencode('Acción no válida.') . '&status=error');
            exit;
    }
}

function registrar_gasto_taller()
{
    $idvehiculos_taller = (int)($_POST['vehiculos_taller_id'] ?? 0);
    $tipo_gasto_id      = (int)($_POST['tipo_gasto_id'] ?? 0);
    $monto              = (float)str_replace(',', '.', str_replace('.', '', $_POST['monto'] ?? '0'));
    $descripcion        = $_POST['descripcion'] ?? null;
    $proveedor          = $_POST['proveedor'] ?? null;
    $comprobante        = $_POST['comprobante_numero'] ?? null;
    $fecha_gasto        = $_POST['fecha_gasto'] ?? date('Y-m-d');
    $tipo_pago          = (int)($_POST['tipo_pago'] ?? 1); // 1=Efectivo, 2=Transferencia
    $idusuario          = $_SESSION['idusuarios'] ?? null;

    if ($idvehiculos_taller <= 0 || $tipo_gasto_id <= 0 || $monto <= 0) {
        header('Location: ../../index.php?page=gastos_taller&idvehiculos_taller=' . $idvehiculos_taller .
            '&mensaje=' . urlencode('Datos insuficientes para registrar el gasto.') . '&status=error');
        exit;
    }

    $db = new Conexion();
    $db->conectar();
    $conn = $db->_con;

    $conn->begin_transaction();

    try {
        // 1) Registrar gasto en gastos_taller
        $gastosModel = new Gastos_Taller($conn);
        $idgasto = $gastosModel->registrar_gasto(
            $fecha_gasto,
            $descripcion,
            $monto,
            $proveedor,
            $comprobante,
            $idusuario,
            $tipo_gasto_id,
            $idvehiculos_taller
        );

        if (!$idgasto) {
            throw new Exception("No se pudo registrar el gasto de taller.");
        }

        // 2) Caja: egreso por gasto de taller
        $caja = new Caja('', '', '', '', '', '', '', $conn);
        $cajaActiva = $caja->verificar_o_abrir_caja_mensual($idusuario);

        if (!$cajaActiva) {
            throw new Exception("No hay caja activa para registrar el gasto.");
        }

        $datosCaja = $cajaActiva->fetch_assoc();
        $idcaja = $datosCaja['idcaja'];

        // Id de tipo movimiento Gasto Taller (asegurate de tenerlo en tabla tipo_movimiento)
        $idTipoGastoTaller = $caja->obtener_id_tipo_movimiento('Gasto taller');

        $descripcionCaja = "Gasto taller ID $idgasto";

        $caja->registrar_movimiento(
            'egreso',
            $monto,
            $descripcionCaja,
            'gastos_taller',
            $idgasto,
            $idcaja,
            $idTipoGastoTaller,
            $tipo_pago,
            $idusuario
        );

        $conn->commit();

        header('Location: ../../index.php?page=gastos_taller&idvehiculos_taller=' . $idvehiculos_taller .
            '&mensaje=' . urlencode('Gasto de taller registrado correctamente.') . '&status=success');
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error registrar_gasto_taller: " . $e->getMessage());

        header('Location: ../../index.php?page=gastos_taller&idvehiculos_taller=' . $idvehiculos_taller .
            '&mensaje=' . urlencode($e->getMessage()) . '&status=error');
        exit;
    } finally {
        $db->desconectar();
    }
}

function anular_gasto_taller()
{
    $idgasto            = (int)($_POST['idgastos_taller'] ?? 0);
    $idvehiculos_taller = (int)($_POST['vehiculos_taller_id'] ?? 0);
    $motivo             = $_POST['motivo_anulacion'] ?? '';
    $idusuario          = $_SESSION['idusuarios'] ?? null;

    if ($idgasto <= 0) {
        header('Location: ../../index.php?page=gastos_taller&idvehiculos_taller=' . $idvehiculos_taller .
            '&mensaje=' . urlencode('Gasto no especificado.') . '&status=error');
        exit;
    }

    $db = new Conexion();
    $db->conectar();
    $conn = $db->_con;

    $conn->begin_transaction();

    try {
        $gastosModel = new Gastos_Taller($conn);
        $gasto = $gastosModel->traer_por_id($idgasto);

        if (!$gasto) {
            throw new Exception("No se encontró el gasto.");
        }

        if ((int)$gasto['anulado'] === 1) {
            throw new Exception("El gasto ya se encuentra anulado.");
        }

        $monto = (float)$gasto['monto'];

        // 1) Marcar anulado
        if (!$gastosModel->marcar_anulado($idgasto)) {
            throw new Exception("No se pudo marcar el gasto como anulado.");
        }

        // 2) Caja: reverso (INGRESO)
        $caja = new Caja('', '', '', '', '', '', '', $conn);
        $cajaActiva = $caja->verificar_o_abrir_caja_mensual($idusuario);

        if (!$cajaActiva) {
            throw new Exception("No hay caja activa para registrar el reverso.");
        }

        $datosCaja = $cajaActiva->fetch_assoc();
        $idcaja = $datosCaja['idcaja'];

        // Podés usar mismo tipo de movimiento o crear uno ej. "Reverso gasto taller"
        $idTipoGastoTaller = $caja->obtener_id_tipo_movimiento('Gasto taller');

        $descripcionCaja = "Reverso gasto taller ID $idgasto";
        $tipo_pago_reverso = 4; // 4 = Reverso/Ajuste

        $caja->registrar_movimiento(
            'ingreso',
            $monto,
            $descripcionCaja,
            'gastos_taller',
            $idgasto,
            $idcaja,
            $idTipoGastoTaller,
            $tipo_pago_reverso,
            $idusuario
        );

        // 3) (Opcional) Registrar en anular_operacion si ya usás ese flujo
        /*
        require_once('../../modelos/anular_operacion.php');
        $anular = new AnularOperacion($conn);
        $anular->registrar(
            'gastos_taller',
            $idgasto,
            'Gasto Taller',
            $motivo,
            $idusuario
        );
        */

        $conn->commit();

        header('Location: ../../index.php?page=gastos_taller&idvehiculos_taller=' . $idvehiculos_taller .
            '&mensaje=' . urlencode('Gasto de taller anulado y reversado en caja.') . '&status=success');
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error anular_gasto_taller: " . $e->getMessage());

        header('Location: ../../index.php?page=gastos_taller&idvehiculos_taller=' . $idvehiculos_taller .
            '&mensaje=' . urlencode($e->getMessage()) . '&status=error');
        exit;
    } finally {
        $db->desconectar();
    }
}
