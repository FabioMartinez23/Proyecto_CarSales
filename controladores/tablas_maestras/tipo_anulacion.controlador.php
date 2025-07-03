<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_anulacion.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_anulacion_controlador = new Tipo_AnulacionControlador();
        $tipo_anulacion_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_anulacion_controlador = new Tipo_AnulacionControlador();
        $tipo_anulacion_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_anulacion_controlador = new Tipo_AnulacionControlador();
        $tipo_anulacion_controlador->eliminar();
    }
}

class Tipo_AnulacionControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_anulacion&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_anulacion= new Tipo_Anulaciones();
        $tipo_anulacion->setDescripcion($_POST['descripcion']);
        $tipo_anulacion->agregar_tipo_anulacion();
        header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_anulacion&mensaje=Tipo de Anulación registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_anulacion = new Tipo_Anulaciones();
        $tipo_anulacion->setIdtipo_anulacion($_POST['idtipo_anulacion']);
        $tipo_anulacion->eliminar_tipo_anulacion();
        header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_anulacion&mensaje=Tipo de Anulación eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_anulacion = new Tipo_Anulaciones();
        $tipo_anulacion->setIdtipo_anulacion($_POST['idtipo_anulacion']);
        $tipo_anulacion->setDescripcion($_POST['descripcion']);
        $tipo_anulacion->modificar_tipo_anulacion();
        header('location: ../../vistas/form_datos_para_operaciones.php?page=form_tipo_anulacion&mensaje=Tipo de Anulación modificado correctamente.&status=success&idtipo_anulacion='.$_POST['idtipo_anulacion']);
    }
    
}