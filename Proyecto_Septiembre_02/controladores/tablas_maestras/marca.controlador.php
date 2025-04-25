<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/marca.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $marca_controlador = new MarcaControlador();
        $marca_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $marca_controlador = new MarcaControlador();
        $marca_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $marca_controlador = new MarcaControlador();
        $marca_controlador->eliminar();
    }
}

class MarcaControlador{
    public function guardar(){

        if(empty($_POST['nombre'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_marca&mensaje=Todos los datos obligarios.&status=danger');
        }
        $marca = new Marcas();
        $marca->setNombre($_POST['nombre']);
        $marca->agregar_marca();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_marca&mensaje=Marca registrada correctamente.&status=success');
    }

    public function eliminar(){
        $marca = new Marcas();
        $marca->setIdmarcas($_POST['idmarcas']);
        $marca->eliminar_marca();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_marca&mensaje=Marca eliminada correctamente.&status=success');
    }

    public function modificar(){
        $marca = new Marcas();
        $marca->setIdmarcas($_POST['idmarcas']);
        $marca->setNombre($_POST['nombre']);
        $marca->actualizar_marca();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_marca&idmarcas='.$_POST['idmarcas']);
    }
    
}