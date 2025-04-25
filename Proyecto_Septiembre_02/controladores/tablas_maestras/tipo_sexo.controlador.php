<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_sexo.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_sexo_controlador = new Tipo_SexoControlador();
        $tipo_sexo_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_sexo_controlador = new Tipo_SexoControlador();
        $tipo_sexo_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_sexo_controlador = new Tipo_SexoControlador();
        $tipo_sexo_controlador->eliminar();
    }
}

class Tipo_SexoControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_sexo&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_sexo = new Tipo_Sexos();
        $tipo_sexo->setDescripcion($_POST['descripcion']);
        $tipo_sexo->agregar_tipo_sexo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_sexo&mensaje=Tipo de Sexo registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_sexo = new Tipo_Sexos();
        $tipo_sexo->setIdtipo_sexo($_POST['idtipo_sexo']);
        $tipo_sexo->eliminar_tipo_sexo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_sexo&mensaje=Tipo de Sexo eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_sexo = new Tipo_Sexos();
        $tipo_sexo->setIdtipo_sexo($_POST['idtipo_sexo']);
        $tipo_sexo->setDescripcion($_POST['descripcion']);
        $tipo_sexo->actualizar_tipo_sexo();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_sexo&idtipo_sexo='.$_POST['idtipo_sexo']);
    }
    
}
