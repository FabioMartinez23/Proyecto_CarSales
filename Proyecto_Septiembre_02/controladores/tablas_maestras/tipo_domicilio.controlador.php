<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_domicilio.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_domicilio_controlador = new Tipo_DomicilioControlador();
        $tipo_domicilio_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_domicilio_controlador = new Tipo_DomicilioControlador();
        $tipo_domicilio_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_domicilio_controlador = new Tipo_DomicilioControlador();
        $tipo_domicilio_controlador->eliminar();
    }
}

class Tipo_DomicilioControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_domicilio&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_domicilio = new Tipo_Domicilios();
        $tipo_domicilio->setDescripcion($_POST['descripcion']);
        $tipo_domicilio->agregar_tipo_domicilio();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_domicilio&mensaje=Tipo de domicilio registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_domicilio = new Tipo_Domicilios();
        $tipo_domicilio->setIdtipo_domicilio($_POST['idtipo_domicilio']);
        $tipo_domicilio->eliminar_tipo_domicilio();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_domicilio&mensaje=Tipo de domicilio eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_domicilio = new Tipo_Domicilios();
        $tipo_domicilio->setIdtipo_domicilio($_POST['idtipo_domicilio']);
        $tipo_domicilio->setDescripcion($_POST['descripcion']);
        $tipo_domicilio->actualizar_tipo_domicilio();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_domicilio&idtipo_domicilio='.$_POST['idtipo_domicilio']);
    }
    
}
