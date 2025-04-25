<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/localidad.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $localidad_controlador = new LocalidadesControlador();
        $localidad_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $localidad_controlador = new LocalidadesControlador();
        $localidad_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $localidad_controlador = new LocalidadesControlador();
        $localidad_controlador->eliminar();
    }
}

class LocalidadesControlador{
    public function guardar(){

        if(empty($_POST['descripcion'] || empty($_POST['provincias_idprovincias']))){
            header('location: ../../vistas/form_datos_para_domicilios.php?page=form_localidad&mensaje=Todos los datos obligarios.&status=danger');
        }
        $localidad = new Localidades();
        $localidad->setDescripcion($_POST['descripcion']);
        $localidad->setProvincias_idprovincias($_POST['provincias_idprovincias']);
        $localidad->agregar_localidad();
        header('location: ../../vistas/form_datos_para_domicilios.php?page=form_localidad&mensaje=Localidad registrada correctamente.&status=success');
    }

    public function eliminar(){
        $localidad = new Localidades();
        $localidad->setIdlocalidades($_POST['idlocalidades']);
        $localidad->eliminar_localidad();
        header('location: ../../vistas/form_datos_para_domicilios.php?page=form_localidad&mensaje=Localidad eliminada correctamente.&status=success');
    }

    public function modificar(){
        $localidad = new Localidades();
        $localidad->setIdlocalidades($_POST['idlocalidades']);
        $localidad->setDescripcion($_POST['descripcion']);
        $localidad->setProvincias_idprovincias($_POST['provincias_idprovincias']);
        $localidad->actualizar_localidad();
        header('location: ../../vistas/form_datos_para_domicilios.php?page=form_localidad&mensaje=Localidad modificada correctamente.&status=success&idlocalidades='.$_POST['idlocalidades']);
    }
    
}