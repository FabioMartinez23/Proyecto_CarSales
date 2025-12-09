<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
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
            8.1 — ENVÍO DE CORREOS (CLIENTE + ADMIN)
            NOTA: Si fallan, NO se revierte la venta.
            =============================================================== */

            try {
                // Datos base para el correo
                $idVehiculo   = (int) $_POST['vehiculos_idvehiculos'];
                $precioVenta  = (float) $_POST['precio_publico_real'];
                $formaPago    = $tipoPago;             // 1,2,3...
                $observacion  = $_POST['observaciones'] ?? '';

                // Correo al cliente
                $this->enviarCorreoVentaCliente(
                    $conn,
                    $idventa,
                    $_POST['id_usuario'],  // id del usuario cliente
                    $idVehiculo,
                    $precioVenta,
                    $formaPago,
                    $observacion
                );

                // Correo al administrador
                $this->enviarCorreoVentaAdmin(
                    $conn,
                    $idventa,
                    $_POST['id_usuario'],  // id del usuario cliente
                    $idVehiculo,
                    $precioVenta,
                    $formaPago,
                    $observacion
                );

            } catch (Exception $e) {
                // No hacemos rollback por temas de email
                error_log("Error al enviar correos de venta ID $idventa: " . $e->getMessage());
            }

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


    /* ============================================================
    📧 CORREO AL CLIENTE: CONFIRMACIÓN DE VENTA
    ============================================================ */
    private function enviarCorreoVentaCliente($conn, $idventa, $idUsuario, $idVehiculo, $precioVenta, $tipoPago, $observaciones = '')
    {
        // Base URL de tu sistema
        $baseUrl   = 'http://localhost/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02';
        $linkVenta = $baseUrl . "/index.php?page=listado_ventas&id=" . (int)$idventa;

        // Traer datos del usuario/cliente
        $usuarioModel = new Usuario('', '', '', '', '', '', $conn);
        $usuarioData  = $usuarioModel->traer_usuario_por_id($idUsuario);

        if (!$usuarioData || empty($usuarioData['email'])) {
            throw new Exception("No se encontró email del cliente para enviar confirmación de venta.");
        }

        $emailCliente = $usuarioData['email'];
        $nombreCliente = trim(($usuarioData['nombre'] ?? '') . ' ' . ($usuarioData['apellido'] ?? ''));

        // Forma de pago descriptiva
        switch ($tipoPago) {
            case 1: $textoPago = 'Efectivo'; break;
            case 2: $textoPago = 'Transferencia bancaria'; break;
            case 3: $textoPago = 'Crédito bancario'; break;
            default: $textoPago = 'Otro'; break;
        }

        $mail = new PHPMailer(true);

        try {
            // Config SMTP Brevo
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '9d6f3f001@smtp-brevo.com';
            $mail->Password   = 'mqA4CSBVnGxgDZLO';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y destinatario
            $mail->setFrom('famartinez2611994@gmail.com', 'CarSales - Ventas');
            $mail->addAddress($emailCliente, $nombreCliente ?: 'Cliente CarSales');

            $mail->isHTML(true);
            $mail->Subject = "Confirmación de operación - Venta N° $idventa";

            $nombreEsc = htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8');
            $obsEsc    = nl2br(htmlspecialchars($observaciones, ENT_QUOTES, 'UTF-8'));

            $mail->Body = "
            <div style='background:#f4f4f8;padding:20px;font-family:Arial,sans-serif;color:#333;'>
                <div style='max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;
                            box-shadow:0 4px 15px rgba(0,0,0,0.08);overflow:hidden;'>
                    
                    <div style='background:#333A56;padding:20px;text-align:center;'>
                        <h2 style='color:#ffffff;margin:0;'>Gracias por tu confianza en CarSales</h2>
                    </div>

                    <div style='padding:25px;'>
                        <p style='font-size:15px;margin-top:0;'>
                            Hola <strong>{$nombreEsc}</strong>,
                        </p>

                        <p style='font-size:14px;line-height:1.6;'>
                            Te confirmamos que hemos registrado tu operación de venta en <strong>CarSales</strong>.
                        </p>

                        <table style='font-size:13px;border-collapse:collapse;width:100%;margin-top:10px;'>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;width:150px;'>N° de operación:</td>
                                <td style='padding:6px 4px;'>$idventa</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>ID Vehículo:</td>
                                <td style='padding:6px 4px;'>$idVehiculo</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>Importe de venta:</td>
                                <td style='padding:6px 4px;'>$ " . number_format($precioVenta, 2, ',', '.') . "</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>Forma de pago:</td>
                                <td style='padding:6px 4px;'>$textoPago</td>
                            </tr>
                        </table>";

            if (!empty($observaciones)) {
                $mail->Body .= "
                        <p style='font-size:13px;margin-top:15px;'>
                            <strong>Observaciones:</strong><br>
                            {$obsEsc}
                        </p>";
            }

            $mail->Body .= "
                        <p style='font-size:13px;margin-top:20px;'>
                            Podés consultar el detalle de tu operación ingresando al sistema.
                        </p>

                        <div style='text-align:center;margin:30px 0;'>
                            <a href='{$linkVenta}' 
                            style='background:#52658F;color:#ffffff;text-decoration:none;
                                    padding:12px 25px;border-radius:30px;font-size:14px;
                                    display:inline-block;'>
                                Ver detalle de la venta
                            </a>
                        </div>

                        <p style='font-size:11px;color:#888;'>
                            Este correo es solo informativo. No respondas a este mensaje.
                        </p>
                    </div>

                    <div style='background:#f0f0f5;padding:10px 20px;text-align:center;font-size:11px;color:#777;'>
                        © " . date('Y') . " CarSales · Sistema de gestión de vehículos
                    </div>
                </div>
            </div>
            ";

            $mail->AltBody = "Hola {$nombreCliente},\n\n"
                . "Te confirmamos que se ha registrado tu operación en CarSales.\n\n"
                . "N° de operación: $idventa\n"
                . "ID Vehículo: $idVehiculo\n"
                . "Importe de venta: $ " . number_format($precioVenta, 2, ',', '.') . "\n"
                . "Forma de pago: $textoPago\n\n"
                . "Podés consultar el detalle ingresando al sistema.\n\n"
                . "CarSales.";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Error PHPMailer venta cliente: ' . $mail->ErrorInfo);
            // No lanzamos excepción hacia arriba para no romper la venta
            return false;
        }
    }

    /* ============================================================
    📧 CORREO AL ADMIN: AVISO NUEVA VENTA
    ============================================================ */
    private function enviarCorreoVentaAdmin($conn, $idventa, $idUsuario, $idVehiculo, $precioVenta, $tipoPago, $observaciones = '')
    {
        $adminEmail = 'famartinez2611994@gmail.com'; // podés parametrizarlo si querés

        // Base URL de tu sistema
        $baseUrl   = 'http://localhost/2do_Cuatrimestre/PP_2/Proyecto_Septiembre_02';
        $linkVenta = $baseUrl . "/index.php?page=listado_ventas&id=" . (int)$idventa;

        // Datos del cliente
        $usuarioModel = new Usuario('', '', '', '', '', '', $conn);
        $usuarioData  = $usuarioModel->traer_usuario_por_id($idUsuario);

        $emailCliente  = $usuarioData['email'] ?? '';
        $nombreCliente = trim(($usuarioData['nombre'] ?? '') . ' ' . ($usuarioData['apellido'] ?? ''));

        switch ($tipoPago) {
            case 1: $textoPago = 'Efectivo'; break;
            case 2: $textoPago = 'Transferencia bancaria'; break;
            case 3: $textoPago = 'Crédito bancario'; break;
            default: $textoPago = 'Otro'; break;
        }

        $mail = new PHPMailer(true);

        try {
            // Config SMTP Brevo
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '9d6f3f001@smtp-brevo.com';
            $mail->Password   = 'mqA4CSBVnGxgDZLO';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            // Remitente y destinatario
            $mail->setFrom('famartinez2611994@gmail.com', 'CarSales - Sistema');
            $mail->addAddress($adminEmail, 'Administrador CarSales');

            $mail->isHTML(true);
            $mail->Subject = "Nueva venta registrada - ID $idventa";

            $nombreCliEsc = htmlspecialchars($nombreCliente, ENT_QUOTES, 'UTF-8');
            $emailCliEsc  = htmlspecialchars($emailCliente, ENT_QUOTES, 'UTF-8');
            $obsEsc       = nl2br(htmlspecialchars($observaciones, ENT_QUOTES, 'UTF-8'));

            $mail->Body = "
            <div style='background:#f4f4f8;padding:20px;font-family:Arial,sans-serif;color:#333;'>
                <div style='max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;
                            box-shadow:0 4px 15px rgba(0,0,0,0.08);overflow:hidden;'>
                    
                    <div style='background:#333A56;padding:20px;text-align:center;'>
                        <h2 style='color:#ffffff;margin:0;'>Nueva venta registrada</h2>
                    </div>

                    <div style='padding:25px;'>
                        <p style='font-size:14px;line-height:1.6;'>
                            Se ha registrado una nueva venta en <strong>CarSales</strong>.
                        </p>

                        <table style='font-size:13px;border-collapse:collapse;width:100%;margin-top:10px;'>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;width:150px;'>ID Venta:</td>
                                <td style='padding:6px 4px;'>$idventa</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>ID Vehículo:</td>
                                <td style='padding:6px 4px;'>$idVehiculo</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>Importe de venta:</td>
                                <td style='padding:6px 4px;'>$ " . number_format($precioVenta, 2, ',', '.') . "</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>Forma de pago:</td>
                                <td style='padding:6px 4px;'>$textoPago</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>Cliente:</td>
                                <td style='padding:6px 4px;'>{$nombreCliEsc}</td>
                            </tr>
                            <tr>
                                <td style='padding:6px 4px;font-weight:bold;'>Email cliente:</td>
                                <td style='padding:6px 4px;'>{$emailCliEsc}</td>
                            </tr>
                        </table>";

            if (!empty($observaciones)) {
                $mail->Body .= "
                        <p style='font-size:13px;margin-top:15px;'>
                            <strong>Observaciones:</strong><br>
                            {$obsEsc}
                        </p>";
            }

            $mail->Body .= "
                        <div style='text-align:center;margin:30px 0;'>
                            <a href='{$linkVenta}' 
                            style='background:#52658F;color:#ffffff;text-decoration:none;
                                    padding:12px 25px;border-radius:30px;font-size:14px;
                                    display:inline-block;'>
                                Ver venta en el sistema
                            </a>
                        </div>

                        <p style='font-size:11px;color:#888;'>
                            Este correo es solo informativo.
                        </p>
                    </div>

                    <div style='background:#f0f0f5;padding:10px 20px;text-align:center;font-size:11px;color:#777;'>
                        © " . date('Y') . " CarSales · Sistema de gestión de vehículos
                    </div>
                </div>
            </div>
            ";

            $mail->AltBody = "Nueva venta registrada en CarSales\n\n"
                . "ID Venta: $idventa\n"
                . "ID Vehículo: $idVehiculo\n"
                . "Importe de venta: $ " . number_format($precioVenta, 2, ',', '.') . "\n"
                . "Forma de pago: $textoPago\n"
                . "Cliente: $nombreCliente\n"
                . "Email cliente: $emailCliente\n";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Error PHPMailer venta admin: ' . $mail->ErrorInfo);
            return false;
        }
    }

}
