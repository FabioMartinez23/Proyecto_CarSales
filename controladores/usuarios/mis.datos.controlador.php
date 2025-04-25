<?php

ini_set('display_errors', 1);
require_once('../../modelos/contactos.php');
require_once('../../modelos/domicilio.php');
require_once('../../modelos/documentos.php');

if(isset($_POST['action'])){
    if ($_POST['action'] == 'guardar'){
        $mis_datos_controlador = new MisDatosControlador();
        $mis_datos_controlador->guardar();
    }
    if ($_POST['action'] == 'actualizar'){
        $mis_datos_controlador = new MisDatosControlador();
        $mis_datos_controlador->actualizar();
    }
}

class MisDatosControlador{

    public function guardar(){

        $documento = new Documento();
        // Eliminar los puntos
        $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
        $documento->setValor($documento_sin_puntos);
        $documento->setTipo_documento_idTipo_documento($_POST['idTipo_documento']);
        $documento->setPersonas_idPersonas($_POST['personas_idpersonas']);
        $documento->agregar_documento();

        $contacto = new Contacto();
        $contacto->setValor($_POST['contacto']);
        $contacto->setTipo_contactos_idtipo_contactos($_POST['idtipo_contacto']);
        $contacto->setPersonas_idPersonas($_POST['personas_idpersonas']);
        $contacto->agregar_contato();

        $domicilio = new Domicilios();
        $domicilio->setDescripcion($_POST['descripcion']);
        $domicilio->setPersonas_idPersonas($_POST['personas_idpersonas']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['idtipo_domicilio']);
        $domicilio->agregar_domicilio();
        header('location: ../../index.php?page=form_mis_datos&mensaje=Los datos fueron guardados correctamente.&status=success');
    }

    public function actualizar(){
        $documento = new Documento();
        // Eliminar los puntos
        $documento_sin_puntos = str_replace('.', '', $_POST['documento']);
        $documento->setValor($documento_sin_puntos);
        $documento->setTipo_documento_idTipo_documento($_POST['idTipo_documento']);
        $documento->setIddocumentos($_POST['iddocumentos']);
        if(!$documento->modificar_documento()){
            echo "Error al actualizar el documento";
            exit();
        }

        $contacto = new Contacto();
        $contacto->setValor($_POST['contacto']);
        $contacto->setTipo_contactos_idtipo_contactos($_POST['idtipo_contacto']);
        $contacto->setIdcontactos($_POST['idcontactos']);
        if(!$contacto->modificar_contacto()){
            echo "Error al actualizar el contacto";
            exit();
        }

        $domicilio = new Domicilios();
        $domicilio->setDescripcion($_POST['descripcion']);
        $domicilio->setBarrios_idbarrios($_POST['barrios_idbarrios']);
        $domicilio->setTipo_domicilio_idtipo_domicilio($_POST['idtipo_domicilio']);
        $domicilio->setIddomicilios($_POST['iddomicilios']);
        if(!$domicilio->modificar_domicilio()){
            echo "Error al actualizar el domicilio";
            exit();
        }

        header('location: ../../index.php?page=form_mis_datos&mensaje=Los datos fueron modificados.&status=success');
    }
}

?>