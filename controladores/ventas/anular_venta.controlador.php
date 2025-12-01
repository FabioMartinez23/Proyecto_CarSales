<?php

require_once('../../modelos/vehiculos.php');
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/anular_operaciones.php');
require_once('../../modelos/caja.php');
require_once('../../modelos/comision_venta.php');
require_once('../../modelos/conexion.php');

if (isset($_POST['action']) && $_POST['action'] == 'anular_venta') {
    $anular = new AnularVentaControlador();
    $anular->anular_venta();
}

class AnularVentaControlador {

    public function anular_venta() {

        /* =====================================================
           1 — VALIDACIÓN DE FECHA
        ===================================================== */
        if (empty($_POST['fecha_anulacion'])) {
            header('Location: ../../index.php?page=anular_ventas&mensaje=Debe ingresar fecha de anulación.&status=error&idventa=' . $_POST['idventa']);
            exit;
        }

        $fecha_anulacion = new DateTime($_POST['fecha_anulacion']);
        $fecha_actual = new DateTime();

        if ($fecha_anulacion > $fecha_actual) {
            header('Location: ../../index.php?page=anular_ventas&mensaje=La fecha no puede ser futura.&status=error&idventa=' . $_POST['idventa']);
            exit;
        }

        /* =====================================================
           2 — INICIO TRANSACCIÓN GLOBAL
        ===================================================== */
        $db = new Conexion();
        $db->conectar();
        $conn = $db->_con;

        $conn->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
        $conn->begin_transaction();

        try {

            $idventa = $_POST['idventa'];
            $idusuario = $_POST['idusuario'];

            /* =====================================================
               3 — TRAER DATOS DE LA VENTA
            ===================================================== */
            $venta = new VenderVehiculo('', '', '', '', '', '', '', '', '', $conn);
            $ventaData = $venta->traer_venta_para_anular($idventa);

            if (!$ventaData) {
                throw new Exception("No se encontró la venta.");
            }

            $idvehiculo = $ventaData['vehiculo_idvehiculo'];

            /* =====================================================
               4 — TRAER COMISIÓN
            ===================================================== */
            $comision = new Comisiones_Ventas('', '', '', '', '', '', '', '', $conn);
            $comisionData = $comision->traer_comision_por_venta($idventa);

            $monto_emp   = floatval($comisionData['monto_empleado'] ?? 0);
            $monto_conces = floatval($comisionData['monto_concesionaria'] ?? 0);

            /* =====================================================
               5 — CAJA — OBTENER CAJA ACTIVA
            ===================================================== */
            $caja = new Caja('', '', '', '', '', '', '', $conn);
            $cajaActiva = $caja->verificar_o_abrir_caja_mensual($idusuario);

            if (!$cajaActiva) {
                throw new Exception("No hay caja activa para procesar.");
            }

            $datosCaja = $cajaActiva->fetch_assoc();
            $idcaja = $datosCaja['idcaja'];

            /* =====================================================
               6 — OBTENER IDs DE MOVIMIENTOS
            ===================================================== */
            $idTipoReversoVenta = $caja->obtener_id_tipo_movimiento('Reverso venta vehículo');
            $idTipoReversoComision = $caja->obtener_id_tipo_movimiento('Reverso comisión empleado');

            if (!$idTipoReversoVenta || !$idTipoReversoComision) {
                throw new Exception("No existen tipos de movimiento de reverso. Debe crearlos.");
            }

            /* =====================================================
               7 — REVERSO EN CAJA
            ===================================================== */

            // Reverso del ingreso principal (concesionaria)
            $caja->registrar_movimiento(
                'egreso',
                $monto_conces,
                "Reverso venta ID $idventa",
                'ventas',
                $idventa,
                $idcaja,
                $idTipoReversoVenta,
                4,          // ← ACÁ FIJO 4
                $idusuario
            );

            // Reverso de la comisión al empleado
            if ($monto_emp > 0) {
                $caja->registrar_movimiento(
                    'ingreso',
                    $monto_emp,
                    "Reverso comisión empleado por venta $idventa",
                    'comisiones_ventas',
                    $idventa,
                    $idcaja,
                    $idTipoReversoComision,
                    4,      // ← ACÁ TAMBIÉN 4
                    $idusuario
                );
            }

            /* =====================================================
               8 — ANULAR COMISIÓN
            ===================================================== */
            $comision->anular_comision_por_venta($idventa);

            /* =====================================================
               9 — ANULAR ESTADO DE VENTA
            ===================================================== */
            $venta->anular_venta_estado($idventa);

            /* =====================================================
               10 — RESTAURAR VEHÍCULO
            ===================================================== */
            $vehiculo = new Vehiculos('', '', '', '', '', '', '', '', '', '', $conn);
            $vehiculo->setIdvehiculos($idvehiculo);
            $vehiculo->restaurar_estado_disponible();

            /* =====================================================
               11 — REGISTRAR ANULACIÓN (AUDITORÍA)
            ===================================================== */
            $anular = new AnularOperacion('', '', '', '', '', $idusuario, $conn);
            $anular->setFecha_anulacion($_POST['fecha_anulacion']);
            $anular->setTipo_anulacion_idtipo_anulacion($_POST['tipo_anulacion']);
            $anular->setEntidad('venta');
            $anular->setId_entidad($idventa);
            $anular->setMotivo_detalle($_POST['motivo_detalle'] ?? null);

            $idanulacion = $anular->registrar();

            /* =====================================================
               12 — NOTIFICACIÓN PUSHER
            ===================================================== */
            require_once __DIR__ . '/../notificacion_trigger.php';
            $pusher->trigger('notificaciones', 'nuevo-evento', [
                'tipo' => 'anulacion',
                'mensaje' => "La venta ID $idventa fue ANULADA",
                'fecha' => date('Y-m-d H:i:s')
            ]);

            /* =====================================================
               13 — COMMIT FINAL
            ===================================================== */
            $conn->commit();

            header("Location: ../../index.php?page=listado_ventas&mensaje=Venta anulada correctamente.&status=success&idventa=$idventa&idanulacion=$idanulacion");
            exit();

        } catch (Exception $e) {

            $conn->rollback();

            header('Location: ../../index.php?page=anular_ventas&mensaje=' . urlencode($e->getMessage()) . '&status=error&idventa=' . $_POST['idventa']);
            exit();

        } finally {
            $db->desconectar();
        }
    }
}