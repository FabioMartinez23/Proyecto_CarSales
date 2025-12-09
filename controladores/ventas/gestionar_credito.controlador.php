<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../modelos/conexion.php');
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/comision_venta.php');
require_once('../../modelos/caja.php');
require_once('../../modelos/vehiculos.php');

if (isset($_POST['action']) && $_POST['action'] === 'actualizar_credito') {
    $controller = new GestionarCreditoControlador();
    $controller->actualizar_credito();
}

class GestionarCreditoControlador
{
    public function actualizar_credito()
    {
        $idventa = isset($_POST['idventa']) ? (int)$_POST['idventa'] : 0;
        $resultado = $_POST['resultado'] ?? '';
        $montoAprobado = $_POST['monto_aprobado_credito'] ?? null;
        $observacionRespuesta = $_POST['observacion_respuesta'] ?? '';

        if ($idventa <= 0 || ($resultado !== 'aprobado' && $resultado !== 'rechazado')) {
            header('Location: ../../index.php?page=listado_ventas&mensaje=' . urlencode('Datos inválidos en gestión de crédito.') . '&status=error');
            exit;
        }

        $db = new Conexion();
        $db->conectar();
        $conn = $db->_con;

        $conn->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
        $conn->begin_transaction();

        try {
            // 1️⃣ Traer venta
            $ventaModel = new VenderVehiculo('', '', '', '', '', '', '', '', '', $conn);
            $venta = $ventaModel->traer_venta_para_anular($idventa); // trae ventas.* completo

            if (!$venta) {
                throw new Exception("No se encontró la venta asociada al crédito.");
            }

            if ((int)$venta['tipo_pago_idtipo_pago'] !== 3) {
                throw new Exception("La venta no es de tipo Crédito Bancario.");
            }

            // 2️⃣ Traer comisión ya calculada (para saber montos a mover en caja si se aprueba)
            $comisionModel = new Comisiones_Ventas('', '', '', '', '', '', '', '', $conn);
            $comision = $comisionModel->traer_comision_por_venta($idventa);

            if (!$comision) {
                throw new Exception("No se encontró la comisión asociada a la venta.");
            }

            // 3️⃣ Armar valores para UPDATE
            $estadoCredito = $resultado === 'aprobado' ? 'aprobado' : 'rechazado';
            $estadoVenta   = $resultado === 'aprobado' ? 'Realizada' : 'Anulada';

            $montoAprobadoCredito = null;
            if ($resultado === 'aprobado' && $montoAprobado !== null && $montoAprobado !== '') {
                $montoAprobadoCredito = (float)$montoAprobado;
            }

            // Observación de crédito final = lo que había + respuesta
            $observacionRespuesta = trim($observacionRespuesta);
            $textoExtra = $observacionRespuesta !== '' 
                ? "Respuesta banco: " . $observacionRespuesta 
                : null;

            // 4️⃣ Actualizar venta (estado crédito + venta + fechas + monto aprobado + observación)
            $ventaModel->setEstado_venta($estadoVenta);
            $ventaModel->setEstado_venta_credito($estadoCredito);
            $ventaModel->setFecha_respuesta_credito(date('Y-m-d H:i:s'));

            if ($montoAprobadoCredito !== null) {
                $ventaModel->setMonto_aprobado_credito($montoAprobadoCredito);
            }

            if ($textoExtra) {
                $ventaModel->setObservacion_credito(
                    (($venta['observacion_credito'] ?? '') . "\n" . $textoExtra)
                );
            }

            if (!$ventaModel->actualizar_estado_credito($idventa)) {
                throw new Exception("Error al actualizar el estado del crédito.");
            }

            // 5️⃣ Si está APROBADO → registrar movimientos de CAJA (ingreso concesionaria + egreso comisión)
            if ($resultado === 'aprobado') {

                $caja = new Caja('', '', '', '', '', '', '', $conn);
                $cajaActiva = $caja->verificar_o_abrir_caja_mensual($_SESSION['idusuarios']);

                if (!$cajaActiva) {
                    throw new Exception("No hay caja activa para registrar el crédito aprobado.");
                }

                $datosCaja  = $cajaActiva->fetch_assoc();
                $idcaja     = $datosCaja['idcaja'];
                $idTipoVenta    = $caja->obtener_id_tipo_movimiento('Venta vehículo');
                $idTipoComision = $caja->obtener_id_tipo_movimiento('Comisión empleado');

                // Montos ya calculados en la comisión
                $montoConces = (float)$comision['monto_concesionaria'];
                $montoEmp    = (float)$comision['monto_empleado'];

                // INGRESO concesionaria (puede representar la comisión acreditada)
                $caja->registrar_movimiento(
                    'ingreso',
                    $montoConces,
                    "Venta (crédito aprobado) ID $idventa del vehiculo ID {$venta['vehiculo_idvehiculo']}",
                    'ventas',
                    $idventa,
                    $idcaja,
                    $idTipoVenta,
                    3, // tipo_pago_idtipo_pago = 3 (Crédito Bancario)
                    $_SESSION['idusuarios']
                );

                // EGRESO comisión empleado
                if ($montoEmp > 0) {
                    $caja->registrar_movimiento(
                        'egreso',
                        $montoEmp,
                        "Comisión empleado ID {$venta['empleados_idempleados']} (crédito aprobado)",
                        'comisiones_ventas',
                        $idventa,
                        $idcaja,
                        $idTipoComision,
                        3,
                        $_SESSION['idusuarios']
                    );
                }
            }

            // 6️⃣ Actualizar estado del VEHÍCULO según resultado del crédito
            $vehiculo = new Vehiculos('', '', '', '', '', '', '', '', '', '', $conn);
            $vehiculo->setIdvehiculos($venta['vehiculo_idvehiculo']);

            if ($resultado === 'aprobado') {
                // ✅ Crédito aprobado → el vehículo pasa a vendido (baja lógica)
                if (!$vehiculo->eliminar_vehiculo_venta()) {
                    throw new Exception("No se pudo marcar el vehículo como vendido (crédito aprobado).");
                }
            } elseif ($resultado === 'rechazado') {
                // ❌ Crédito rechazado → el vehículo vuelve a disponible
                if (!$vehiculo->marcar_disponible()) {
                    throw new Exception("No se pudo devolver el vehículo a estado 'disponible' tras rechazo del crédito.");
                }
            }

            $conn->commit();

            $msg = $resultado === 'aprobado'
                ? 'Crédito aprobado y movimientos de caja registrados.'
                : 'Crédito rechazado correctamente. El vehículo volvió a disponible.';

            header('Location: ../../index.php?page=listado_ventas&id='.$idventa.'&mensaje='.urlencode($msg).'&status=success');
            exit;

        } catch (Exception $e) {

            $conn->rollback();
            error_log("Error en gestionar_credito: " . $e->getMessage());

            header('Location: ../../index.php?page=listado_ventas&mensaje='.urlencode($e->getMessage()).'&status=error');
            exit;

        } finally {
            $db->desconectar();
        }
    }
}

