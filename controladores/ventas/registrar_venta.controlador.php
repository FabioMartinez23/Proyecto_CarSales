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
require_once('../../modelos/gastos_generales.php');   // <<< AGREGADO
require_once('../../modelos/conexion.php');

if (isset($_POST['action']) && $_POST['action'] == 'registrar_venta') {
    $venta_controlador = new RegistrarVentaControlador();
    $venta_controlador->registrar_venta();
}

class RegistrarVentaControlador {

    public function registrar_venta() {

        /* ===============================================================
           🔹 1 — CONEXIÓN GLOBAL PARA TODA LA TRANSACCIÓN
        =============================================================== */
        $db = new Conexion();
        $db->conectar();
        $conn = $db->_con;

        $conn->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
        $conn->begin_transaction();

        try {

            // ⭐ Tipo de pago (1=Efectivo, 2=Transferencia, 3=Crédito Bancario, 4=Reverso/Ajuste)
            $tipoPago = isset($_POST['tipo_pago']) ? (int) $_POST['tipo_pago'] : 1;

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

                if (!$cliente_id) throw new Exception("Error al registrar cliente.");
            }

            /* ===============================================================
               3 — REGISTRAR LA VENTA
            =============================================================== */
            $venta = new VenderVehiculo('', '', '', '', '', '', '', '', $conn);
            $venta->setDescripcion($_POST['observaciones']);
            $venta->setTipo_pago_idtipo_pago($tipoPago); // ⭐ usamos $tipoPago
            $venta->setVehiculo_idvehiculo($_POST['vehiculos_idvehiculos']);
            $venta->setRegistro_clientes_idregistro_clientes($cliente_id);
            $venta->setEmpleados_idempleados($_POST['idempleado']);
            $venta->setTitular_vehiculo_idtitular_vehiculo($_POST['titular_vehiculo']);
            $venta->setPrecio_venta($_POST['precio_publico_real']);

            if ($tipoPago === 3) { // 3 = Crédito Bancario
                // Estado general de la venta
                $venta->setEstado_venta('Pendiente crédito');
                $venta->setEstado_venta_credito('pendiente');

                // Datos de crédito
                $venta->setBanco_credito($_POST['banco_credito'] ?? null);

                // Podés usar el textarea de "nota_credito" como observación del crédito
                $observacionCredito = $_POST['nota_credito'] ?? '';

                // Si además querés incluir otras cosas (monto estimado, referencia, etc.)
                if (!empty($_POST['monto_estimado_credito'])) {
                    $observacionCredito .= "\nMonto estimado: " . $_POST['monto_estimado_credito'];
                }
                if (!empty($_POST['referencia_credito'])) {
                    $observacionCredito .= "\nRef. gestión banco: " . $_POST['referencia_credito'];
                }

                $venta->setObservacion_credito($observacionCredito ?: null);

                // Fecha de solicitud = ahora
                $venta->setFecha_solicitud_credito(date('Y-m-d H:i:s'));

                // La respuesta y el monto aprobado se llenarán después en otra pantalla
            } else {
                // Venta normal (efectivo / transferencia)
                $venta->setEstado_venta('Realizada');
                $venta->setEstado_venta_credito('ninguno');
            }

            // 🔹 Más adelante (otra etapa) acá podemos:
            // - Si $tipoPago == 3 (Crédito Bancario), setear estado_venta = 'Pendiente crédito'
            //   con algún método tipo $venta->setEstadoVenta('Pendiente crédito');

            $idventa = $venta->agregar_venta();
            if (!$idventa) throw new Exception("Error al registrar la venta.");

            /* ===============================================================
               4 — COMISIONES (SE CALCULAN SIEMPRE)
            =============================================================== */
            $precio_tomado  = floatval($_POST['precio_tomado'] ?? 0);
            $precio_publico = floatval($_POST['precio_publico_real'] ?? 0);

            if ($precio_publico <= 0 || $precio_tomado <= 0)
                throw new Exception("No se pudieron obtener los precios del vehículo.");

            $ganancia = max($precio_publico - $precio_tomado, 0);

            // Perfil del empleado
            $usuario = new Usuario('', '', '', '', '', '', $conn);
            $perfil_data = $usuario->traer_perfil_por_id($_POST['idempleado']);
            $perfil = strtolower(trim($perfil_data['perfil'] ?? ''));

            if (strpos($perfil, 'admin') !== false) {
                $porc_emp = 0;
                $porc_conces = 100;
                $tipo_comision = 1;
            } else {
                $porc_emp = 15;
                $porc_conces = 85;
                $tipo_comision = 2;
            }

            $monto_emp = ($ganancia * $porc_emp) / 100;
            $monto_conces = ($ganancia * $porc_conces) / 100;

            // Registrar comisión (teórica / esperada) ⭐
            $comision = new Comisiones_Ventas('', '', '', '', '', '', '', '', $conn);
            $comision->setPorcentaje_empleado($porc_emp);
            $comision->setPorcentaje_concesionaria($porc_conces);
            $comision->setMonto_empleado($monto_emp);
            $comision->setMonto_concesionaria($monto_conces);
            $comision->setTipo_comisiones_idtipo_comisiones($tipo_comision);
            $comision->setVentas_idventas($idventa);

            if (!$comision->agregar_comision_venta())
                throw new Exception("Error al registrar comisión.");

            /* ===============================================================
               5 — CAJA
               🔸 Efectivo / Transferencia: igual que siempre.
               🔸 Crédito Bancario: NO MOVEMOS CAJA TODAVÍA.
            =============================================================== */

            if ($tipoPago !== 3) { // ⭐ Solo si NO es Crédito Bancario

                $caja = new Caja('', '', '', '', '', '', '', $conn);
                $cajaActiva = $caja->verificar_o_abrir_caja_mensual($_SESSION['idusuarios']);

                if (!$cajaActiva) throw new Exception("No hay caja activa.");

                $datosCaja = $cajaActiva->fetch_assoc();
                $idcaja = $datosCaja['idcaja'];

                $idTipoVenta = $caja->obtener_id_tipo_movimiento('Venta vehículo');
                $idTipoComision = $caja->obtener_id_tipo_movimiento('Comisión empleado');

                // INGRESO de la concesionaria (comisión)
                $caja->registrar_movimiento(
                    'ingreso',
                    $monto_conces,
                    "Venta ID $idventa del vehiculo ID {$_POST['vehiculos_idvehiculos']}",
                    'ventas',
                    $idventa,
                    $idcaja,
                    $idTipoVenta,
                    $tipoPago,           // ⭐ usamos $tipoPago
                    $_SESSION['idusuarios']
                );

                // EGRESO comisión empleado
                if ($monto_emp > 0) {
                    $caja->registrar_movimiento(
                        'egreso',
                        $monto_emp,
                        "Comisión empleado ID {$_POST['idempleado']}",
                        'comisiones_ventas',
                        $idventa,
                        $idcaja,
                        $idTipoComision,
                        $tipoPago,       // ⭐ usamos $tipoPago
                        $_SESSION['idusuarios']
                    );
                }
            }
            // Si es Crédito Bancario (3) no se hace ningún movimiento de caja aquí.
            // Más adelante, cuando el banco confirme, registraremos esos movimientos
            // en otro controlador o flujo (Aprobación de crédito).

            /* ===============================================================
               6 — GASTOS AUTOMÁTICOS DE VENTA (AQUÍ) - NO CORRESPONDE
            =============================================================== */

            /* ===============================================================
            7 — VEHÍCULO VENDIDO / RESERVADO POR CRÉDITO
            =============================================================== */
            $vehiculo = new Vehiculos('', '', '', '', '', '', '', '', '', '', $conn);
            $vehiculo->setIdvehiculos($_POST['vehiculos_idvehiculos']);

            if ($tipoPago === 3) {
                // ⭐ Crédito bancario:
                // No lo damos de baja, solo lo marcamos como RESERVADO CREDITO
                if (!$vehiculo->marcar_reservado_credito()) {
                    throw new Exception("No se pudo marcar el vehículo como 'Reservado Credito'.");
                }
            } else {
                // 💵 Efectivo / Transferencia: comportamiento anterior (vendido / baja lógica)
                $vehiculo->eliminar_vehiculo_venta();
            }


            /* ===============================================================
               8 — NOTIFICACIÓN
            =============================================================== */
            require_once __DIR__ . '/../notificacion_trigger.php';
            $pusher->trigger('notificaciones', 'nuevo-evento', [
                'tipo' => 'venta',
                'mensaje' => "Nueva venta ID $idventa",
                'fecha' => date('Y-m-d H:i:s')
            ]);

            /* ===============================================================
               9 — COMMIT FINAL
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
