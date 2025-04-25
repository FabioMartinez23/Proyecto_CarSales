<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/color.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $color_controlador = new ColorControlador();
        $color_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $color_controlador = new ColorControlador();
        $color_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $color_controlador = new ColorControlador();
        $color_controlador->eliminar();
    }
}

class ColorControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_color&mensaje=Todos los datos obligarios.&status=danger');
        }
        $color = new Colores();
        $color->setDescripcion($_POST['descripcion']);
        $color->agregar_color();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_color&mensaje=Color registrado correctamente.&status=success');
    }

    public function eliminar(){
        $color = new Colores();
        $color->setIdcolores($_POST['icoloress']);
        $color->eliminar_color();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_color&mensaje=Color eliminado correctamente.&status=success');
    }

    public function modificar(){
        $color = new Colores();
        $color->setIdcolores($_POST['idcolores']);
        $color->setDescripcion($_POST['descripcion']);
        $color->actualizar_color();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_color&idcolores='.$_POST['idcolores']);
    }
    
}