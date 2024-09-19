<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_de_puesto.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_puesto_controlador = new Tipo_PuestoControlador();
        $tipo_puesto_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_puesto_controlador = new Tipo_PuestoControlador();
        $tipo_puesto_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_puesto_controlador = new Tipo_PuestoControlador();
        $tipo_puesto_controlador->eliminar();
    }
}

class Tipo_PuestoControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_puesto&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_puesto = new Tipo_De_Puestos();
        $tipo_puesto->setDescripcion($_POST['descripcion']);
        $tipo_puesto->agregar_tipo_puesto();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_puesto&mensaje=Tipo de Puesto registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_puesto = new Tipo_De_Puestos();
        $tipo_puesto->setIdtipo_de_puestos($_POST['idtipo_de_puestos']);
        $tipo_puesto->eliminar_tipo_puesto();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_puesto&mensaje=Tipo de Puesto eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_puesto = new Tipo_De_Puestos();
        $tipo_puesto->setIdtipo_de_puestos($_POST['idtipo_de_puestos']);
        $tipo_puesto->setDescripcion($_POST['descripcion']);
        $tipo_puesto->actualizar_tipo_puesto();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_puesto&idtipo_de_puestos='.$_POST['idtipo_de_puestos']);
    }
    
}
