<?php

require_once('../../modelos/vehiculos.php');
require_once('../../modelos/vender_vehiculos.php');
require_once('../../modelos/anular_operaciones.php');

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'anular_venta') {
        $anular_venta = new AnularVentaControlador();
        $anular_venta->anular_venta();
    }
}

class AnularVentaControlador{

    public function anular_venta(){
        if (empty($_POST['fecha_anulacion'])) {
            header('Location: ../../index.php?page=anular_ventas&mensaje=Debe ingresar una fecha de anulación.&status=error&idventa=' . $_POST['idventa']);
            exit;
        }

        // Convertir las fechas a objetos DateTime para compararlas
        $fecha_anulacion = new DateTime($_POST['fecha_anulacion']);
        $fecha_actual = new DateTime(); // Hoy

        // Validar que no sea una fecha futura
        if ($fecha_anulacion > $fecha_actual) {
            header('Location: ../../index.php?page=anular_ventas&mensaje=La fecha de anulación no puede ser mayor a la fecha actual.&status=error&idventa=' . $_POST['idventa']);
            exit;
        } else{
            $recuperar_vehiculo = new VenderVehiculo();
            $result_recuperar = $recuperar_vehiculo->traer_venta_para_anular($_POST['idventa']);
            if($result_recuperar){
                $idvehiculo = $result_recuperar['vehiculo_idvehiculo'];
                $anular_venta = new AnularOperacion();
                $anular_venta->setFecha_anulacion($_POST['fecha_anulacion']);
                $anular_venta->setTipo_anulacion_idtipo_anulacion($_POST['tipo_anulacion']);
                $anular_venta->setVentas_idventas($_POST['idventa']);
                $anular_venta->setUsuarios_idusuarios($_POST['idusuario']);
                if($result_anulacion = $anular_venta->anular_venta($idvehiculo)){
                    header('Location: ../../index.php?page=anular_ventas&mensaje=Se anulo correctamente.&status=success&idventa='. $_POST['idventa'] . '&idanulacion=' . $result_anulacion);
                    exit;
                }
            }
        }
    }
}


//var_dump($_POST);
//exit();