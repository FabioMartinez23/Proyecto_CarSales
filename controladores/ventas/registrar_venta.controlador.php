<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Modelos
require_once('../../modelos/vehiculos.php');
require_once('../../modelos/precios_vehiculos.php');
require_once('../../modelos/personas.php');
require_once('../../modelos/domicilio.php');
require_once('../../modelos/documentos.php');
require_once('../../modelos/usuarios.php');
require_once('../../modelos/contactos.php');
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/registro_clientes.php');
require_once('../../modelos/ventas_forma_pagos.php');
require_once('../../modelos/comision_venta.php');
require_once('../../modelos/tablas_maestras/tipo_comision.php');
require_once('../../modelos/caja.php');
require_once('../../modelos/conexion.php');

if (isset($_POST['action']) && $_POST['action'] == 'registrar_venta') {
    $venta_controlador = new RegistrarVentaControlador();
    $venta_controlador->registrar_venta();
}

class RegistrarVentaControlador {

    public function registrar_venta() {

        /* ===============================================================
           🔹 1 — Crear conexión GLOBAL compartida para TODA la transacción
        =============================================================== */
        $db = new Conexion();
        $db->conectar();
        $conn = $db->_con;

        $conn->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
        $conn->begin_transaction();

        try {

            /* ===============================================================
               2 — CLIENTE
            =============================================================== */
            $consulta_cliente = new RegistroCliente('', '', $conn);
            $result_consulta = $consulta_cliente->traer_cliente_por_id($_POST['id_usuario']);

            if ($result_consulta) {
                $cliente_id = $result_consulta['idregistro_clientes'];
            } else {
                $registrar_cliente = new RegistroCliente('', $conn);
                $registrar_cliente->setUsuarios_idusuarios($_POST['id_usuario']);
                $cliente_id = $registrar_cliente->agregar_cliente();

                if (!$cliente_id) {
                    throw new Exception("Error al registrar cliente.");
                }
            }

            /* ===============================================================
               3 — VENTA
            =============================================================== */
            $venta = new VenderVehiculo('', '', '', '', '', '', '', '', $conn);
            $venta->setDescripcion($_POST['observaciones']);
            $venta->setTipo_pago_idtipo_pago($_POST['tipo_pago']);
            $venta->setVehiculo_idvehiculo($_POST['vehiculos_idvehiculos']);
            $venta->setRegistro_clientes_idregistro_clientes($cliente_id);
            $venta->setEmpleados_idempleados($_POST['idempleado']);
            $venta->setTitular_vehiculo_idtitular_vehiculo($_POST['titular_vehiculo']);

            $idventa = $venta->agregar_venta();
            if (!$idventa) {
                throw new Exception("Error al registrar la venta.");
            }

            /* ===============================================================
               4 — COMISIONES
            =============================================================== */
            $precio_tomado  = floatval($_POST['precio_tomado'] ?? 0);
            $precio_publico = floatval($_POST['precio_publico_real'] ?? 0);

            if ($precio_publico <= 0 || $precio_tomado <= 0) {
                throw new Exception("No se pudieron obtener los precios del vehículo.");
            }

            $ganancia = max($precio_publico - $precio_tomado, 0);

            // Perfil empleado
            $usuario = new Usuario('', '', '', '', '', '', $conn);
            $perfil_data = $usuario->traer_perfil_por_id($_POST['idempleado']);
            $perfil = strtolower(trim($perfil_data['perfil'] ?? ''));

            if (strpos($perfil, 'admin') !== false) {
                $porc_emp = 0;
                $porc_conces = 100;
                $tipo_comision = 1;
            } else {
                $porc_emp = 30;
                $porc_conces = 70;
                $tipo_comision = 2;
            }

            $monto_emp = ($ganancia * $porc_emp) / 100;
            $monto_conces = ($ganancia * $porc_conces) / 100;

            // Registrar comisiones
            $comision = new Comisiones_Ventas('', '', '', '', '', '', '', '', $conn);
            $comision->setPorcentaje_empleado($porc_emp);
            $comision->setPorcentaje_concesionaria($porc_conces);
            $comision->setMonto_empleado($monto_emp);
            $comision->setMonto_concesionaria($monto_conces);
            $comision->setTipo_comisiones_idtipo_comisiones($tipo_comision);
            $comision->setVentas_idventas($idventa);

            if (!$comision->agregar_comision_venta()) {
                throw new Exception("Error al registrar comisión.");
            }

            /* ===============================================================
               5 — CAJA
            =============================================================== */
            $caja = new Caja('', '', '', '', '', '', '', $conn);
            $cajaActiva = $caja->verificar_o_abrir_caja_mensual($_SESSION['idusuarios']);

            if (!$cajaActiva) {
                throw new Exception("No hay caja activa.");
            }

            $datosCaja = $cajaActiva->fetch_assoc();
            $idcaja = $datosCaja['idcaja'];

            $idTipoVenta = $caja->obtener_id_tipo_movimiento('Venta vehículo');
            $idTipoComision = $caja->obtener_id_tipo_movimiento('Comisión empleado');

            // Movimiento ingreso
            $caja->registrar_movimiento(
                'ingreso',
                $monto_conces,
                "Venta ID $idventa del vehiculo ID {$_POST['vehiculos_idvehiculos']}",
                'ventas',
                $idventa,
                $idcaja,
                $idTipoVenta,
                $_POST['tipo_pago'],
                $_SESSION['idusuarios']
            );

            // Movimiento egreso
            if ($monto_emp > 0) {
                $caja->registrar_movimiento(
                    'egreso',
                    $monto_emp,
                    "Comisión empleado ID {$_POST['idempleado']}",
                    'comisiones_ventas',
                    $idventa,
                    $idcaja,
                    $idTipoComision,
                    $_POST['tipo_pago'],
                    $_SESSION['idusuarios']
                );
            }

            /* ===============================================================
               6 — VEHÍCULO vendido
            =============================================================== */
            $vehiculo = new Vehiculos('', '', '', '', '', '', '', '', '', '', $conn);
            $vehiculo->setIdvehiculos($_POST['vehiculos_idvehiculos']);
            $vehiculo->eliminar_vehiculo_venta(); // ✔ versión correcta para transacciones

            /* ===============================================================
               7 — NOTIFICACIÓN
            =============================================================== */
            require_once __DIR__ . '/../notificacion_trigger.php';

            $pusher->trigger('notificaciones', 'nuevo-evento', [
                'tipo' => 'venta',
                'mensaje' => "Nueva venta ID $idventa",
                'fecha' => date('Y-m-d H:i:s')
            ]);

            /* ===============================================================
               8 — CONFIRMAR TODO
            =============================================================== */
            $conn->commit();

            header('Location: ../../index.php?page=listado_ventas&id='.$idventa.'&mensaje=Venta registrada correctamente.&status=success');
            exit();

        } catch (Exception $e) {

            $conn->rollback();
            error_log("Error en registrar_venta: " . $e->getMessage());

            header('Location: ../../index.php?page=registrar_ventas&mensaje='.urlencode($e->getMessage()).'&status=error');
            exit();

        } finally {
            $db->desconectar();
        }
    }
}

