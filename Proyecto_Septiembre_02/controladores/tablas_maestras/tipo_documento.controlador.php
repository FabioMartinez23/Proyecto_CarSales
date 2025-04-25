<?php

ini_set('display_errors', 1);
require_once('../../modelos/tablas_maestras/tipo_documento.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $tipo_documento_controlador = new Tipo_DocumentoControlador();
        $tipo_documento_controlador->guardar();
    }
    if ($_POST['action'] == 'modificar'){
        $tipo_documento_controlador = new Tipo_DocumentoControlador();
        $tipo_documento_controlador->modificar();
    }
    if ($_POST['action'] == 'eliminar'){
        $tipo_documento_controlador = new Tipo_DocumentoControlador();
        $tipo_documento_controlador->eliminar();
    }
}

class Tipo_DocumentoControlador{
    public function guardar(){

        if(empty($_POST['descripcion'])){
            header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_documento&mensaje=Todos los datos obligarios.&status=danger');
        }
        $tipo_documento = new Tipo_Documentos();
        $tipo_documento->setDescripcion($_POST['descripcion']);
        $tipo_documento->agregar_tipo_documento();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_documento&mensaje=Tipo de Documento registrado correctamente.&status=success');
    }

    public function eliminar(){
        $tipo_documento = new Tipo_Documentos();
        $tipo_documento->setIdTipo_documento($_POST['idTipo_documento']);
        $tipo_documento->eliminar_tipo_documento();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_documento&mensaje=Tipo de Documento eliminado correctamente.&status=success');
    }

    public function modificar(){
        $tipo_documento = new Tipo_Documentos();
        $tipo_documento->setIdTipo_documento($_POST['idTipo_documento']);
        $tipo_documento->setDescripcion($_POST['descripcion']);
        $tipo_documento->actualizar_tipo_documento();
        header('location: ../../vistas/form_tablas_maestras.php?page=form_tipo_documento&idTipo_documento='.$_POST['idTipo_documento']);
    }
    
}
