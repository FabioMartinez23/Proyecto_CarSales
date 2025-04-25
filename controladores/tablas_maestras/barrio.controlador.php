<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/barrio.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $barrio_controlador = new BarriosControlador();
        $barrio_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $barrio_controlador = new BarriosControlador();
        $barrio_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $barrio_controlador = new BarriosControlador();
        $barrio_controlador->eliminar();
    }
}

class BarriosControlador{
    public function guardar(){

        if(empty($_POST['descripcion'] || empty($_POST['localidades_idlocalidades']))){
            header('location: ../../vistas/form_datos_para_domicilios.php?page=form_barrio&mensaje=Todos los datos obligarios.&status=danger');
        }
        $barrio = new Barrios();
        $barrio->setDescripcion($_POST['descripcion']);
        $barrio->setLocalidades_idlocalidades($_POST['localidades_idlocalidades']);
        $barrio->agregar_barrio();
        header('location: ../../vistas/form_datos_para_domicilios.php?page=form_barrio&mensaje=Barrio registrado correctamente.&status=success');
    }

    public function eliminar(){
        $barrio = new Barrios();
        $barrio->setIdbarrios($_POST['idbarrios']);
        $barrio->eliminar_barrio();
        header('location: ../../vistas/form_datos_para_domicilios.php?page=form_barrio&mensaje=Barrio eliminado correctamente.&status=success');
    }

    public function modificar(){
        $barrio = new Barrios();
        $barrio->setIdbarrios($_POST['idbarrios']);
        $barrio->setDescripcion($_POST['descripcion']);
        $barrio->setLocalidades_idlocalidades($_POST['localidades_idlocalidades']);
        $barrio->actualizar_barrio();
        header('location: ../../vistas/form_datos_para_domicilios.php?page=form_barrio&mensaje=Barrio modificado correctamente.&status=success&idbarrios='.$_POST['idbarrios']);
    }
    
}