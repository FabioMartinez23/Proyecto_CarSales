<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_pago.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_pago_controlador = new Tipo_PagoControlador();
        $tipo_pago_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_pago_controlador = new Tipo_PagoControlador();
        $tipo_pago_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_pago_controlador = new Tipo_PagoControlador();
        $tipo_pago_controlador->eliminar();
    }
}

class Tipo_PagoControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_pago&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_pago = new Tipo_Pagos();
        $tipo_pago->setDescripcion($_POST['descripcion']);
        $tipo_pago->agregar_tipo_pago();
        header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_pago&mensaje=Tipo de Pago registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_pago = new Tipo_Pagos();
        $tipo_pago->setIdtipo_pago($_POST['idtipo_pago']);
        $tipo_pago->eliminar_tipo_pago();
        header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_pago&mensaje=Tipo de Pago eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_pago = new Tipo_Pagos();
        $tipo_pago->setIdtipo_pago($_POST['idtipo_pago']);
        $tipo_pago->setDescripcion($_POST['descripcion']);
        $tipo_pago->actualizar_tipo_pago();
        header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_pago&mensaje=Tipo de Pago modificado correctamente.&status=success&idtipo_pago='.$_POST['idtipo_pago']);
    }
    
}