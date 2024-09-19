<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/provincia.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $provincia_controlador = new ProvinciasControlador();
        $provincia_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $provincia_controlador = new ProvinciasControlador();
        $provincia_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $provincia_controlador = new ProvinciasControlador();
        $provincia_controlador->eliminar();
    }
}

class ProvinciasControlador{
    public function guardar(){

        if(empty($_POST['descripcion'] || empty($_POST['paises_idpaises'] || empty($_POST['tipo_vehiculos_idtipo_vehiculos'])))){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_provincia&mensaje=Todos los datos obligarios.&status=danger');
        }
        $provincia = new Provincias();
        $provincia->setDescripcion($_POST['descripcion']);
        $provincia->setPaises_idpaises($_POST['paises_idpaises']);
        $provincia->agregar_provincia();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_provincia&mensaje=Provincia registrada correctamente.&status=success');
    }

    public function eliminar(){
        $provincia = new Provincias();
        $provincia->setIdprovincias($_POST['idprovincias']);
        $provincia->eliminar_provincia();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_provinciao&mensaje=Provincia eliminada correctamente.&status=success');
    }

    public function modificar(){
        $provincia = new Provincias();
        $provincia->setIdprovincias($_POST['idprovincias']);
        $provincia->setDescripcion($_POST['descripcion']);
        $provincia->setPaises_idpaises($_POST['paises_idpaises']);
        $provincia->actualizar_provincia();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_provincia&idprovincias='.$_POST['idprovincias']);
    }
    
}