<?php

require_once('../../modelos/vehiculos.php');
require_once('../../modelos/comprar_vehiculos.php');
require_once('../../modelos/anular_operaciones.php');
require_once('../../modelos/tablas_maestras/estado_vehiculo.php');
require_once('../../modelos/conexion.php');

if (isset($_POST['action']) && $_POST['action'] == 'anular_compra') {
    $controlador = new AnularCompraControlador();
    $controlador->anular_compra();
}

class AnularCompraControlador {

    public function anular_compra() {

        /* =====================================================
           1 — VALIDACIÓN DE FECHA
        ===================================================== */
        if (empty($_POST['fecha_anulacion'])) {
            header('Location: ../../index.php?page=anular_compras&mensaje=Debe ingresar fecha de anulación.&status=error&idcompra=' . $_POST['idcompra']);
            exit;
        }

        $fecha_anulacion = new DateTime($_POST['fecha_anulacion']);
        $fecha_actual    = new DateTime();

        if ($fecha_anulacion > $fecha_actual) {
            header('Location: ../../index.php?page=anular_compras&mensaje=La fecha no puede ser futura.&status=error&idcompra=' . $_POST['idcompra']);
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

            $idcompra  = (int)$_POST['idcompra'];
            $idusuario = (int)$_POST['idusuario'];

            /* =====================================================
               3 — TRAER DATOS BÁSICOS DE COMPRA
                  (solo necesitamos vehiculo_idvehiculo y estado)
            ===================================================== */
            // Usamos tu método ya existente
            $compraModel = new ComprarVehiculo('', '', '', '', '', '', '', $conn);
            $compraData  = $compraModel->traer_compra_por_id_devolver($idcompra);

            if (!$compraData) {
                throw new Exception("No se encontró la consignación.");
            }

            // Si ya está anulada, no seguimos
            if (isset($compraData['estado_compra']) && $compraData['estado_compra'] === 'Anulada') {
                throw new Exception("La consignación ya se encuentra anulada.");
            }

            $idvehiculo = (int)$compraData['vehiculo_idvehiculo'];

            /* =====================================================
               4 — ANULAR ESTADO DE COMPRA
            ===================================================== */
            if (!$compraModel->anular_compra_estado($idcompra)) {
                throw new Exception("No se pudo actualizar el estado de la consignación.");
            }

            /* =====================================================
               5 — CAMBIAR ESTADO DEL VEHÍCULO
            ===================================================== */
            $estadoVehiculo = new Estado_Vehiculo();
            // Definí en tu tabla estado_vehiculo una clave 'baja' o 'baja_consignacion'
            $id_estado_baja = $estadoVehiculo->obtenerIdPorEstado('Baja consignación');

            if (!$id_estado_baja) {
                throw new Exception("No se encontró el estado de vehículo 'Baja consignación'.");
            }

            $vehiculo = new Vehiculos();
            $vehiculo->actualizar_estado($idvehiculo, $id_estado_baja);

            /* =====================================================
               6 — REGISTRAR ANULACIÓN EN anular_operacion
            ===================================================== */
            $anular = new AnularOperacion('', '', '', '', '', $idusuario, $conn);
            $anular->setFecha_anulacion($_POST['fecha_anulacion']);
            $anular->setTipo_anulacion_idtipo_anulacion($_POST['tipo_anulacion']);
            $anular->setEntidad('compra');
            $anular->setId_entidad($idcompra);
            $anular->setMotivo_detalle($_POST['motivo_detalle'] ?? null);

            $idanulacion = $anular->registrar();

            if (!$idanulacion) {
                throw new Exception("No se pudo registrar la anulación en auditoría.");
            }

            /* =====================================================
               7 — (OPCIONAL) NOTIFICACIÓN PUSHER
            ===================================================== */
            require_once __DIR__ . '/../notificacion_trigger.php';
            $pusher->trigger('notificaciones', 'nuevo-evento', [
                'tipo'    => 'anulacion_compra',
                'mensaje' => "La consignación (compra) ID $idcompra fue ANULADA",
                'fecha'   => date('Y-m-d H:i:s')
            ]);

            /* =====================================================
               8 — COMMIT FINAL
            ===================================================== */
            $conn->commit();

            header("Location: ../../index.php?page=listado_compras&mensaje=Consignación anulada correctamente.&status=success&idcompra=$idcompra&idanulacion=$idanulacion");
            exit();

        } catch (Exception $e) {

            $conn->rollback();
            error_log("Error en anular_compra: " . $e->getMessage());

            header('Location: ../../index.php?page=anular_compras&mensaje=' . urlencode($e->getMessage()) . '&status=error&idcompra=' . $_POST['idcompra']);
            exit();

        } finally {
            $db->desconectar();
        }
    }
}
