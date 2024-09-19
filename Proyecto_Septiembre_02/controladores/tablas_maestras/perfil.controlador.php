<?php

ini_set('display_errors', 1);
require_once('../../modelos/perfiles.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $perfil_controlador = new PerfilControlador();
        $perfil_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $perfil_controlador = new PerfilControlador();
        $perfil_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $perfil_controlador = new PerfilControlador();
        $perfil_controlador->eliminar();
    }
}

class PerfilControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_perfiles&mensaje=Todos los datos obligarios.&status=danger');
        }
        $perfil = new Perfil();
        $perfil->setDescripcion($_POST['descripcion']);
        $perfil->guardar();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_perfiles&mensaje=Perfil registrado correctamente.&status=success');
    }

    public function eliminar(){
        $perfil = new Perfil();
        $perfil->setIdperfiles($_POST['idperfiles']);
        $perfil->eliminar_perfil();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_perfiles&mensaje=Perfil eliminado correctamente.&status=success');
    }

    public function modificar(){
        $perfil = new Perfil();
        $perfil->setIdperfiles($_POST['idperfiles']);
        $perfil->setDescripcion($_POST['descripcion']);
        $perfil->actualizar_perfil();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_perfil&idperfiles='.$_POST['idperfiles']);
    }
    
}