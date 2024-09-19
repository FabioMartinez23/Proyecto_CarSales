<?php

ini_set('display_errors', 1);
require_once('../../modelos/vehiculos.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $vehiculo_controlador = new VehiculosControlador();
        $vehiculo_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $vehiculo_controlador = new VehiculosControlador();
        $vehiculo_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $vehiculo_controlador = new VehiculosControlador();
        $vehiculo_controlador->eliminar();
    }
}

class VehiculosControlador{
    public function guardar(){

        if(empty($_POST['patente']) || empty($_POST['chasis']) || empty($_POST['motor']) || empty($_POST['patente']) || empty($_POST['chasis']) || empty($_POST['motor'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_modelo_vehiculo&mensaje=Todos los datos obligarios.&status=danger');
        }
        $modelo_vehiculo = new Modelos_Vehiculos();
        $modelo_vehiculo->setNombre($_POST['nombre']);
        $modelo_vehiculo->setMarcas_idmarcas($_POST['marcas_idmarcas']);
        $modelo_vehiculo->setTipo_vehiculos_idtipo_vehiculos($_POST['tipo_vehiculos_idtipo_vehiculos']);
        $modelo_vehiculo->agregar_modelo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_modelo_vehiculo&mensaje=Modelo registrado correctamente.&status=success');
    }

    public function eliminar(){
        $modelo_vehiculo = new Modelos_Vehiculos();
        $modelo_vehiculo->setIdmodelos($_POST['idmodelos']);
        $modelo_vehiculo->eliminar_modelo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_modelo_vehiculo&mensaje=Modelo eliminado correctamente.&status=success');
    }

    public function modificar(){
        $modelo_vehiculo = new Modelos_Vehiculos();
        $modelo_vehiculo->setIdmodelos($_POST['idmodelos']);
        $modelo_vehiculo->setNombre($_POST['nombre']);
        $modelo_vehiculo->setMarcas_idmarcas($_POST['marcas_idmarcas']);
        $modelo_vehiculo->setTipo_vehiculos_idtipo_vehiculos($_POST['tipo_vehiculos_idtipo_vehiculos']);
        $modelo_vehiculo->actualizar_modelo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_modelo_vehiculo&idmodelos='.$_POST['idmodelos']);
    }
    
}