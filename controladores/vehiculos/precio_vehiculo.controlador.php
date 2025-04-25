<?php

ini_set('display_errors', 1);
require_once('../../modelos/precios_vehiculos.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'agregar'){
        $precio_controlador = new PrecioVehiculoControlador();
        $precio_controlador->agregar();
    }
    if ($_POST['action'] == 'actualizar'){
        $precio_controlador = new PrecioVehiculoControlador();
        $precio_controlador->actualizar();
    }
}

class PrecioVehiculoControlador{
    public function agregar(){
        if(empty($_POST['precio_nuevo'])){
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Campo Vacio.&status=error');
            return;
        }
        $precio = new PrecioVehiculo();
        $precio->setPrecio($_POST['precio_nuevo']);
        $precio->setVehiculos_idvehiculos($_POST['idvehiculos']);
        if($precio->actualizar_precio()){
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Precio Agregado.&status=success');
        }
    }

    public function actualizar(){
        if(empty($_POST['precio_nuevo'])){
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Campo Vacio.&status=error');
            return;
        }
        $precio = new PrecioVehiculo();
        $precio->setPrecio($_POST['precio_nuevo']);
        $precio->setVehiculos_idvehiculos($_POST['idvehiculos']);
        if($precio->actualizar_precio()){
            header('location: ../../index.php?page=listado_vehiculos&mensaje=Precio Agregado.&status=success');
        }
    }
}

?>