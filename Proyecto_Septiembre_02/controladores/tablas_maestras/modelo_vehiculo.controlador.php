<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/modelo_vehiculo.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $modelo_vehiculo_controlador = new Modelos_VehiculosControlador();
        $modelo_vehiculo_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $modelo_vehiculo_controlador = new Modelos_VehiculosControlador();
        $modelo_vehiculo_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $modelo_vehiculo_controlador = new Modelos_VehiculosControlador();
        $modelo_vehiculo_controlador->eliminar();
    }
}

class Modelos_VehiculosControlador{
    public function guardar(){

        if(empty($_POST['nombre'] || empty($_POST['marcas_idmarcas'] || empty($_POST['tipo_vehiculos_idtipo_vehiculos'])))){
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