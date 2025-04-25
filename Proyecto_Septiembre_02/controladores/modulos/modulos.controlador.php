<?php

//ini_set('display_errors', 1);
require_once('../../modelos/modulos.php');


if(isset($_POST['action'])){
    if ($_POST['action'] == 'actualizar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->actualizar();
    }
    if ($_POST['action'] == 'guardar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->guardar();
    }

}


class ModuloControlador {

    public function actualizar(){
        $modulo = new Modulo();
        $modulo->setPerfiles_idperfiles($_POST['perfiles_idperfiles']);
        $modulo->setModulos_idmodulos($_POST['modulos_idmodulos']);
        $modulo->actualizar();
        header('location: ../../index.php?page=listado_modulos&idperfiles='.$_POST['perfiles_idperfiles']);
    }

    public function guardar(){
        
    }

}


?>