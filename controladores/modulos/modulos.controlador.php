<?php

//ini_set('display_errors', 1);
require_once('../../modelos/modulos.php');
require_once('../../modelos/perfiles.php');


if(isset($_POST['action'])){
    if ($_POST['action'] == 'actualizar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->actualizar();
    }
    if ($_POST['action'] == 'eliminar'){
        $modulo_controlador = new ModuloControlador();
        $modulo_controlador->eliminar();
    }

}


class ModuloControlador {

    public function actualizar(){
        $modulo = new Modulo();
        $modulo->setPerfiles_idperfiles($_POST['perfiles_idperfiles']);
        $modulo->setModulos_idmodulos($_POST['modulos_idmodulos']);
        $modulo->actualizar();
        header('location: ../../index.php?page=listado_modulos&idperfiles='.$_POST['perfiles_idperfiles'].'&mensaje=Modulos Agregados con Exito.&status=success');
    }

    public function eliminar(){
        if ($_POST['perfil'] == 'Administrador'){
            header('location: ../../index.php?page=listado_modulos&mensaje=No se puede eliminar el Perfil Administrador.&status=warning');
            return;
        }else{
            $perfil = new Perfil();
            $perfil->setIdperfiles($_POST['idperfiles']);
            if($perfil->eliminar_perfil()){
                header('location: ../../index.php?page=listado_modulos&mensaje=Perfil Eliminado.&status=warning');
                exit();
            }
        }
    }

}


?>