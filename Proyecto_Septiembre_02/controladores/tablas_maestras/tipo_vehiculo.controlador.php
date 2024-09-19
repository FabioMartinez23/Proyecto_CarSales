<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_vehiculo.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_vehiculo_controlador = new Tipo_VehiculoControlador();
        $tipo_vehiculo_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_vehiculo_controlador = new Tipo_VehiculoControlador();
        $tipo_vehiculo_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_vehiculo_controlador = new Tipo_VehiculoControlador();
        $tipo_vehiculo_controlador->eliminar();
    }
}

class Tipo_VehiculoControlador{
    public function guardar(){

        if(empty($_POST['nombre'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_vehiculo&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_vehiculo = new Tipo_Vehiculos();
        $tipo_vehiculo->setNombre($_POST['nombre']);
        $tipo_vehiculo->agregar_tipo_vehiculo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_vehiculo&mensaje=Tipo de Sexo registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_vehiculo = new Tipo_Vehiculos();
        $tipo_vehiculo->setIdtipo_vehiculos($_POST['idtipo_vehiculos']);
        $tipo_vehiculo->eliminar_tipo_vehiculo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_vehiculo&mensaje=Tipo de Sexo eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_vehiculo = new Tipo_Vehiculos();
        $tipo_vehiculo->setIdtipo_vehiculos($_POST['idtipo_vehiculos']);
        $tipo_vehiculo->setNombre($_POST['nombre']);
        $tipo_vehiculo->actualizar_tipo_vehiculo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_vehiculo&idtipo_vehiculos='.$_POST['idtipo_vehiculos']);
    }
    
}
