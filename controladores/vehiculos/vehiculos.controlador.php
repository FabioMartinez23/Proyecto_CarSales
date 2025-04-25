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

        if(empty($_POST['patente']) || empty($_POST['chasis']) || empty($_POST['motor']) || empty($_POST['año']) || empty($_POST['kilometraje']) || empty($_POST['modelos_idmodelos']) || empty($_POST['colores_idcolores']) || empty($_POST['tipo_vehiculos_idtipo_vehiculos'])){
            header('location: ../../index.php?page=registrar_vehiculos&mensaje=Todos los datos obligarios.&status=error');
        }
        $vehiculo = new Vehiculos();
        $vehiculo->setPatente($_POST['patente']);
        $vehiculo->setChasis($_POST['chasis']);
        $vehiculo->setMotor($_POST['motor']);
        $vehiculo->setAño($_POST['año']);
        $vehiculo->setKilometraje($_POST['kilometraje']);
        $vehiculo->setModelos_idmodelos($_POST['modelos_idmodelos']);
        $vehiculo->setColores_idcolores($_POST['colores_idcolores']);
        $vehiculo->setTipo_vehiculos_idtipo_vehiculos($_POST['tipo_vehiculos_idtipo_vehiculos']);
        $vehiculo->agregar_vehiculo();
        header('location: ../../index.php?page=listado_vehiculos&mensaje=Vehiculo registrado correctamente.&status=success');
    }

    public function eliminar(){
        $vehiculo = new Vehiculos();
        $vehiculo->setIdvehiculos($_POST['idvehiculos']);
        $vehiculo->eliminar_vehiculo();
        header('location: ../../index.php?page=listado_vehiculos&mensaje=Vehiculo eliminado correctamente.&status=success');
    }

    public function modificar(){
        $vehiculo = new Vehiculos();
        $vehiculo->setIdvehiculos($_POST['idvehiculos']);
        $vehiculo->setPatente($_POST['patente']);
        $vehiculo->setChasis($_POST['chasis']);
        $vehiculo->setMotor($_POST['motor']);
        $vehiculo->setAño($_POST['año']);
        $vehiculo->setKilometraje($_POST['kilometraje']);
        $vehiculo->setModelos_idmodelos($_POST['modelos_idmodelos']);
        $vehiculo->setColores_idcolores($_POST['colores_idcolores']);
        $vehiculo->actualizar_vehiculo();
        header('location: ../../index.php?page=listado_vehiculos&mensaje=Vehiculo Modificado correctamente.&status=success');
    }
    
}