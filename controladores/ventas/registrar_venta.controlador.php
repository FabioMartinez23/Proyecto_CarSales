<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../modelos/vehiculos.php');
require_once('../../modelos/precios_vehiculos.php');
require_once('../../modelos/personas.php');
require_once('../../modelos/domicilio.php');
require_once('../../modelos/documentos.php');
require_once('../../modelos/usuarios.php');
require_once('../../modelos/contactos.php');
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/registro_clientes.php');

//header('Content-Type: application/json'); // Establecer el tipo de contenido a JSON

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'registrar_venta') {
        $venta_controlador = new RegistrarVentaControlador();
        $venta_controlador->registrar_venta();
    }
}


    class RegistrarVentaControlador {
        public function registrar_venta() {
            $consulta_cliente = new RegistroCliente();
            $result_consulta = $consulta_cliente->traer_cliente_por_id($_POST['id_usuario']);
        
            // Verificar si el cliente ya existe
            if ($result_consulta) {
                // El cliente ya existe, obtener su ID
                $cliente_id = $result_consulta['idregistro_clientes'];
            } else {
                // El cliente no existe, registrar nuevo cliente
                $registrar_cliente = new RegistroCliente();
                $registrar_cliente->setUsuarios_idusuarios($_POST['id_usuario']);
                
                if (!$cliente_id = $registrar_cliente->agregar_cliente()) {
                    header('location: ../../index.php?page=registrar_ventas&mensaje=Error al registrar cliente.&status=error');
                    exit();
                }
            }
        
            // Ahora registrar la venta
            $registrar_venta = new VenderVehiculo();
            $registrar_venta->setDescripcion($_POST['observaciones']);
            $registrar_venta->setTipo_pago_idtipo_pago($_POST['tipo_pago']);
            $registrar_venta->setVehiculo_idvehiculo($_POST['vehiculos_idvehiculos']);
            $registrar_venta->setRegistro_clientes_idregistro_clientes($cliente_id);
        
            // Registrar la venta y obtener el ID
            if (!$idventa = $registrar_venta->agregar_venta()) {
                header('location: ../../index.php?page=registrar_ventas&mensaje=Error al registrar la venta.&status=error');
                exit();
            }
        
            // Comprobar que $idventa no sea nulo o cero
            if ($idventa) {
                // Eliminar el vehículo que participó en la venta
                $eliminar_vehiculo = new Vehiculos();
                $eliminar_vehiculo->setIdvehiculos($_POST['vehiculos_idvehiculos']);
                $result_eliminado = $eliminar_vehiculo->eliminar_vehiculo(); // Pasa el ID del vehículo aquí
        
                // Verificar si la eliminación fue exitosa
                if ($result_eliminado) {
                    // Redirigir con mensaje de éxito y mostrar el ID de la venta
                    header('Location: ../../index.php?page=listado_ventas&id=' . $idventa . '&mensaje=Registro de Ventas Exitoso!&status=success');
                } else {
                    // Manejar el error de eliminación
                    header('Location: ../../index.php?page=listado_ventas&id=' . $idventa . '&mensaje=La venta se registró, pero no se pudo eliminar el vehículo.&status=warning');
                }
                exit();
            } else {
                echo "Error: No se pudo obtener el ID de la venta";
                exit();
            }
        }
        
    }


