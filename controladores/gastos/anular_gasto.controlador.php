<?php

require_once('../../modelos/gastos_generales.php');
require_once('../../modelos/caja.php');
require_once('../../modelos/conexion.php');
// Si ya tenés anular_operaciones.php adaptado al nuevo esquema, podés usarlo.
// Por ahora haremos el INSERT directo sobre anular_operacion.

if (isset($_POST['action']) && $_POST['action'] === 'anular_gasto') {
    $controlador = new AnularGastoControlador();
    $controlador->anular_gasto();
}

class AnularGastoControlador
{
    public function anular_gasto()
    {
        /* =====================================================
           1 — VALIDACIÓN DE FECHA
        ===================================================== */
        if (empty($_POST['fecha_anulacion'])) {
            header('Location: ../../index.php?page=listado_gastos&mensaje=' . urlencode('Debe ingresar fecha de anulación.') . '&status=error');
            exit;
        }

        $fecha_anulacion = new DateTime($_POST['fecha_anulacion']);
        $fecha_actual = new DateTime();

        if ($fecha_anulacion > $fecha_actual) {
            header('Location: ../../index.php?page=listado_gastos&mensaje=' . urlencode('La fecha de anulación no puede ser futura.') . '&status=error');
            exit;
        }

        /* =====================================================
           2 — INICIO TRANSACCIÓN GLOBAL
        ===================================================== */
        $db = new Conexion();
        $db->conectar();
        $conn = $db->_con;

        // Nivel de aislamiento (opcional, igual que en ventas)
        $conn->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
        $conn->begin_transaction();

        try {

            $idgasto      = (int)($_POST['idgasto'] ?? 0);
            $idusuario    = (int)($_POST['idusuario'] ?? 0);
            $tipo_anulacion = (int)($_POST['tipo_anulacion'] ?? 0);
            $motivo_detalle = $_POST['motivo_detalle'] ?? null;

            if ($idgasto <= 0 || $idusuario <= 0 || $tipo_anulacion <= 0) {
                throw new Exception("Datos incompletos para anular el gasto.");
            }

            /* =====================================================
               3 — TRAER DATOS DEL GASTO
            ===================================================== */

            // Usamos el modelo con conexión compartida
            $gastoModel = new GastoGeneral(
                '', '', '', '', '', '', '', '', '', '', '', 
                $conn
            );

            $gasto = $gastoModel->traer_gasto_detallado($idgasto);

            if (!$gasto) {
                throw new Exception("No se encontró el gasto indicado.");
            }

            // Si ya está anulado, no permitir
            if (isset($gasto['estado_gasto']) && $gasto['estado_gasto'] === 'Anulado') {
                throw new Exception("El gasto ya se encuentra anulado.");
            }

            $monto   = (float)$gasto['monto'];
            $origen  = $gasto['origen'];
            $idVeh   = $gasto['vehiculos_idvehiculos'] ?? null;
            $idVen   = $gasto['ventas_idventas'] ?? null;
            $idEmp   = $gasto['empleados_idempleados'] ?? null;

            if ($monto <= 0) {
                throw new Exception("El monto del gasto es inválido para anular.");
            }

            /* =====================================================
               4 — CAJA ACTIVA + TIPO MOVIMIENTO REVERSO
            ===================================================== */

            $caja = new Caja('', '', '', '', '', '', '', $conn);

            // Igual que en ventas: verificamos u obtenemos caja mensual
            $cajaActiva = $caja->verificar_o_abrir_caja_mensual($idusuario);

            if (!$cajaActiva || $cajaActiva->num_rows === 0) {
                throw new Exception("No hay caja activa para procesar el reverso del gasto.");
            }

            $datosCaja = $cajaActiva->fetch_assoc();
            $idcaja = (int)$datosCaja['idcaja'];

            // Tipo de movimiento para reverso de gasto
            $idTipoReversoGasto = $caja->obtener_id_tipo_movimiento('Reverso gasto');

            if (!$idTipoReversoGasto) {
                throw new Exception("No existe el tipo de movimiento 'Reverso gasto'. Debe crearlo en tipo_movimiento.");
            }

            /* =====================================================
               5 — REGISTRAR MOVIMIENTO DE REVERSO EN CAJA
            ===================================================== */

            $descripcionReverso = "Reverso gasto ID $idgasto";

            // Tipo de movimiento: ingreso (para compensar el egreso original)
            // Tipo de pago: usamos 4 como en reverso de venta (puede ser un tipo genérico 'Ajuste/Reverso')
            $idTipoPagoReverso = 4;

            $caja->registrar_movimiento(
                'ingreso',                 // tipo
                $monto,                    // monto
                $descripcionReverso,       // descripción
                'gastos_generales',        // referencia_tabla
                $idgasto,                  // referencia_id
                $idcaja,                   // caja_id
                $idTipoReversoGasto,       // tipo_movimiento_id
                $idTipoPagoReverso,        // tipo_pago_id
                $idusuario                 // usuario
            );

            /* =====================================================
               6 — MARCAR GASTO COMO ANULADO
            ===================================================== */

            $fechaAnulacionFull = $fecha_anulacion->format('Y-m-d') . ' ' . date('H:i:s');

            $sqlUpdate = "
                UPDATE gastos_generales
                SET estado_gasto = 'Anulado',
                    fecha_anulacion = '$fechaAnulacionFull'
                WHERE idgastos_generales = $idgasto
            ";

            if (!$conn->query($sqlUpdate)) {
                throw new Exception("Error al actualizar el estado del gasto: " . $conn->error);
            }

            /* =====================================================
               7 — REGISTRAR EN anular_operacion (AUDITORÍA)
            ===================================================== */
            // Usamos el esquema genérico:
            // entidad = 'gasto', id_entidad = idgasto

            $motivo_detalle_sql = $motivo_detalle ? ("'" . $conn->real_escape_string($motivo_detalle) . "'") : "NULL";
            $fechaSQL = $fecha_anulacion->format('Y-m-d');

            $sqlAnulacion = "
                INSERT INTO anular_operacion
                    (fecha_anulacion, tipo_anulacion_idtipo_anulacion, entidad, id_entidad, motivo_detalle, Usuarios_idusuarios)
                VALUES
                    ('$fechaSQL', $tipo_anulacion, 'gasto', $idgasto, $motivo_detalle_sql, $idusuario)
            ";

            if (!$conn->query($sqlAnulacion)) {
                throw new Exception("Error al registrar la anulación en auditoría: " . $conn->error);
            }

            $idanulacion = $conn->insert_id;

            /* =====================================================
               8 — COMMIT FINAL
            ===================================================== */
            $conn->commit();

            $db->desconectar();

            header("Location: ../../index.php?page=listado_gastos&mensaje=" . urlencode("Gasto anulado correctamente.") . "&status=success&idgasto=$idgasto&idanulacion=$idanulacion");
            exit;

        } catch (Exception $e) {

            $conn->rollback();
            $db->desconectar();

            header("Location: ../../index.php?page=listado_gastos&mensaje=" . urlencode($e->getMessage()) . "&status=error");
            exit;
        }
    }
}
