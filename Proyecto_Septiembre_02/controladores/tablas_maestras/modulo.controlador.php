<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/modulo.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->eliminar();
    }
}

class ModuloControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_modulo&mensaje=Todos los datos obligarios.&status=danger');
        }
        $modulo = new Modulos();
        $modulo->setDescripcion($_POST['descripcion']);
        $modulo->agregar_modulo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_modulo&mensaje=Modulo registrado correctamente.&status=success');
    }

    public function eliminar(){
        $modulo = new Modulos();
        $modulo->setIdmodulos($_POST['idmodulos']);
        $modulo->eliminar_modulo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_modulo&mensaje=Modulo eliminado correctamente.&status=success');
    }

    public function modificar(){
        $modulo = new Modulos();
        $modulo->setIdmodulos($_POST['idmodulos']);
        $modulo->setDescripcion($_POST['descripcion']);
        $modulo->actualizar_modulo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_modulo&idmodulos='.$_POST['idmodulos']);
    }
    
}
