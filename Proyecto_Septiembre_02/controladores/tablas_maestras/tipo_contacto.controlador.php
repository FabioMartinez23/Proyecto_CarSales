<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_contacto.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_contacto_controlador = new Tipo_ContactoControlador();
        $tipo_contacto_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_contacto_controlador = new Tipo_ContactoControlador();
        $tipo_contacto_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_contacto_controlador = new Tipo_ContactoControlador();
        $tipo_contacto_controlador->eliminar();
    }
}

class Tipo_ContactoControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_contacto&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_contacto = new Tipo_Contactos();
        $tipo_contacto->setDescripcion($_POST['descripcion']);
        $tipo_contacto->agregar_tipo_contacto();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_contacto&mensaje=Tipo de Contacto registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_contacto = new Tipo_Contactos();
        $tipo_contacto->setIdtipo_contacto($_POST['idtipo_contacto']);
        $tipo_contacto->eliminar_tipo_contacto();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_contacto&mensaje=Tipo de Contacto eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_contacto = new Tipo_Contactos();
        $tipo_contacto->setIdtipo_contacto($_POST['idtipo_contacto']);
        $tipo_contacto->setDescripcion($_POST['descripcion']);
        $tipo_contacto->actualizar_tipo_contacto();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_contacto&idtipo_contacto='.$_POST['idtipo_contacto']);
    }
    
}
