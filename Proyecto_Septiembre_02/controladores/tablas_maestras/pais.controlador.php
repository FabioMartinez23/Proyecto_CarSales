<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/pais.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $pais_controlador = new PaisControlador();
        $pais_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $pais_controlador = new PaisControlador();
        $pais_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $pais_controlador = new PaisControlador();
        $pais_controlador->eliminar();
    }
}

class PaisControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_pais&mensaje=Todos los datos obligarios.&status=danger');
        }
        $pais = new Paises();
        $pais->setDescripcion($_POST['descripcion']);
        $pais->agregar_pais();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_pais&mensaje=Pais registrado correctamente.&status=success');
    }

    public function eliminar(){
        $pais = new Paises();
        $pais->setIdpaises($_POST['idpaises']);
        $pais->eliminar_pais();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_pais&mensaje=Pais eliminado correctamente.&status=success');
    }

    public function modificar(){
        $pais = new Paises();
        $pais->setIdpaises($_POST['idpaises']);
        $pais->setDescripcion($_POST['descripcion']);
        $pais->actualizar_pais();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_pais&idpaises='.$_POST['idpaises']);
    }
    
}